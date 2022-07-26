<?php

namespace MixPlus\Queue\Util;

class Packer implements PackerInterface
{
    public function pack($data)
    {
        return serialize($data);
    }

    public function unpack($data)
    {
        return unserialize($data);
    }
}