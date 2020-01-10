<?php
/**
 * redis sub(消息订阅端)
 * @date 2016-04-24 15:00
 */
$redis = new Redis();
$res = $redis->connect('127.0.0.1', 6379,0);
$redis->auth('redis123');
//这里不用使用 while(true),启动时会伴随着进程
$redis->subscribe(array('sub'), function ($redisObject, $channelName, $message) {
	echo $channelName, "==>", $message,PHP_EOL;
	echo date('Y-m-d H:i:s'),PHP_EOL;
});