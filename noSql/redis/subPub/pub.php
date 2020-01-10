<?php
/**
 * redis sub(消息发送端)
 * @date 2016-04-24 15:00
 */
$redis = new Redis();
// 第一个参数为redis服务器的ip,第二个为端口
$res = $redis->connect('127.0.0.1', 6379);
$redis->auth('redis123');
// test为发布的频道名称,hello,world为发布的消息
$res = $redis->publish('sub','hello,world');
var_dump($res); // 0 or 1