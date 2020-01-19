<?php
$webSocket = new swoole_websocket_server('0.0.0.0',9502);

//监听WebSocket连接打开事件
$webSocket->on('open', function ($ws, $request) {
	var_dump($request->fd, $request->get, $request->server);
	$ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$webSocket->on('message', function ($ws, $frame) {
	echo "Message: {$frame->data}\n";
	$ws->push($frame->fd, "server: {$frame->data}");
});

//设置请求,用于主动通知
$webSocket->on("request", function(swoole_http_request $request, swoole_http_response $response){
	//$request->post验证数据
	//遍历所有WebSocket连接用户的fd，给所有用户推送
	global $webSocket;
	foreach ($webSocket->connections as $fd) {
		// 需要先判断是否是正确的websocket连接，否则有可能会push失败
		if ($webSocket->isEstablished($fd)) {
			$webSocket->push($fd, $request->post['scene']);
		}
	}
});

//监听WebSocket连接关闭事件
$webSocket->on('close', function ($ws, $fd) {
	echo "client-{$fd} is closed\n";
});

$webSocket->start();