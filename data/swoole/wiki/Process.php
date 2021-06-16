<?php
use Swoole\Process;
//---------------------------------------
// Swoole\Process
//属性
//	pid					子进程的 PID。
//	pipe 				unixSocket 的文件描述符。
//方法
//	__construct()
//	start()
//	exportSocket()
//	name()
//	exec()
//	close()
//	exit()
//	kill()
//	signal()
//	wait()
//	daemon()
//	alarm()
//	setAffinity()
//---------------------------------------

/*for ($n = 1; $n <= 3; $n++) {
	$process = new Process(function () use ($n) {
		echo 'Child #' . getmypid() . " start and sleep {$n}s" . PHP_EOL;
		sleep($n);
		echo 'Child #' . getmypid() . ' exit' . PHP_EOL;
	});
	$process->start();
}
for ($n = 3; $n--;) {
		$status = Process::wait(true);
	echo "Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
}
echo 'Parent #' . getmypid() . ' exit' . PHP_EOL;*/


/*$redis = new Redis;
$redis->connect('127.0.0.1', 6379);

function callback_function() {
	swoole_timer_after(1000, function () {
		echo "hello world";
	});
	global $redis;//同一个连接
};

swoole_timer_tick(1000, function () {
	echo "parent timer\n";
});//不会继承

Swoole\Process::signal(SIGCHLD, function ($sig) {
	while ($ret = Swoole\Process::wait(false)) {
		// create a new child process
		$p = new Swoole\Process('callback_function');
		$p->start();
	}
});

// create a new child process
$p = new Swoole\Process('callback_function');

$p->start();*/


/*$proc1 = new Swoole\Process(function (swoole_process $proc) {
	$socket = $proc->exportSocket();
	echo $socket->recv();
	$socket->send("hello master\n");
	echo "proc1 stop\n";
}, false, 1, true);

$proc1->start();

//父进程创建一个协程容器
Co\run(function() use ($proc1) {
	$socket = $proc1->exportSocket();
	$socket->send("hello pro1\n");
	var_dump($socket->recv());
});
Swoole\Process::wait(true);*/



$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
	echo "Worker#{$workerId} is started\n";
	$redis = new Redis();
	$redis->pconnect('127.0.0.1', 6379);
	$key = "key1";
	while (true) {
		$msg = $redis->brpop($key, 2);
		if ( $msg == null) continue;
		var_dump($msg);
	}
});

$pool->on("WorkerStop", function ($pool, $workerId) {
	echo "Worker#{$workerId} is stopped\n";
});

$pool->start();
