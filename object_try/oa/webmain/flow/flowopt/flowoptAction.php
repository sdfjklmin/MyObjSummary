<?php
class flowoptClassAction extends Action
{
	public function addlogAjax()
	{
		$sm 	= $this->post('sm');
		$mid 	= (int)$this->post('mid');
		$modenum= $this->post('modenum');
		$name 	= $this->post('name');
		$zt 	= $this->post('zt');
		$ztid 	= $this->post('ztid','1');
		m('flow')->addlog($modenum, $mid,$name,array(
			'explain' 		=> $sm,
			'statusname' 	=> $zt,
			'status' 		=> $ztid
		));
		$this->showreturn('ok');
	}
	
	public function delflowAjax()
	{
		$mid 	= (int)$this->post('mid');
		$modenum= $this->post('modenum');
		$sm 	= $this->post('sm');
		$msg 	= m('flow')->opt('deletebill', $modenum, $mid, $sm);
		if($msg != 'ok')$this->showreturn('', $msg, 201);
		$this->showreturn('ok');
	}
	
	public function checkAjax()
	{
		$mid 	= (int)$this->post('mid');
		$zt 	= (int)$this->post('zt');
		$modenum= $this->post('modenum');
		$sm 	= $this->post('sm');
		$msg 	= m('flow')->opt('check', $modenum, $mid, $zt, $sm);
		
		backmsg('', $msg);
	}
	
	public function getoptnumAjax()
	{
		$mid 	= (int)$this->post('mid');
		$num	= $this->post('num');
		
		$arr 	= m('flow')->getoptmenu($num, $mid, 1);
		$this->showreturn($arr);
	}
	
	public function yyoptmenuAjax()
	{
		$num 	= $this->post('modenum');
		$sm 	= $this->post('sm');
		$optid 	= (int)$this->post('optmenuid');
		$zt 	= (int)$this->post('statusvalue');
		$mid 	= (int)$this->post('mid');
		$msg 	= m('flow')->optmenu($num, $mid, $optid, $zt, $sm);
		if($msg != 'ok')$this->showreturn('', $msg, 201);
		$this->showreturn('');
	}
}