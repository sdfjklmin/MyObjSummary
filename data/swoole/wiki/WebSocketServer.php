<?php
$server = new \Swoole\WebSocket\Server('127.0.0.1',9502);
//handShake -> open -> massage -> close

#这里open已经握手成功
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
	echo "server: handshake success with fd{$request->fd}\n";
	//websocket_status : 3
	echo "server: websocket_status {$server->connection_info($request->fd)['websocket_status']}\n";
});

$server->on('message', function (Swoole\WebSocket\Server $server, Swoole\Websocket\Frame $frame) {
	//$frame->fd	客户端的 socket id，使用 $server->push 推送数据时需要用到
	//$frame->data	数据内容，可以是文本内容也可以是二进制数据，可以通过 opcode 的值来判断
	//$frame->opcode	WebSocket 的 OpCode 类型，可以参考 WebSocket 协议标准文档
	//$frame->finish	表示数据帧是否完整，一个 WebSocket 请求可能会分成多个数据帧进行发送（底层已经实现了自动合并数据帧，现在不用担心接收到的数据帧不完整）

	//推送数据,自动进行协议打包
	//Swoole\WebSocket\Server->push(int $fd, string $data, int $opcode = 1, bool $finish = true): bool;
	$server->push($frame->fd, "this is server");

	//主动关闭连接
	$server->disconnect($frame->fd,1,'最大125');
});

$server->on('close', function (Swoole\WebSocket\Server $ser, $fd) {
	echo "close: client {$fd} closed\n";
	//websocket_status : 3
	echo "close: websocket_status {$ser->connection_info($fd)['websocket_status']}\n";
});

$server->start();