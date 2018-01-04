Yar:
	php的rpc扩展,已经纳入php官网文档.

	安装:
		yar依赖于msgpack.
		针对于pecl安装:
			pecl install msgpack 
			pecl install yar 
			把对应的扩展加入到php.ini即可使用 .
	使用:
		客户端->YarClient.php
		服务端->YarIndex.php
		服务端代码调用->Test.php
	客户端配置:
		//指定请求地址
		$client = new Yar_Client("http://192.168.124.130/YarIndex.php");
		//请求超时时间
		$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT,1000);
		//数据格式
		$client->SetOpt(YAR_OPT_PACKAGER,'json');
		请求时间和数据格式根据自己的php系统进行添加,如果缺少可能报错