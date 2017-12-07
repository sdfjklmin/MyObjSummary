<?php 
class checkChajian extends Chajian{
	
	//是否为邮箱
	public function isemail($str)
	{
		if(isempt($str))return false;
		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}
	
	//是否为手机号
	public function ismobile($str)
	{
		if(isempt($str))return false;
		if(!is_numeric($str) || strlen($str)!=11)return false;
		return true;
	}
	
	//是否有中文
	public function isincn($str)
	{
		return preg_match("/[\x7f-\xff]/", $str);
	}
	
	//是否整个的英文a-z,0-9
	public function iszgen($str)
	{
		if(isempt($str))return false;
		if($this->isincn($str)){
			return false;
		}
		return true;
	}
}