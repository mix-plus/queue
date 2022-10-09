<?php

namespace MixPlus\Queue\Event;

use MixPlus\Queue\MessageInterface;

class Event
{
    /**
     * @var MessageInterface
     */
    public $message;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
}