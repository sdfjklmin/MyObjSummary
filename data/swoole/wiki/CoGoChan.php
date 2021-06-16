<?php

/*在onRequest 中需要并发两个 http 请求，可使用 go 函数创建 2 个子协程，并发地请求多个 URL
并创建了一个 chan，使用 use 闭包引用语法，传递给子协程
主协程循环调用 chan->pop，等待子协程完成任务，yield 进入挂起状态
并发的两个子协程其中某个完成请求时，调用 chan->push 将数据推送给主协程
子协程完成 URL 请求后退出，主协程从挂起状态中恢复，继续向下执行调用 $resp->end 发送响应结果*/
$server = new Swoole\Http\Server('127.0.0.1',9501);
$server->on('request',function (\Swoole\Http\Request $request, \Swoole\Http\Response $response){
	echo "into \n";
	$chan = new chan(2);
	go(function ()use ($chan){
		$client = new Swoole\Coroutine\Http\Client('www.qq.com',80);
		$client->set(['timeout'=>10]);
		$client->setHeaders([
								'Host' => "www.qq.com",
								"User-Agent" => 'Chrome/49.0.2587.3',
								'Accept' => 'text/html,application/xhtml+xml,application/xml',
								'Accept-Encoding' => 'gzip',
							]);
		$ret = $client->get('/');
		$chan->push(['www.qq.com' => $client->body]);
	});
	go(function ()use ($chan){
		$client = new Swoole\Coroutine\Http\Client('www.163.com',80);
		$client->set(['timeout'=>10]);
		$client->setHeaders([
								'Host' => "www.163.com",
								"User-Agent" => 'Chrome/49.0.2587.3',
								'Accept' => 'text/html,application/xhtml+xml,application/xml',
								'Accept-Encoding' => 'gzip',
							]);
		$ret = $client->get('/');
		$chan->push(['www.163.com' => $client->body]);
	});

	$result = [];
	for ($i = 0; $i < 2; $i++)
	{
		$result += $chan->pop();
	}
	$response->end($result);
});
$server->start();