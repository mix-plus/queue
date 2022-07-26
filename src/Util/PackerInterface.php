<?php

namespace MixPlus\Queue\Util;

interface PackerInterface
{
    public function pack($data);

    public function unpack($data);
}