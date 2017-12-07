<?php
class flow_bookborrowClassModel extends flowModel
{
	public function flowrsreplace($rs)
	{
		$fte = '<font color=red>否</font>';
		$isgh= 0;
		if(!isempt($rs['ghtime'])){
			$fte = '<font color=green>是</font>';
			$isgh= 1;
		}
		if($rs['isgh'] != $isgh)$this->update('`isgh`='.$isgh.'', $rs['id']);
		$rs['isgh'] = $fte;
		return $rs;
	}

	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$key 	= $this->rock->post('key');
		$dt 	= $this->rock->post('dt');
		if($key != '')$where.=" and (`bookname` like '%$key%' or `optname` like '%$key%')";
		if($dt != '')$where.=" and (`applydt`='$dt' or `jydt`='$dt')";
		
		return array(
			'where' => $where,
			'order' => 'optdt desc'
		);
	}
}