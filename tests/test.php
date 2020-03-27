<?php

include_once '../vendor/autoload.php';

$http = new \swoole_http_server('127.0.0.1', 9501);
// Socket 设置
$config = new \EasySwoole\Template\Think\Config();
// 加载Socket设置， think-template配置， 设置SwooleHttp对象
$render = \EasySwoole\Template\Think\Render::getInstance()->init($config, [
    'view_path'	=>	'./views/',
    'cache_path'	=>	'./views/cache/',
    'view_suffix'   =>	'html'
], $http);
$http->on('request', static function(\Swoole\Http\Request $request, \Swoole\Http\Response $response)use($render){
    $response->end($render->view('test/think', ['engine'=>'think2']));
});
echo 'server start at http://127.0.0.1:9501'.PHP_EOL;
$http->start();
