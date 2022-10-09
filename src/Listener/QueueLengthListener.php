<?php


namespace MixPlus\Queue\Listener;

use MixPlus\Event\Contract\ListenerInterface;
use MixPlus\Queue\Event\QueueLength;
use MixPlus\Queue\Util\Logger;
use Psr\Log\LoggerInterface;

class QueueLengthListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $level = [
        'debug' => 10,
        'info' => 50,
        'warning' => 500,
    ];

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
        $value = 0;
        foreach ($this->level as $level => $value) {
            if ($event->length < $value) {
                $message = sprintf('Queue lengh of %s is %d.', $event->key, $event->length);
                $this->logger->{$level}($message);
                break;
            }
        }

        if ($event->length >= $value) {
            $this->logger->error(sprintf('Queue lengh of %s is %d.', $event->key, $event->length));
        }
    }
}
