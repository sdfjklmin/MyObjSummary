<?php
namespace app\swoole;

class App
{
	const APP_NAME = 'WebServer';

	/**
	 * @var \swoole_http_server
	 */
	private $server;

	public function __construct()
	{
		$this->server = new \swoole_http_server('127.0.0.1',9501);
		$this->server->on('request',array($this,'onRequest'));
	}


	public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
	{
		$response->write((new Route($request))->parsing());
		$response->end();
	}

	public function run()
	{
		$this->server->start();
	}
}
