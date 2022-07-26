<?php

namespace MixPlus\Queue;

interface JobInterface
{
    /**
     * Handle the job.
     */
    public function handle();

    public function getMaxAttempts(): int;
}