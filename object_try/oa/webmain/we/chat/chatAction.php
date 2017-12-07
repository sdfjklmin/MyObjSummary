<?php 
class chatClassAction extends ActionNot{
	public function defaultAction()
	{
		$type 	= $this->get('type');
		$uid  	= $this->get('uid');
		$db 	= m('reim');
		$arr 	= $db->getreceinfor($type, $uid);
		if(!isset($arr['name']))exit('error');
		$this->title = $arr['name'];
		$this->assign('arr', $arr);
	}
}