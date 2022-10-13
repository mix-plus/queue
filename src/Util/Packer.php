<?php

namespace MixPlus\Queue\Util;

class Packer implements PackerInterface
{
    public function pack($data)
    {
        try {
            return serialize($data);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function unpack($data)
    {
        try {
            return unserialize($data);
        } catch (\Throwable $e) {
            return null;
        }
    }
}