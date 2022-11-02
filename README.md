# queue
Generic resident memory queue
Swoole support

# install
```
composer require mix-plus/queue
```
# run
```php
// create queue
$queue = new RedisDriver([
                'default' => 'default',
                'host' => '127.0.0.1',
                'port' => 6379,
                'password' => '',
                'select' => 0,
                'timeout' => 0,
            ], [
                QueueHandleListener::class,
                QueueLengthListener::class,
                ReloadChannelListener::class,
            ]);

// start consume
// Swoole is coroutine run  go $queue->consume();
$queue->consume();
```
# test
> [code](test/queue.php)

`php test/queue.php`
```
array(10) {
  [0]=>
  bool(false)
  [1]=>
  bool(false)
  [2]=>
  bool(false)
  [3]=>
  bool(false)
  [4]=>
  bool(false)
  [5]=>
  bool(false)
  [6]=>
  bool(false)
  [7]=>
  bool(false)
  [8]=>
  bool(false)
  [9]=>
  bool(true)
}
string(9) "123123123"
string(8) "dispatch"
string(8) "dispatch"
string(8) "dispatch"
string(8) "dispatch"
```

# LICENSE
Apache License Version 2.0, http://www.apache.org/licenses/
