<?php
$webSocket = new swoole_websocket_server('0.0.0.0',9502);

//监听WebSocket连接打开事件
$webSocket->on('open', function ($ws, $request) {
	//可以将 fd 和 用户信息 进行绑定
	var_dump($request->fd, $request->get, $request->server);
	$sign = $request->get['sign'] ?? '';
	//签名验证可以使用 sha1,前端有对应的签名组件
	if($sign != 'test') {
		$ws->disconnect($request->fd,403,'没有权限,主动关闭');
	}else{
		$ws->push($request->fd, "hello, welcome\n");
	}
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
			//发送消息
			$webSocket->push($fd, $request->post['scene']);
			//主动关闭连接当前连接
			//$webSocket->disconnect($fd,404,'主动关闭');
		}
	}
});

//监听WebSocket连接关闭事件
$webSocket->on('close', function ($ws, $fd) {
	echo "client-{$fd} is closed\n";
});

$webSocket->start();