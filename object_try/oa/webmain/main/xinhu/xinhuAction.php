<?php
class xinhuClassAction extends Action
{

	
	public function setsaveAjax()
	{
		$this->option->setval('reimhostsystem', $this->post('host'));
		$this->option->setval('reimrecidsystem', $this->post('receid'));
		$this->option->setval('reimpushurlsystem', $this->post('push'));
		$this->backmsg();
	}
	
	public function getsetAjax()
	{
		$arr= array();
		$arr['reimhost']= $this->option->getval('reimhostsystem');
		$arr['reimrecid']= $this->option->getval('reimrecidsystem');
		$arr['reimpushurl']= $this->option->getval('reimpushurlsystem');
		echo json_encode($arr);
	}
	
	public function testsendAjax()
	{
		$obj = m('reim');
		$str = $obj->sendpush($this->adminid, $this->adminid,array(
			'cont' 	=> $this->jm->base64encode('测试内容:'.$this->now.''),
			'type' 	=> 'user',
			'optdt' => $this->now,
			'messid' => 0
		));
		$msg 	= '';
		if(!contain($str,'ok'))$msg='<font color=red>服务端推送地址不能使用</font>';
		if($msg=='')$msg='服务端推送地址可以使用';
		echo $msg;
	}
}