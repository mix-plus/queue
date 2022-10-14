<?php

use MixPlus\Queue\Driver\RedisDriver;
use MixPlus\Queue\Test\TestJob;

require dirname(__DIR__) . '/./vendor/autoload.php';

$queue = (new RedisDriver([
    'default' => 'default',
    'host' => '127.0.0.1',
    'port' => '6379',
    'password' => '',
    'select' => 0,
    'timeout' => 0,
]));

$i = 0;
while (true) {
    $i++;
    $queue->push(new TestJob($i), 10);
}



