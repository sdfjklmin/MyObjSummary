<?php
/**
*	接口文件
*	createname：雨中磐石
*	homeurl：http://xh829.com/
*	Copyright (c) 2016 rainrock (xh829.com)
*	Date:2016-01-01
*	explain：返回200为正常
*/
class apiAction extends ActionNot
{

	public $userrs;
	public $cfrom		= '';
	public $token		= '';
	
	public function initAction()
	{
		$this->display= false;
		$time 		= time();
		$this->cfrom= $this->request('cfrom');
		$this->token= $this->request('token', $this->admintoken);
		$this->adminid 	 = (int)$this->request('adminid', $this->adminid);
		$this->adminname = '';
		$boss = (M == 'login|api');
		if(!$boss && HOST!='127.0.0.1'){
			if($this->isempt($this->token))$this->showreturn('','token invalid', 299);
			$to = m('logintoken')->rows("`token`='$this->token' and `uid`='$this->adminid' and `online`=1");
			if($to==0)$this->showreturn('','access invalid', 199);
		}
		$this->userrs = m('admin')->getone("`id`='$this->adminid' and `status`=1", '`name`,`user`,`id`,`ranking`,`deptname`,`deptid`');
		if(!$this->userrs && !$boss){
			$this->showreturn('', 'not found user', 199);
		}
		$this->adminname 	= $this->userrs['name'];
		$this->rock->adminid	= $this->adminid;
		$this->rock->adminname 	= $this->adminname;
	}
	
	public function getvals($nae, $dev='')
	{
		$sv = $this->rock->jm->base64decode($this->post($nae));
		if($this->isempt($sv))$sv=$dev;
		return $sv;
	}
}