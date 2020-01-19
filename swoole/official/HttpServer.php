<?php
//--------------
// http server |
//--------------
//这里类似于启动服务器,可以生成一些全局变量(数据库长连接),在监听时使用
//这里不推荐全局变量,比如现在有一个master和三个worker进程,
//当 worker进程都使用mater时,master中的数据早已经创建,当worker1,worker2,worker3使用时可能会有数据延迟,混乱,错误等情况.
//就算在单个worker中使用单独的DB连接,那怎么保证每个worker中的数据操作不会相互干扰呢?(MySQL事物级别为RR-可重复读)(通过PHP和MySql之间的套接层接口调用)
/** @var \Swoole\Http\Server $swoole */
$swoole = new swoole_http_server('127.0.0.1',9900);
echo "-----------init http Server","\n";
$swoole->on('request', function(swoole_http_request $request, swoole_http_response $response) {
	//这里类似于服务器,在内部输出打印类似于日志,要响应请求这需要特定的语法封装,类似于解析
	//这里输出要使用 write 或者 end
	//$response->write('what');
	//$response->end();
	echo "-----------each response http Server","\n";
	//谷歌浏览器的默认机制会去请求 /favicon.ico 找到则显示,未找到则 404,
	//所以这里用 谷歌浏览器 访问会输出两次,用 postman,curl,httpClient 则会输出一次
	var_dump($request->server);
    $response->end("<h1>hello swoole</h1>");
	echo "-----------each end http Server","\n";
});
$swoole->start();
