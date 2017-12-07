<?php
class flow_userinfoClassModel extends flowModel
{
	public  $uidfields = 'id';
	
	public function initModel()
	{
		$this->statearr 	= explode(',','试用期,正式,实习生,兼职,临时工,离职');
		$this->birtypearr 	= explode(',','阳历,农历');
	}

	public function flowrsreplace($rs)
	{
		$rs['state']		= $this->statearr[$rs['state']];
		$rs['birtype']		= $this->birtypearr[$rs['birtype']];
		return $rs;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$s 		= '';
		$key 	= $this->rock->post('key');
		if($key!=''){
			$s = " and (`name` like '%$key%' or `ranking` like '%$key%' or `deptname` like '%$key%') ";
		}
		return array(
			'where'	=> $s,
			'fields'=> 'id,name,deptname,ranking,state,tel,sex,mobile,workdate,quitdt,positivedt,birtype'
		);
	}
}