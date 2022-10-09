<?php


namespace MixPlus\Queue\Listener;

use MixPlus\Event\Contract\ListenerInterface;
use MixPlus\Queue\Event\AfterHandle;
use MixPlus\Queue\Event\BeforeHandle;
use MixPlus\Queue\Event\Event;
use MixPlus\Queue\Event\FailedHandle;
use MixPlus\Queue\Event\RetryHandle;
use MixPlus\Queue\Util\Logger;
use Psr\Log\LoggerInterface;

class QueueHandleListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = Logger::instance();
    }

    public function listen(): array
    {
        return [
            AfterHandle::class,
            BeforeHandle::class,
            FailedHandle::class,
            RetryHandle::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof Event && $event->message->job()) {
            $this->logger->debug("join queue handle process");
            $job = $event->message->job();
            $jobClass = get_class($job);
            $date = date('Y-m-d H:i:s');

            switch (true) {
                case $event instanceof BeforeHandle:
                    $this->logger->info(sprintf('[%s] Processing %s.', $date, $jobClass));
                    break;
                case $event instanceof AfterHandle:
                    $this->logger->info(sprintf('[%s] Processed %s.', $date, $jobClass));
                    break;
                case $event instanceof FailedHandle:
                    $this->logger->error(sprintf('[%s] Failed %s.', $date, $jobClass));
                    $this->logger->error((string) $event->getThrowable());
                    break;
                case $event instanceof RetryHandle:
                    $this->logger->warning(sprintf('[%s] Retried %s.', $date, $jobClass));
                    break;
            }
        }
    }
}
