<?php
/**
* 
*/
namespace MyObjSummary\Api ;
require_once('./GameApi.php');
class FflApi extends GameApi
{
    public $_web = [] ;  # 网关请求地址
	public function __construct()
	{
		# code...
		parent::__construct('11');
		var_dump($this->_web);
	}
}

$test = new FflApi();