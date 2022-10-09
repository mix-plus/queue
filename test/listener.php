<?php
require dirname(__DIR__) . '/./vendor/autoload.php';

new \MixPlus\Queue\Listener\QueueHandleListener();
new \MixPlus\Queue\Listener\QueueLengthListener();
