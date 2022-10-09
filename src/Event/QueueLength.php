<?php

namespace MixPlus\Queue\Event;

use MixPlus\Queue\Driver\DriverInterface;

class QueueLength
{
    /**
     * @var DriverInterface
     */
    public $driver;

    /**
     * @var string
     */
    public $key;

    /**
     * @var int
     */
    public $length;

    public function __construct(DriverInterface $driver, string $key, int $length)
    {
        $this->driver = $driver;
        $this->key = $key;
        $this->length = $length;
    }
}