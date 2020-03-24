<?php
//类比于 php 自带 socket/stream_socket,优化封装
//----------------------------
// demo 1					 |
//----------------------------
/*$fp = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
fwrite($fp,"GET / HTTP/1.1\r\nHost: www.qq.com\r\n\r\n");

Swoole\Event::add($fp, function($fp) {
	$resp = fread($fp, 8192);
	//var_dump($resp);
	//socket处理完成后，从epoll事件中移除socket
	swoole_event_del($fp);
	fclose($fp);
});
echo "Finish\n";  //Swoole\Event::add 不会阻塞进程，这行代码会顺序执行
*/

//-------------------------------------
// demo 2							  |
//-------------------------------------
/*use Swoole\Event;
$fd = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
Event::add($fd, function ($fd1) use ($fd){
	Event::del($fd);
}, null, SWOOLE_EVENT_READ);
var_dump(Event::isset($fd, SWOOLE_EVENT_READ)); //返回 true
var_dump(Event::isset($fd, SWOOLE_EVENT_WRITE)); //返回 false
var_dump(Event::isset($fd, SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE)); //返回 true*/
