<?php
class flow_waichuClassModel extends flowModel
{
	protected function flowgetfields($lx)
	{
		$arr = array();
		$this->rs['location']	= '';
		//$arr['location'] 		= '此时间定位打卡';
		return $arr;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$dt 	= $this->rock->date;
		$where 	= "`uid`=$uid and `intime`>'$dt 00:00:00' and `outtime`<'$dt 23:59:59'";
		if($lx=='myall'){
			$where 	= "`uid`=$uid";
		}
		return array(
			'where' => 'and '.$where
		);
	}
}