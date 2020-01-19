<?php
class WebSocketServerPush
{

	/**
	 * @var string
	 */
	private $request_client;

	public function __construct()
	{
		$this->request_client = 'curl';
	}

	public function curl($data)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:9502");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_exec($curl);
		curl_close($curl);
	}

	public function send()
	{
		$param['scene'] = '牛皮啊1';
		$this->curl($param);   // 主动推送消息
	}
}

$obj = new WebSocketServerPush();
$obj->send();