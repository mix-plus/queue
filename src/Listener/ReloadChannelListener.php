<?php


namespace MixPlus\Queue\Listener;

use MixPlus\Event\Contract\ListenerInterface;
use MixPlus\Queue\Event\QueueLength;
use MixPlus\Queue\Util\Logger;

class ReloadChannelListener implements ListenerInterface
{
    /**
     * @var string[]
     */
    protected $channels = [
        'timeout',
    ];

    protected $logger;

    public function __construct()
    {
        $this->logger = Logger::instance();
    }


    public function listen(): array
    {
        return [
            QueueLength::class,
        ];
    }

    /**
     * @param object $event
     */
    public function process(object $event)
    {
        if (!$event instanceof QueueLength) {
            return;
        }

        if (!in_array($event->key, $this->channels)) {
            return;
        }

        if ($event->length == 0) {
            return;
        }

        $event->driver->reload($event->key);

        $this->logger->info(sprintf('%s channel reload %d messages to waiting channel success.', $event->key, $event->length));
    }
}
