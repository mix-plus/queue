<?php

namespace MixPlus\Queue;

use MixPlus\Queue\Contract\CompressInterface;
use MixPlus\Queue\Contract\UnCompressInterface;
use Serializable;

class Message implements MessageInterface, Serializable
{
    /**
     * @var JobInterface
     */
    protected $job;
    /**
     * @var int
     */
    protected $attempts = 0;


    public function __serialize(): array
    {
        if ($this->job instanceof CompressInterface) {
            /* @phpstan-ignore-next-line */
            $this->job = $this->job->compress();
        }

        return [$this->job, $this->attempts];
    }

    public function __unserialize(array $data): void
    {
        [$job, $attempts] = $data;
        if ($job instanceof UnCompressInterface) {
            $job = $job->uncompress();
        }

        $this->job = $job;
        $this->attempts = $attempts;
    }

    public function job(): JobInterface
    {
        return $this->job;
    }

    public function attempts(): bool
    {
        if ($this->job->getMaxAttempts() > $this->attempts++) {
            return true;
        }
        return false;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function serialize()
    {
        if ($this->job instanceof CompressInterface) {
            /* @phpstan-ignore-next-line */
            $this->job = $this->job->compress();
        }

        return serialize([$this->job, $this->attempts]);
    }

    public function unserialize($serialized)
    {
        [$job, $attempts] = unserialize($serialized);
        if ($job instanceof UnCompressInterface) {
            $job = $job->uncompress();
        }

        $this->job = $job;
        $this->attempts = $attempts;
    }
}