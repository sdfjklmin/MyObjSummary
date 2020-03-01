<?php

/**
 * Class TcpOrUdpServer
 * @author sjm
 * @link https://wiki.swoole.com/#/server/methods
 * @step
 * 		创建服务 -> (设置参数|注册事件(设置必选回调函数)) -> 启动服务
 */
$server = new \Swoole\Server( '127.0.0.1',  $port = 9502,  $mode = SWOOLE_PROCESS,  $sockType = SWOOLE_SOCK_TCP);
//-------------------------
// 绑定服务
//-------------------------
// 您可以混合使用UDP/TCP，同时监听内网和外网端口，多端口监听参考 addlistener小节。
// 添加 TCP
//$server->addlistener("127.0.0.1", 9502, SWOOLE_SOCK_TCP);

// 添加 Web Socket (这里是对应本机的ip地址,ipconfig,ip addr)
// error : WARNING swSocket_bind(:439): bind(这里绑定的应该是本机内外IP) failed, Error: Cannot assign requested address[99]
//$server->addlistener("192.168.100.134", 9503, SWOOLE_SOCK_TCP);

// UDP
//$server->addlistener("0.0.0.0", 9504, SWOOLE_SOCK_UDP);

//$server->addlistener("/var/run/myserv.sock", 0, SWOOLE_UNIX_STREAM); //UnixSocket Stream
//TCP + SSL
//$server->addlistener("127.0.0.1", 9502, SWOOLE_SOCK_TCP | SWOOLE_SSL);

// 系统随机分配端口，返回值为随机分配的端口
//$port = $server->addListener("0.0.0.0", 0, SWOOLE_SOCK_TCP);
//echo $port->port,"\n";

//-------------------------
// 设置参数
//-------------------------
//$server->set([]);


//--------------------------------------------
// 注册事件
// 不同的服务类型有不同的必要事件,否则服务无法启动
//  TCP 必须要有 onReceive
//  UPD 必须要有 onPacket
//--------------------------------------------
$server->on('connect',function ($servers, $fd) {
	echo "link";
});

$server->on('receive',function (\Swoole\Server $servers, $fd, $reactor_id, $data) {
	echo 'on receive';
	//获取某个连接来自于哪个端口
	var_dump($servers->getClientInfo($fd));

	//主动关闭
	$servers->close($fd);
});

//-------------------------
// 启动服务
//-------------------------
$server->start();