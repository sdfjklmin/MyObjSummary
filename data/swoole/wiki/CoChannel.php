<?php
/*demo*/
go(function(){
	//对象别名: new chan();
	$chan = new Swoole\Coroutine\Channel(2);
	//属性
	var_dump($chan->capacity);//对应构造函数的设置
	var_dump($chan->errCode);
	//方法
	Swoole\Coroutine::create(function () use ($chan) {
		for($i = 0; $i < 3; $i++) {
			co::sleep(0.5);
			$chan->push(['rand' => rand(1000, 9999), 'index' => $i]);
			echo "$i\n";
		}
	});
	Swoole\Coroutine::create(function () use ($chan) {
		while(1) {
			$data = $chan->pop();
			//获取通道中的元素数量。
			var_dump($chan->length());
			var_dump($chan->isEmpty());
			var_dump($chan->isFull());
		}
	});
});

//--------------------------------------------------------------------------
// push 向通道中写入数据
// Swoole\Coroutine\Channel->push(mixed $data, float $timeout = -1): bool;
//
// pop  从通道中读取数据
// Swoole\Coroutine\Channel->pop(float $timeout = -1): mixed;
//
// close 关闭通道。并唤醒所有等待读写的协程。
// Swoole\Coroutine\Channel->close(): bool;
//
// length 获取通道中的元素数量。
// Swoole\Coroutine\Channel->length(): int;
//--------------------------------------------------------------------------
/*go(function (){
	$channel = new Swoole\Coroutine\Channel(1);
	go(function ()use ($channel){
		$channel->push(rand().'-tt');
	});
	$channel->close();
	go(function ()use ($channel){
		while (true) {
			$channel->pop();
		}
	});
});*/

