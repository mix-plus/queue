<?php

namespace MixPlus\Queue\Driver;

use MixPlus\Queue\MessageInterface;
use MixPlus\Queue\Util\Packer;
use Throwable;

abstract class Driver implements DriverInterface
{
    protected Packer $packer;

    protected int $lengthCheckCount = 500;

    public function __construct()
    {
        $this->packer = new Packer();
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

                do_parallel([$callback]);

                if ($messageCount % $this->lengthCheckCount === 0) {
                    $this->checkQueueLength();
                }
                if ($maxMessages > 0 && $messageCount >= $maxMessages) {
                    break;
                }
            } catch (Throwable $e) {
                var_dump((string)$e);
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
//                    $this->event && $this->event->dispatch(new BeforeHandle($message));
                    $message->job()->handle();
//                    $this->event && $this->event->dispatch(new AfterHandle($message));
                }

                $this->ack($data);
            } catch (Throwable $ex) {
                var_dump((string)$ex);
                if (isset($message, $data)) {
                    if ($message->attempts() && $this->remove($data)) {
//                        $this->event && $this->event->dispatch(new RetryHandle($message, $ex));
                        var_dump('retry');
                        $this->retry($message);
                    } else {
//                        $this->event && $this->event->dispatch(new FailedHandle($message, $ex));
                        var_dump('fail');
                        $this->fail($data);
                    }
                }
            }
        };
    }

    /**
     * Remove data from reserved queue.
     */
    abstract protected function remove(mixed $data): bool;

    /**
     * Handle a job again some seconds later.
     */
    abstract protected function retry(MessageInterface $message): bool;

    protected function checkQueueLength(): void
    {
        $info = $this->info();
        foreach ($info as $key => $value) {
            var_dump('dispatch');
//            $this->event && $this->event->dispatch(new QueueLength($this, $key, $value));
        }
    }
}