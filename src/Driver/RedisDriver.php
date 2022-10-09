<?php

namespace MixPlus\Queue\Driver;

use MixPlus\Queue\JobInterface;
use MixPlus\Queue\JobMessage;
use MixPlus\Queue\MessageInterface;
use Redis;
use RuntimeException;

class RedisDriver extends Driver
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var ChannelConfig
     */
    protected $channel;

    /**
     * Max polling time.
     * @var int
     */
    protected $timeout;

    /**
     * Retry delay time.
     * @var array|int
     */
    protected $retrySeconds;

    /**
     * Handle timeout.
     * @var int
     */
    protected $handleTimeout;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->initRedis();
        $this->timeout = 5;
        $this->retrySeconds = 10;
        $this->handleTimeout = 10;
        $this->channel = make(ChannelConfig::class, ['channel' => $config['default']]);
    }

    private function initRedis()
    {
        $options = [
            'expire' => 60,
            'default' => 'default',
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' => '',
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];
        if (!empty($this->config)) {
            $this->config = array_merge($options, $this->config);
        }
        if (!extension_loaded('redis')) {
            throw new RuntimeException('redis扩展未安装');
        }
        $func = $this->config['persistent'] ? 'pconnect' : 'connect';
        $this->redis = new Redis();
        $this->redis->{$func}($this->config['host'], $this->config['port'], $this->config['timeout']);

        if ($this->config['password'] != '') {
            $this->redis->auth($this->config['password']);
        }

        if ($this->config['select'] != 0) {
            $this->redis->select($this->config['select']);
        }
    }

    public function push(JobInterface $job, int $delay = 0): bool
    {
        $message = make(JobMessage::class, [$job]);
        $data = $this->packer->pack($message);

        if ($delay === 0) {
            return (bool)$this->redis->lPush($this->channel->getWaiting(), $data);
        }

        return $this->redis->zAdd($this->channel->getDelayed(), [], time() + $delay, $data) > 0;
    }

    public function delete(JobInterface $job): bool
    {
        $message = make(JobMessage::class, [$job]);
        $data = $this->packer->pack($message);

        return (bool)$this->redis->zRem($this->channel->getDelayed(), $data);
    }

    public function pop(): array
    {
        $this->move($this->channel->getDelayed(), $this->channel->getWaiting());
        $this->move($this->channel->getReserved(), $this->channel->getTimeout());

        $res = $this->redis->brPop($this->channel->getWaiting(), $this->timeout);
        if (!isset($res[1])) {
            return [false, null];
        }

        $data = $res[1];
        $message = $this->packer->unpack($data);
        if (!$message) {
            return [false, null];
        }

        $this->redis->zadd($this->channel->getReserved(), time() + $this->handleTimeout, $data);

        return [$data, $message];
    }

    /**
     * Move message to the waiting queue.
     */
    protected function move(string $from, string $to): void
    {
        $now = time();
        $options = ['LIMIT' => [0, 100]];
        if ($expired = $this->redis->zrevrangebyscore($from, (string)$now, '-inf', $options)) {
            foreach ($expired as $job) {
                if ($this->redis->zRem($from, $job) > 0) {
                    $this->redis->lPush($to, $job);
                }
            }
        }
    }

    public function ack($data): bool
    {
        return $this->remove($data);
    }

    protected function remove($data): bool
    {
        return $this->redis->zrem($this->channel->getReserved(), (string)$data) > 0;
    }

    public function fail($data): bool
    {
        if ($this->remove($data)) {
            return (bool)$this->redis->lPush($this->channel->getFailed(), (string)$data);
        }
        return false;
    }

    public function reload(string $queue = null): int
    {
        $channel = $this->channel->getFailed();
        if ($queue) {
            if (!in_array($queue, ['timeout', 'failed'])) {
                throw new RuntimeException(sprintf('Queue %s is not supported.', $queue));
            }

            $channel = $this->channel->get($queue);
        }

        $num = 0;
        while ($this->redis->rpoplpush($channel, $this->channel->getWaiting())) {
            ++$num;
        }
        return $num;
    }

    public function flush(string $queue = null): bool
    {
        $channel = $this->channel->getFailed();
        if ($queue) {
            $channel = $this->channel->get($queue);
        }

        return (bool)$this->redis->del($channel);
    }

    public function info(): array
    {
        return [
            'waiting' => $this->redis->lLen($this->channel->getWaiting()),
            'delayed' => $this->redis->zCard($this->channel->getDelayed()),
            'failed' => $this->redis->lLen($this->channel->getFailed()),
            'timeout' => $this->redis->lLen($this->channel->getTimeout()),
        ];
    }

    protected function retry(MessageInterface $message): bool
    {
        $data = $this->packer->pack($message);

        $delay = time() + $this->getRetrySeconds($message->getAttempts());

        return $this->redis->zAdd($this->channel->getDelayed(), $delay, $data) > 0;
    }

    protected function getRetrySeconds(int $attempts): int
    {
        if (!is_array($this->retrySeconds)) {
            return $this->retrySeconds;
        }

        if (empty($this->retrySeconds)) {
            return 10;
        }

        return $this->retrySeconds[$attempts - 1] ?? end($this->retrySeconds);
    }
}