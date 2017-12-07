<?php
class flow_userractClassModel extends flowModel
{

	public function initModel()
	{
		$this->statearr 	= explode(',','<font color=blue>待执行</font>,<font color=green>生效中</font>,<font color=#888888>已终止</font>,<font color=red>已过期</font>');
	}

	public function flowrsreplace($rs)
	{
		$rs['state']		= $this->statearr[$rs['state']];
		return $rs;
	}
	public function updatestate()
	{
		$dt 	= $this->rock->date;
		$this->update("`state`=1", "state=0 and `startdt`<='$dt' and `enddt`>='$dt'");
		$this->update("`state`=3", "state in(0,1) and `enddt`<'$dt'");
		$this->update("`state`=2", "state<>2 and `tqenddt` is not null");
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$dt 	= $this->rock->date;
		$this->updatestate();
		$key  	= $this->rock->post('key');
		$where 	= '';
		if($key!='')$where.=" and (b.deptallname like '%$key%' or b.name like '%$key%' or a.name like '%$key%' or a.httype like '%$key%')";
		if($lx == 'kdq'){//快到期
			$dt30 = c('date')->adddate($dt,'d',30);
			$where.=" and `enddt`<='$dt30' and `state`=1";
		}
		if($lx == 'sxz'){
			$where.=" and `state`=1";
		}
		if($lx == 'ygq'){
			$where.=" and `state`=3";
		}
		if($lx == 'yzz'){
			$where.=" and `state`=2";
		}
		$table 	= '`[Q]userract` a left join `[Q]admin` b on a.uid=b.id';
		return array(
			'where' => $where,
			'table'	=> $table,
			'fields'=> 'a.*,b.deptname',
			'order' => 'a.`optdt` desc'
		);
	}
}