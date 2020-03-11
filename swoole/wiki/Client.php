<?php
$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if(!$client->connect('127.0.0.1',9501,-1)) {
	exit('Client error');
}
$client->send("hello");
echo $client->recv();
$client->close();