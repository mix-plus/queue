<?php

namespace MixPlus\Queue\Driver;

use MixPlus\Event\EventDispatcher;
use MixPlus\Event\ListenerProviderFactory;
use MixPlus\Queue\Event\AfterHandle;
use MixPlus\Queue\Event\BeforeHandle;
use MixPlus\Queue\Event\FailedHandle;
use MixPlus\Queue\Event\QueueLength;
use MixPlus\Queue\Event\RetryHandle;
use MixPlus\Queue\Listener\QueueHandleListener;
use MixPlus\Queue\Listener\QueueLengthListener;
use MixPlus\Queue\MessageInterface;
use MixPlus\Queue\Util\Logger;
use MixPlus\Queue\Util\Packer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

abstract class Driver implements DriverInterface
{
    /**
     * @var Packer
     */
    protected $packer;

    /**
     * @var EventDispatcherInterface
     */
    protected $event;

    /**
     * @var int
     */
    protected $lengthCheckCount = 500;

    /**
     * @var array
     */
    protected $config;

    protected $logger;

    public function __construct($config = [])
    {
        $this->packer = new Packer();
        $this->config = $config;
        $this->logger = Logger::instance();
        $provider = new ListenerProviderFactory;
        $listeners = $provider([
            QueueHandleListener::class,
            QueueLengthListener::class,
        ]);
        $this->event = new EventDispatcher($listeners, $this->logger);
    }

    public function consume(): void
    {
        $messageCount = 0;
        $maxMessages = 0;
        while (true) {
            try {
                [$data, $message] = $this->pop();
                if ($data === false) {
                    continue;
                }
                $callback = $this->getCallback($data, $message);

                call($callback);

                if ($messageCount % $this->lengthCheckCount === 0) {
                    $this->checkQueueLength();
                }
                if ($maxMessages > 0 && $messageCount >= $maxMessages) {
                    break;
                }
            } catch (Throwable $e) {
                $this->logger->error((string)$e);
            } finally {
                ++$messageCount;
            }
        }
    }

    protected function getCallback($data, $message): callable
    {
        return function () use ($data, $message) {
            try {
                if ($message instanceof MessageInterface) {
                    $this->event && $this->event->dispatch(new BeforeHandle($message));
                    $message->job()->handle();
                    $this->event && $this->event->dispatch(new AfterHandle($message));
                }

                $this->ack($data);
            } catch (Throwable $ex) {
                if (isset($message, $data)) {
                    if ($message->attempts() && $this->remove($data)) {
                        $this->event && $this->event->dispatch(new RetryHandle($message, $ex));
                        $this->retry($message);
                    } else {
                        $this->event && $this->event->dispatch(new FailedHandle($message, $ex));
                        $this->fail($data);
                    }
                }
            }
        };
    }

    /**
     * Remove data from reserved queue.
     */
    abstract protected function remove($data): bool;

    /**
     * Handle a job again some seconds later.
     */
    abstract protected function retry(MessageInterface $message): bool;

    protected function checkQueueLength(): void
    {
        $info = $this->info();
        foreach ($info as $key => $value) {
            $this->event && $this->event->dispatch(new QueueLength($this, $key, $value));
        }
    }
}