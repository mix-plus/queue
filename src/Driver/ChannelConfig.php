<?php

namespace MixPlus\Queue\Driver;

use RuntimeException;

class ChannelConfig
{
    /**
     * @var string
     */
    protected $channel;

    /**
     * Key for waiting message.
     * @var string
     */
    protected $waiting;

    /**
     * Key for reserved message.
     * @var string
     */
    protected $reserved;

    /**
     * Key for reserve timeout message.
     * @var string
     */
    protected $timeout;

    /**
     * Key for delayed message.
     * @var string
     */
    protected $delayed;

    /**
     * Key for failed message.
     * @var string
     */
    protected $failed;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
        $this->waiting = "{$channel}:waiting";
        $this->reserved = "{$channel}:reserved";
        $this->delayed = "{$channel}:delayed";
        $this->failed = "{$channel}:failed";
        $this->timeout = "{$channel}:timeout";
    }

    public function get(string $queue)
    {
        if (isset($this->{$queue}) && is_string($this->{$queue})) {
            return $this->{$queue};
        }

        throw new RuntimeException(sprintf('Queue %s is not exist.', $queue));
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function getWaiting(): string
    {
        return $this->waiting;
    }

    public function setWaiting(string $waiting)
    {
        $this->waiting = $waiting;
        return $this;
    }

    public function getReserved(): string
    {
        return $this->reserved;
    }

    public function setReserved(string $reserved)
    {
        $this->reserved = $reserved;
        return $this;
    }

    public function getTimeout(): string
    {
        return $this->timeout;
    }

    public function setTimeout(string $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getDelayed(): string
    {
        return $this->delayed;
    }

    public function setDelayed(string $delayed)
    {
        $this->delayed = $delayed;
        return $this;
    }

    public function getFailed(): string
    {
        return $this->failed;
    }

    public function setFailed(string $failed)
    {
        $this->failed = $failed;
        return $this;
    }
}