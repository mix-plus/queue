<?php

namespace MixPlus\Queue\Test;


use MixPlus\Queue\Job;

class TestJob extends Job
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
//        var_dump('延迟2s');
//        \Swoole\Coroutine::sleep(2);
//        sleep(2);
//        if ($this->id == 5) {
//            throw new \RuntimeException("id = 5");
//        }
        var_dump('message: 123123123 id :' .$this->id);
    }
}