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
$server->set([
	#reactor(反应堆)数量,设置启动的 Reactor 线程数,一般与cup核数保持一致
	'reactor_num' => 4 , # <= swoole_cpu_num() * 4

	#设置 worker 进程数,如 1 个请求为 100ms,要保证 1000qps,必须配置 10个进程或更多.
	#但进程越多开销越大,相互切换也会产生大量开销,这里要适当,不要太大.
	'worker_num' => 2, #建议与cup保持一致

	#设置 worker 进程的最大任务数,当操过数值后会自动退出(有第一的的等待时间 max_wait_time ),释放资源,避免内存泄漏
	'max_request' => 0, #默认不退出

	#服务器程序,最大允许的连接数,超过数值后会拒绝连接.
	#最小 (worker_num + task_worker_num) * 2 + 32 ,小于则重置为默认
	'max_connection' => 40, #默认 ulimit -n

	#配置 Task 进程的数量。【默认值：未配置则不启动 task,同步阻塞】
	#配置时候需要注册 onTask,onFinish
	//'task_worker_num' => '',
	//'task_相关配置'    => '请参考官方文档'
	'dispatch_func' => function ($serv, $fd, $type, $data) {
	    echo '-----self dispatch func start',"\n";
		var_dump($fd, $type, $data);
	    echo '-----self dispatch func end',"\n";
		return intval($data[0]);
	},
]);

//--------------------------------------------
// 注册事件
// 不同的服务类型有不同的必要事件,否则服务无法启动
//  TCP 必须要有 onReceive
//  UPD 必须要有 onPacket
//--------------------------------------------
$server->on('connect',function (\Swoole\Server $servers, $fd) {
	echo "当前服务器共有 " . count($servers->connections) . " 个连接\n";
});

$server->on('receive',function (\Swoole\Server $servers, $fd, $reactor_id, $data) {
	echo '---- into receive\n';
	//获取某个连接来自于哪个端口
	//var_dump($servers->getClientInfo($fd));
	echo date('Y-m-d H:i:s'),"\n";
	//主动关闭
	//$servers->close($fd);
});

$server->on('close',function ($serv, $fd, $from_id){
	echo '---- into close\n';
	echo "Client {$fd} close connection\n";
});

//-------------------------
// 启动服务
//-------------------------
$server->start();