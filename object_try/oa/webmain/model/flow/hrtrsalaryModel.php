<?php
class flow_hrtrsalaryClassModel extends flowModel
{

	protected function flowbillwhere($uid, $lx)
	{
		$key  	= $this->rock->post('key');
		$where 	= '';
		if($key!='')$where.=" and (b.deptallname like '%$key%' or b.name like '%$key%' or b.ranking like '%$key%' )";
		$table 	= '`[Q]hrtrsalary` a left join `[Q]admin` b on a.uid=b.id';
		return array(
			'where' => $where,
			'table'	=> $table,
			'fields'=> 'a.*,b.deptname,b.name',
			'order' => 'a.`optdt` desc'
		);
	}
}