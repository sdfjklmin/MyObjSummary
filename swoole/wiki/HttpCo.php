<?php
\Swoole\Coroutine\Run(function () {
	$server = new \Co\Http\Server("127.0.0.1", 9502, false);
	$server->handle('/', function ($request,\Swoole\Http\Response $response) {
		$response->end("<h1>Index</h1>");
	});
	$server->handle('/test', function ($request,\Swoole\Http\Response $response) {
		$response->end("<h1>Test</h1>");
	});
	$server->handle('/stop', function ($request,\Swoole\Http\Response $response) use ($server) {
		$response->end("<h1>Stop</h1>");
		$server->shutdown();
	});
	$server->start();
});
