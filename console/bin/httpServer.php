#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

$server = new Swoole\HTTP\Server("0.0.0.0", 9501);

$server->set([
    'worker_num' => 2,
    'daemonize' => false,
    'max_request' => 10000,
    'dispatch_mode' => 2,
    'debug_mode'=> 1,
    'log_level' => 0,
    'enable_coroutine' => true,
    'open_http_protocol' => true,
    'open_http2_protocol' => true,
    'log_file' => '/var/log/supervisor/swoole/swoole_http_server.log',
]);

$server->on("start", function (Swoole\Http\Server $server) {
    echo "OpenSwoole http server is started at http://127.0.0.1:9501" . PHP_EOL;
    echo PHP_EOL;
});

$server->on("request", function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");
});

$server->start();