<?php

namespace MixPlus\Queue\Driver;

use MixPlus\Queue\JobInterface;
use MixPlus\Queue\MessageInterface;

class ChannelDriver extends Driver
{

    public function push(JobInterface $job, int $delay = 0): bool
    {
        // TODO: Implement push() method.
    }

    public function delete(JobInterface $job): bool
    {
        // TODO: Implement delete() method.
    }

    public function pop(): array
    {
        // TODO: Implement pop() method.
    }

    public function ack($data): bool
    {
        // TODO: Implement ack() method.
    }

    public function fail($data): bool
    {
        // TODO: Implement fail() method.
    }

    public function reload(string $queue = null): int
    {
        // TODO: Implement reload() method.
    }

    public function flush(string $queue = null): bool
    {
        // TODO: Implement flush() method.
    }

    public function info(): array
    {
        // TODO: Implement info() method.
    }

    protected function remove($data): bool
    {
        // TODO: Implement remove() method.
    }

    protected function retry(MessageInterface $message): bool
    {
        // TODO: Implement retry() method.
    }
}