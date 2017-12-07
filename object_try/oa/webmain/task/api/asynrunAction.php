<?php
class asynrunClassAction extends apiAction
{
	public function initAction()
	{
		$this->display	= false;
		$uid   			= (int)$this->get('adminid');
		$key   			= $this->get('asynkey');
		$mykey			= getconfig('asynkey');
		if($mykey != ''){
			$wodkey 	= md5(md5($mykey));
			if($wodkey != $key)exit('sorry,asyn');
		}
		$urs 			= m('admin')->getone($uid,'`id`,`name`,`user`');
		if($urs)$this->setNowUser($urs['id'], $urs['name'], $urs['user']);
	}
	
	//测试
	public function indexAction()
	{
		$runtime = $this->get('runtime');
		$this->rock->debugs('hehe:'.time().','.$runtime.'','yibu');
	}
	
	//消息同步到微信企业会话
	public function wxchattbAction()
	{
		$id   = (int)$this->get('id');
		m('weixin:chat')->chattongbu($id);
	}
	
	//薪资发放通知给人员
	public function salaryffAction()
	{
		$id    = (int)$this->get('id');
		m('flow')->initflow('hrsalary', $id)->todouser();
	}
	
	//下载微信发送的图片到服务器
	public function downwxpicAction()
	{
		$picurl = $this->rock->jm->uncrypt($this->get('picurl'));
		$msgid  = $this->get('msgid');
		m('reim')->downwximg($picurl, $msgid);
	}
}