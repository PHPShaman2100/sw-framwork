#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use SW\Kernel;

if (!defined('ENVAROMENT')) {
    define('ENVAROMENT', 'dev');
}

if (!isset($host)) {
    $host = [
        'host' => '0.0.0.0',
        'port' => 9501
    ];
}
if (!isset($config)) {
    $config = [
        'worker_num' => 1,
        'daemonize' => false,
        'max_request' => 10000,
        'dispatch_mode' => 2,
        'debug_mode'=> 0,
        'log_level' => 3,
        'enable_coroutine' => true,
        'open_http_protocol' => true,
        'open_http2_protocol' => true,
        'log_file' => '/var/log/supervisor/swoole/swoole_http_server.log',
    ];
}

$app = new Kernel();

$server = new Swoole\HTTP\Server(host: $host['host'], port: $host['port']);

$server->set($config);

$server->on('start', function (Swoole\Http\Server $server) use ($host, $config, $app) {
    echo 'Swoole http server is started at http://' . $host['host'] . ':' . $host['port'];
    echo PHP_EOL;

//    $routes = $app->getContainerBuilder()->getParameter('route');
//    var_dump($routes);
//    echo PHP_EOL;
});

$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($app) {
    $app->action($request, $response)->end();
});

$server->start();
