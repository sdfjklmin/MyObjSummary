<?php 
class deptClassAction extends apiAction
{
	public function dataAction()
	{
		$deptarr 	= m('dept')->getdata();
		$userarr 	= m('admin')->getuser(1);
		$grouparr 	= m('reim')->getgroup($this->adminid);
		
		$arr['deptjson']	= json_encode($deptarr);
		$arr['userjson']	= json_encode($userarr);
		$arr['groupjson']	= json_encode($grouparr);
		$this->showreturn($arr);
	}
}