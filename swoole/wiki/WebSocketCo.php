<?php
\Swoole\Coroutine\Run(function () {
	$server = new Co\Http\Server("127.0.0.1", 9502, false);
	$server->handle('/websocket', function ($request,Swoole\Http\Response  $ws) {
		/*$ws->upgrade()：向客户端发送 WebSocket 握手消息
		while(true) 循环处理消息的接收和发送
		$ws->recv() 接收 WebSocket 消息帧
		$ws->push() 向对端发送数据帧
		$ws->close() 关闭连接*/

		$ws->upgrade();
		while (true) {
			$frame = $ws->recv();
			if ($frame === false) {
				echo "error : " . swoole_last_error() . "\n";
				break;
			} else if ($frame == '') {
				break;
			} else {
				if ($frame->data == "close") {
					$ws->close();
					return;
				}
				$ws->push("Hello {$frame->data}!");
				$ws->push("How are you, {$frame->data}?");
			}
		}
	});

	$server->handle('/', function ($request, $response) {
		$response->end(<<<HTML
    <h1>Swoole WebSocket Server</h1>
    <script>
var wsServer = 'ws://127.0.0.1:9502/websocket';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
    websocket.send('hello');
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
</script>
HTML
		);
	});

	$server->start();
});
