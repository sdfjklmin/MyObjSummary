<?php
// HTTP Co
Co\run(function () {
	$server = new Co\Http\Server("127.0.0.1", 9502, false);
	$server->handle('/', function ($request, $response) {
		$response->end("<h1>Index</h1>");
	});
	$server->handle('/test', function ($request, $response) {
		$response->end("<h1>Test</h1>");
	});
	$server->handle('/stop', function ($request, $response) use ($server) {
		$response->end("<h1>Stop</h1>");
		$server->shutdown();
	});
	$server->start();
});
echo 1;//得不到执行


//添加 2 个协程并发的做一些事情
Co\run(function () {
	go(function() {
		var_dump(file_get_contents("http://www.xinhuanet.com/"));
	});

	go(function() {
		Co::sleep(1);
		echo "done\n";
	});
});
echo 1;//可以得到执行

//调度器
$scheduler = new Swoole\Coroutine\Scheduler;
$scheduler->add(function ($a, $b) {
	Co::sleep(1);
	assert($a == 'hello');
	assert($b == 12345);
	echo "Done.\n";
}, "hello", 12345);

$scheduler->start();


