# EasySwoole-Template-Think

## 扩展安装
```bash
composer require yehua/easyswoole-think
```

## 实现原理
注册N个自定义进程做渲染进程，进程内关闭协程环境，并监听UNIXSOCK，客户端调用携程的client客户端发送数据给进程渲染，进程再返回结果给客户端，用来解决PHP模板引擎协程安全问题。

## 测试用例使用教程

```php
<?php
include_once '../vendor/autoload.php';

$http = new \swoole_http_server('127.0.0.1', 9501);
// Socket 设置
$config = new \EasySwoole\Template\Think\Config();
// 加载Socket设置， think-template配置(https://github.com/top-think/think-template)， 设置SwooleHttp对象
$render = \EasySwoole\Template\Think\Render::getInstance()->init($config, [
    'view_path'	=>	'./views/',
    'cache_path'	=>	'./views/cache/',
    'view_suffix'   =>	'html'
], $http);
$http->on('request', static function(\Swoole\Http\Request $request, \Swoole\Http\Response $response)use($render){
    $response->end($render->view(
        // 模板路径
        'test/think',
        // 渲染参数
        ['engine'=>'think2'],
        // 当次渲染配置(只作用于当次渲染)
        ['view_suffix'=>'htm']
        ));
});
echo 'server start at http://127.0.0.1:9501'.PHP_EOL;
$http->start();
```

### 启动测试服务器

代码包内置了一个小型的测试服务器，只需要运行目录下的test.php即可开启测试
```bash
php test.php
```

## EasySwoole框架使用教程

```php
// 框架 EasySwooleEvent.php 中 mianServerCreate 方法中加入以下配置
public static function mainServerCreate(EventRegister $register)
{
    // 加载配置参数类
    $config = new \EasySwoole\Template\Think\Config();
    // 设置全局socket基础配置（不可修改）和 think-template全局配置(think配置见https://github.com/top-think/think-template)
    \EasySwoole\Template\Think\Render::getInstance()->init($config, [
        'view_path'	=>	EASYSWOOLE_ROOT.'/Template/',
        'cache_path'	=>	EASYSWOOLE_ROOT.'/Template/Cache/',
        'view_suffix'   =>	'html'
    ]);
}

// 控制器中使用
public function index()
{
    // 可添加参数三 用于设置当次渲染配置并且只作用于当次（默认为空）
    $renderStr = \EasySwoole\Template\Think\Render::getInstance()->view('Index/index',['time'=>time()]);
    $this->response()->write($renderStr);
}
```