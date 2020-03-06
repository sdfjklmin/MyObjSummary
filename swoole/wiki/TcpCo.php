<?php
//多进程管理模块,workerStart必要事件
$pool = new Swoole\Process\Pool(2);

//让每个OnWorkerStart回调都自动创建一个协程,每运行一次就会有一个服务
$pool->set(['enable_coroutine' => true]);
$pool->on("workerStart", function ($pool, $id) {
	//每个进程都监听9501端口
	$server = new Swoole\Coroutine\Server('127.0.0.1', '9501' , false, true);
	//收到15信号关闭服务,示例 : Swoole\Process::kill(22661,15);#pid,信号
	//这里有可能执行多次
	Swoole\Process::signal(SIGTERM, function () use ($server) {
		echo "stop signal \n";
		$server->shutdown();
	});
	//接收到新的连接请求
	$server->handle(function (Swoole\Coroutine\Server\Connection $conn) {
		//接收数据
		$data = $conn->recv();
		echo "data into \n";
		if (empty($data)) {
			echo "data into and close \n";
			//关闭连接
			$conn->close();
		}
		//发送数据
		echo "data into and send \n";
		$conn->send("hello");
	});
	//开始监听端口
	$server->start();
});
$pool->start();