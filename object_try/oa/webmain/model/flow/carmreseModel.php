<?php
class flow_carmreseClassModel extends flowModel
{


	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$key 	= $this->rock->post('key');
		$dt 	= $this->rock->post('dt');
		if($key != '')$where.=" and (`carnum`='$key' or `usename` like '%$key%' or `optname` like '%$key%')";
		if($dt != '')$where.=" and (`applydt`='$dt' or `startdt` like '$dt%')";
		
		return array(
			'where' => $where,
			'order' => 'optdt desc'
		);
	}
}