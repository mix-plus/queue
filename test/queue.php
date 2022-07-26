<?php

use MixPlus\Queue\Driver\RedisDriver;
use MixPlus\Queue\Test\TestJob;


require dirname(__DIR__) . '/./vendor/autoload.php';


$queue = (new RedisDriver());

$i = 10;
$r = [];
while ($i--) {
    $r[] = $queue->push(new TestJob(), $i);
}

var_dump($r);

go(function () use ($queue) {
    $queue->consume();
});





