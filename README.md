# EasySwoole-Template-Think

## 实现原理
注册N个自定义进程做渲染进程，进程内关闭协程环境，并监听UNIXSOCK，客户端调用携程的client客户端发送数据给进程渲染，进程再返回结果给客户端，用来解决PHP模板引擎协程安全问题。

## 测试用例使用教程

```php
<?php

include_once './vendor/autoload.php';

// 注册swoole http服务
$http = new \swoole_http_server('127.0.0.1', 9501);

// 加载配置参数类
$config = new \EasySwoole\Template\Think\Config();

// 设置必要的模板目录和缓存目录（其他配置参考类中属性）
$config->setViewPath(__DIR__);
$config->setCachePath(__DIR__);

// 初始化渲染进程
$render = \EasySwoole\Template\Think\Render::getInstance()->init($config, [], $http);
$http->on('request', static function(\Swoole\Http\Request $request, \Swoole\Http\Response $response)use($render){
    // 当发生http请求时，调用渲染方法
    $response->end($render->view('think', ['engine'=>'think2']));
});
echo 'server start at http://127.0.0.1:9501'.PHP_EOL;
$http->start();
```

### 启动测试服务器

代码包内置了一个小型的测试服务器，只需要运行目录下的test.php即可开启测试


## 框架使用教程

```php

```

```bash
php test.php
```