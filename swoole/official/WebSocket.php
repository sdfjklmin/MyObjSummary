<?php
class WebSocket
{
	/**
	 * @var swoole_websocket_server
	 */
	private $_webSocket;

	/**
	 * @var \Swoole\Table
	 */
	private $_initTable;

	/**
	 * WebSocket constructor.
	 * @param int $port
	 */
	public function __construct($port = 9502)
	{
		//创建内存表,要在服务之前创建
		$this->_table();
		//创建服务
		$this->_webSocket = new swoole_websocket_server('0.0.0.0',$port);
		$this->_webSocket->on('open',array($this,'onOpen'));
		$this->_webSocket->on('message',array($this,'onMessage'));
		$this->_webSocket->on('request',array($this,'onRequest'));
		$this->_webSocket->on('close',array($this,'onClose'));
		//$this->_webSocket->set();
		$this->_webSocket->start();
	}

	/**
	 * 内存表操作
	 */
	public function _table()
	{
		$table = new Swoole\Table(1024);
		$table->column('user_id', swoole_table::TYPE_INT, 4);       //1,2,4,8
		$table->column('fd_id', swoole_table::TYPE_INT, 4);       //1,2,4,8
		$table->column('name', swoole_table::TYPE_STRING, 64);
		$table->column('num', swoole_table::TYPE_FLOAT);
		$table->create();
		$this->_initTable = $table;
		//获取全部数据
		/*foreach ($this->_initTable as $item) {
			var_dump($item);
		}*/
	}

	/**
	 * @param swoole_websocket_server $ws
	 * @param swoole_http_request $request
	 */
	public function onOpen(swoole_websocket_server $ws, swoole_http_request $request)
	{
		//可以将 fd 和 用户信息 进行绑定
		//var_dump($request->fd, $request->get, $request->server);
		$sign   = $request->get['sign'] ?? '';
		$userId = $request->get['user_id'] ?? '';
		if(!$sign || !$userId) {
			$ws->disconnect($request->fd,400,'参数错误');
		}
		//签名验证可以使用 sha1,前端有对应的签名组件
		if($sign != 'test') {
			$ws->disconnect($request->fd,403,'没有权限,主动关闭');
		}else{
			//设置当前连接数据与用户信息
			$this->_initTable->set($userId,['fd_id'=>$request->fd,'user_id'=>$userId]);
			$ws->push($request->fd, "hello, welcome\n");
		}
	}

	/**
	 * @param swoole_websocket_server $ws
	 * @param $frame
	 */
	public function onMessage(swoole_websocket_server $ws, swoole_websocket_frame $frame)
	{
		echo "--------------message----","\n";
		//var_dump($frame);
		$ws->push($frame->fd, "server: {$frame->data}");
	}

	/**
	 * @param swoole_http_request $request
	 * @param swoole_http_response $response
	 */
	public function onRequest(swoole_http_request $request, swoole_http_response $response)
	{
		//$request->post验证数据
		//业务逻辑操作,剔除指定的用户ws
		$userId = $request->post['user_id'];
		if($userId) {
			$userData = $this->_initTable->get($userId);
			$fd = $userData['fd_id'];
			if ($this->_webSocket->isEstablished($fd)) {
				//发送消息
				$this->_webSocket->push($fd, '你已被管理员剔除');
				//主动关闭连接当前连接
				$this->_webSocket->disconnect($fd,404,'主动关闭');
			}
		}
		//遍历所有WebSocket连接用户的fd，给所有用户推送
		/*foreach ($this->_webSocket->connections as $fd) {
			// 需要先判断是否是正确的websocket连接，否则有可能会push失败
			if ($this->_webSocket->isEstablished($fd)) {
				//发送消息
				$this->_webSocket->push($fd, $request->post['scene']);
				//主动关闭连接当前连接
				//$this->_webSocket->disconnect($fd,404,'主动关闭');
			}
		}*/
	}

	/**
	 * @param swoole_websocket_server $ws
	 * @param int $fd 对应连接的标示 socket id
	 */
	public function onClose(swoole_websocket_server $ws, $fd)
	{
		echo "----------------close --------------------\n";
		var_dump($fd);
	}
}

new WebSocket();

//------------------------------------------------------------
//			面向过程										     |
//------------------------------------------------------------
/*$webSocket = new swoole_websocket_server('0.0.0.0',9502);

//监听WebSocket连接打开事件
$webSocket->on('open', function ($ws, $request) {
	//可以将 fd 和 用户信息 进行绑定
	var_dump($request->fd, $request->get, $request->server);
	$sign = $request->get['sign'] ?? '';
	//签名验证可以使用 sha1,前端有对应的签名组件
	if($sign != 'test') {
		$ws->disconnect($request->fd,403,'没有权限,主动关闭');
	}else{
		$ws->push($request->fd, "hello, welcome\n");
	}
});

//监听WebSocket消息事件
$webSocket->on('message', function ($ws, $frame) {
	echo "Message: {$frame->data}\n";
	$ws->push($frame->fd, "server: {$frame->data}");
});

//设置请求,用于主动通知
$webSocket->on("request", function(swoole_http_request $request, swoole_http_response $response){
	//$request->post验证数据
	//遍历所有WebSocket连接用户的fd，给所有用户推送
	global $webSocket;
	foreach ($webSocket->connections as $fd) {
		// 需要先判断是否是正确的websocket连接，否则有可能会push失败
		if ($webSocket->isEstablished($fd)) {
			//发送消息
			$webSocket->push($fd, $request->post['scene']);
			//主动关闭连接当前连接
			//$webSocket->disconnect($fd,404,'主动关闭');
		}
	}
});

//监听WebSocket连接关闭事件
$webSocket->on('close', function ($ws, $fd) {
	echo "client-{$fd} is closed\n";
});

$webSocket->start();*/