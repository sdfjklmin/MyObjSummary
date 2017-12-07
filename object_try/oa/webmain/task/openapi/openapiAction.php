<?php
/**
*	对外开发接口文件
*	createname：信呼
*	homeurl：http://xh829.com/
*	Copyright (c) 2016 rainrock (xh829.com)
*	Date:2016-11-01
*	explain：返回200为正常
*/
class openapiAction extends ActionNot
{
	private $openkey = 'rockxinhukey';
	public 	$postdata= '';
	
	public function initAction()
	{
		$this->display= false;
		$openkey = $this->post('openkey');
		if(HOST != '127.0.0.1'){
			if($openkey != md5($this->openkey))$this->showreturn('', 'openkey not access', 201);
		}
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))$this->postdata = $GLOBALS['HTTP_RAW_POST_DATA'];
	}
}