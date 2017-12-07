<?php 
class kaoqinClassAction extends apiAction
{
	public function adddkjlAction()
	{
		$mac 	= $this->post('mac');
		$ip 	= $this->post('ip');
		$msg 	= m('kaoqin')->adddkjl($this->adminid,0,'',$ip,$mac);
		if($msg!='')$this->showreturn('', $msg, 201);
		$this->showreturn($this->now);
	}
}