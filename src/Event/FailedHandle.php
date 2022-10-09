<?php

namespace MixPlus\Queue\Event;

use MixPlus\Queue\MessageInterface;
use Throwable;

class FailedHandle extends Event
{
    /**
     * @var Throwable
     */
    protected $throwable;

    public function __construct(MessageInterface $message, Throwable $throwable)
    {
        parent::__construct($message);
        $this->throwable = $throwable;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}