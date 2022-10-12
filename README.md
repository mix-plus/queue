# 队列
通用常驻内存队列支持 Swoole 以及 Swoole
# 安装
```
composer require mix-plus/queue
```
# 运行
```php
// 创建实例
$queue = new RedisDriver([
                'expire' => 60,
                'default' => 'default',
                'host' => '127.0.0.1',
                'port' => 6379,
                'password' => '',
                'select' => 0,
                'timeout' => 0,
                'persistent' => false,
            ], [
                QueueHandleListener::class,
                QueueLengthListener::class,
                ReloadChannelListener::class,
            ]);

// 启动消费者
// Swoole 环境可以协程启动 go $queue->consume();
$queue->consume();
```
# 测试
> [测试代码](test/queue.php)

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