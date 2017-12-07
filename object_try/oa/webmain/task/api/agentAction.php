<?php 
class agentClassAction extends apiAction
{
	public function dataAction()
	{
		$agentarr			= m('reim')->getagent($this->adminid);
		$arr['agentjson']	= json_encode($agentarr);
		$this->showreturn($arr);
	}
	
	public function getoptnumAction()
	{
		$num  	= $this->post('num');
		$mid  	= (int)$this->post('mid');
		
		$arr 	= m('flow')->opt('getoptmenu', $num, $mid);
		$this->showreturn($arr);
	}
}