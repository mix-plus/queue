<?php

use MixPlus\Queue\Driver\RedisDriver;
use MixPlus\Queue\Test\TestJob;
use MixPlus\Queue\Util\Logger;


require dirname(__DIR__) . '/./vendor/autoload.php';

$queue = (new RedisDriver([
    'default' => 'default',
    'host' => '127.0.0.1',
    'port' => '6379',
    'password' => '',
    'select' => 0,
    'timeout' => 0,
]));

//$i = 10;
//$r = [];
//while ($i--) {
//    $r[] = $queue->push(new TestJob($i), $i);
//    Logger::instance()->info($i);
//}

$queue->consume();
//go(function () use($queue) {
//    $queue->consume();
//});





