<?php

try {
    include_once './vendor/autoload.php';

    $http = new \swoole_http_server('127.0.0.1', 9501);

    $config = new \EasySwoole\Template\Think\Config();
    $config->setViewPath(__DIR__);
    $config->setCachePath(__DIR__);
    $render = \EasySwoole\Template\Think\Render::getInstance()->init($config, [], $http);
    $http->on('request', static function(\Swoole\Http\Request $request, \Swoole\Http\Response $response)use($render){
        $response->end($render->view('think', ['engine'=>'think2']));
    });
    echo 'server start at http://127.0.0.1:9501'.PHP_EOL;
    $http->start();
}catch (\Throwable $e){
    var_dump($e->getMessage());
}
