<?php
//设置大小->字段->创建
$table = new Swoole\Table(1024);
$table->column('fd', Swoole\Table::TYPE_INT);
$table->column('reactor_id', Swoole\Table::TYPE_INT,1);
$table->column('data', Swoole\Table::TYPE_STRING, 64);
$table->create();
//-----------------------
// Swoole\Talbe 方法
// column() 设置字段
// create() 创表
// set()  设置数据
// incr() 自增
// decr() 自减
// get()  获取
// exist() 是否存在
// count() 总数
// del()  删除
//-----------------------

$serv = new Swoole\Server('127.0.0.1', 9501);
$serv->set(['dispatch_mode' => 1]);

/*手动设置动态变量*/
$serv->table = $table;

$serv->on('receive', function (\Swoole\Server $serv, $fd, $reactor_id, $data) {

	$cmd = explode(" ", trim($data));
	//get
	if ($cmd[0] == 'get') {
		//get self
		if (count($cmd) < 2) {
			$cmd[1] = $fd;
		}
		$get_fd = intval($cmd[1]);
		$info   = $serv->table->get($get_fd);
		var_dump($info);
		$serv->send($fd, var_export($info, true) . "\n");
	} //set
	elseif ($cmd[0] == 'set') {
		$ret = $serv->table->set($fd, array('reactor_id' => -2147483648, 'fd' => $fd, 'data' => $cmd[1]));
		if ($ret === false) {
			$serv->send($fd, "ERROR\n");
		} else {
			$serv->send($fd, "OK\n");
		}
	} else {
		$serv->send($fd, "command error.\n");
	}
});

$serv->start();
