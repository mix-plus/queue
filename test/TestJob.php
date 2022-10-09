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
        if ($this->id == 5) {
            throw new \RuntimeException("id = 5");
        }
        var_dump('message: 123123123 id :' .$this->id);
    }
}