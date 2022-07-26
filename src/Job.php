<?php

namespace MixPlus\Queue;

use MixPlus\Queue\Contract\CompressInterface;
use MixPlus\Queue\Contract\UnCompressInterface;

abstract class Job implements JobInterface
{
    protected int $maxAttempts = 0;

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function uncompress(): static
    {
        foreach ($this as $key => $value) {
            if ($value instanceof UnCompressInterface) {
                $this->{$key} = $value->uncompress();
            }
        }

        return $this;
    }

    public function compress(): static
    {
        foreach ($this as $key => $value) {
            if ($value instanceof CompressInterface) {
                $this->{$key} = $value->compress();
            }
        }

        return $this;
    }
}