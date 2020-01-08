<?php
/** 订阅消息
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/7/17
 * Time: 11:36
 */

/**
 * redis sub(消息订阅端)
 * @ blog: phping.sinaapp.com
 * @date 2016-04-24 15:00
 */
$redis = new Redis();
$res = $redis->connect('127.0.0.1', 6379,0);
$redis->subscribe(array('test'), 'callback');

// 回调函数,这里写处理逻辑
function callback($instance, $channelName, $message) {
    echo $channelName, "==>", $message,PHP_EOL;
}