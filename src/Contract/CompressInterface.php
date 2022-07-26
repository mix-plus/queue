<?php

namespace MixPlus\Queue\Contract;

interface CompressInterface
{
    public function compress(): UnCompressInterface;
}