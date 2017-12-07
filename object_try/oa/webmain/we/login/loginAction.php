<?php 
class loginClassAction extends ActionNot{
	public function defaultAction()
	{
	}
	
	public function wxloginAction()
	{
		$this->display= false;
		m('weixin:oauth')->login();
	}
	
	public function wxlogincodeAction()
	{
		$this->display= false;
		m('weixin:oauth')->logincode();
	}
}