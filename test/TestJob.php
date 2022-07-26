<?php

namespace MixPlus\Queue\Test;

use MixPlus\Queue\Job;

class TestJob extends Job
{

    public function handle()
    {
        var_dump('123123123');
    }
}