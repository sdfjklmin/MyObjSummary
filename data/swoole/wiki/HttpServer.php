<?php
$http = new Swoole\Http\Server("127.0.0.1", 9501);

//Setting
//--------------------
// 配置   			 |
//--------------------
$http->set([
			   //配置静态文件根目录，与 enable_static_handler 配合使用。
			   //可直接访问 http://web/try.html  http://web/try.txt 等静态文件
			   'document_root'         => '/home/wwwroot/php-map', // v4.4.0以下版本, 此处必须为绝对路径
			   'enable_static_handler' => true,
		   ]);

#在 onRequest 回调函数返回时底层会销毁 $request 和 $response 对象
$http->on('request', function (swoole_http_request $request, swoole_http_response $response) {
	/*
	server {
		root /data/wwwroot/;
		server_name local.swoole.com;

		location / {
			proxy_http_version 1.1;
			proxy_set_header Connection "keep-alive";
			proxy_set_header X-Real-IP $remote_addr;
			if (!-e $request_filename) {
				 proxy_pass http://127.0.0.1:9501;
			}
		}
	}*/
	//配置了nginx才能获取x-real-ip
	$ip = $request->header['x-real-ip'];

	//Request
	//--------------------
	// 请求对象 $request   |
	//--------------------
	$request->header;
	@$request->header['key_name'];

	$request->server;
	@$request->server['key_name']; #server 与 PHP 的 $_SERVER 数组保持一致

	$request->get;
	@$request->get['key_name'];#相当于 PHP 中的 $_GET，格式为数组。为防止 HASH 攻击，GET 参数最大不允许超过 128 个

	#POST 与 Header 加起来的尺寸不得超过 package_max_length 的设置，否则会认为是恶意请求
	$request->post;
	@$request->post['key_name'];#相当于 PHP 中的 $_POST，格式为数组。为防止 HASH 攻击，POST 参数最大不允许超过 128 个

	$request->cookie;
	@$request->cookie['key_name'];

	#当 $request 对象销毁时，会自动删除上传的临时文件
	$request->files;
	/*Array
	(
		[name] => facepalm.jpg // 浏览器上传时传入的文件名称
		[type] => image/jpeg // MIME类型
		[tmp_name] => /tmp/swoole.upfile.n3FmFr // 上传的临时文件，文件名以/tmp/swoole.upfile开头
		[size] => 15476 // 文件尺寸
		[error] => 0
	)*/

	//获取原始的 POST 包体。
	//用于非 application/x-www-form-urlencoded 格式的 Http POST 请求。
	//返回原始 POST 数据，此函数等同于 PHP 的 fopen('php://input')
	$request->rawContent();//string

	//获取完整的原始 Http 请求报文。包括 Http Header 和 Http Body
	$request->getData();//string
	/*
	POST /?key_name=32 HTTP/1.1
	Connection: keep-alive
	X-Real-IP: 127.0.0.1
	Host: 127.0.0.1:9501
	Content-Length: 10
	Authorization: null
	Content-Type: text/plain
	User-Agent: PostmanRuntime/7.22.0
	Accept:
	*/

	#demo
	/*var_dump($request->server['query_string']);//get参数,没有则不存在
	var_dump($request->server['request_method']);
	var_dump($request->server['request_uri']);
	var_dump($request->server['path_info']);*/


	//Response
	//-------------------------
	//响应对象 $response  	  |
	//before: 需要在 end 前设置 |
	//-------------------------
	#before:设置herder
	//$response->header('content-type', 'image/jpeg', true);

	#before:设置响应cookie
	#$response->cookie(string $key, string $value = '', int $expire = 0 , string $path = '/', string $domain  = '', bool $secure = false , bool $httponly = false, string $samesite = '');

	#before:设置 HTTP 响应的 cookie 信息,rawCookie() 的参数和上文的 cookie() 一致，只不过不进行编码处理
	#$response->rawCookie('a','b');

	#before:设置响应码
	#$response->status(int $http_status_code, int $reason): bool;
	$response->status(200);

	#before:压缩,此方法在 4.1.0 或更高版本中已废弃，请移步 http_compression
	#$response->gzip(int $level = 1);

	#redirect(),重定向
	#发送 Http 跳转。调用此方法会自动 end 发送并结束响应。
	#状态码【默认为 302 临时跳转，传入 301 表示永久跳转】
	//$response->redirect("http://www.baidu.com/", 301);

	#write(),启用 Http Chunk 分段向浏览器发送相应内容。

	#sendfile(),发送文件到浏览器。
	#$response->sendfile(string $filename, int $offset = 0, int $length = 0): bool;
	/*$response->header('Content-Type', 'image/jpeg');
	$response->sendfile(__DIR__.$request->server['request_uri']);*/


	#结束,后面的不会响应给客户端
	#发送 Http 响应体，并结束请求处理。
	$response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");

	#detach(),分离响应对象。使用此方法后，$response 对象销毁时不会自动 end，与 Http\Response::create 和 Server::send 配合使用。
	#@link https://wiki.swoole.com/#/http_server?id=detach

	#create(),
	//这里要分离响应,否则会响应两次
	/*$response->detach();
	$resp2 = Swoole\Http\Response::create($request->fd);
	$resp2->end("hello world");*/
});

$http->start();