<?php
/**
* YAR CLIENT
*/
class YarClient
{

	public static function index($action,$argument)
	{
		//判断请求是否有效
		if(2 != count(explode('/',$action))) {
			throw new Exception("api url not find"."\n"."eg: Test/getInfo"."\n");
		}
		if(!is_string($argument)) {
			throw new Exception("the argument must be a string");
		}
		//判断扩展是否存在
		if (!extension_loaded('yar')) {
		   throw new Exception("client not support yar");
		}

		//config
		//指定请求地址
		$client = new Yar_Client("http://192.168.124.130/YarIndex.php");
		//请求超时时间
		$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT,1000);
		//数据格式
		$client->SetOpt(YAR_OPT_PACKAGER,'json');
		//密码验证
		$pawdConf = [
			'intoPwd' => 'YarInto' ,
			'addSalt' => 'YarSalt' ,
			'other'	  => 'addYar' ,	
		] ;
		$hash = password_hash(json_encode($pawdConf),PASSWORD_DEFAULT);
		//service同一入口
		return $client->index($action,$argument,$hash);
		
	}

}
//代码调用
$data = YarClient::index('test/getInfo',json_encode(['id'=>1,'name'=>'zzlqc']));
var_dump($data);exit();

