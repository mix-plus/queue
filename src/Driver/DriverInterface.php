<?php

namespace MixPlus\Queue\Driver;

use MixPlus\Queue\JobInterface;

interface DriverInterface
{
    /**
     * Push a job to queue.
     */
    public function push(JobInterface $job, int $delay = 0): bool;

    /**
     * Delete a delay job to queue.
     */
    public function delete(JobInterface $job): bool;

    /**
     * Pop a job from queue.
     */
    public function pop(): array;

    /**
     * Ack a job.
     */
    public function ack($data): bool;

    /**
     * Push a job to failed queue.
     */
    public function fail($data): bool;

    /**
     * Consume jobs from a queue.
     */
    public function consume(): void;

    /**
     * Reload failed message into waiting queue.
     */
    public function reload(string $queue = null): int;

    /**
     * Delete all failed message from failed queue.
     */
    public function flush(string $queue = null): bool;

    /**
     * Return info for current queue.
     */
    public function info(): array;
}