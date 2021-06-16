<?php


namespace app\swoole;


class Route
{
	/**
	 * @var \swoole_http_request
	 */
	private $request;

	public function __construct(\swoole_http_request $request)
	{
		$this->request = $request;
	}

	public function parsing()
	{
		//TODO::更具路由映射对应的操作
		if($this->request->server['request_uri'] != '/favicon.ico') {
			var_dump(pathinfo($this->request->server['request_uri']));
		}
		return 'Route handing';
	}
}