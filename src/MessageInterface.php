<?php

namespace MixPlus\Queue;

interface MessageInterface
{
    public function job(): JobInterface;

    /**
     * Whether the queue can be handle again.
     */
    public function attempts(): bool;

    /**
     * The current attempt count.
     */
    public function getAttempts(): int;
}