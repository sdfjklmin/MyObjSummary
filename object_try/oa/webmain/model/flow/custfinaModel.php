<?php

class flow_custfinaClassModel extends flowModel
{
	
	
	public function flowrsreplace($rs)
	{
		$starrr			= array('收','付');
		$ispay 			= '<font color=red>未'.$starrr[$rs['type']].'款</font>';
		if($rs['ispay']==1)$ispay = '<font color=green>已'.$starrr[$rs['type']].'款</font>';
		$rs['ispay']	 = $ispay;
		$rs['type']	 	 = ''.$starrr[$rs['type']].'款单';
		
		return $rs;
	}
	
	//操作菜单操作
	protected function flowoptmenu($ors, $arr)
	{
		//标识已付款处理
		if($ors['num']=='pay'){
			$ispay = 0;
			$paydt = $arr['fields_paydt'];
			if(!isempt($paydt))$ispay = 1;
			$this->update('ispay='.$ispay.'', $this->id);
			m('crm')->ractmoney($this->rs['htid']);
		}
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$key 	= $this->rock->post('key');
		$where 	= '`uid`='.$uid.'';
		$lxa 	= explode('_', $lx);
		$lx 	= $lxa[1];
		$lxs 	= $lxa[0];
		
		
		if($lxs=='myskd'){
			$where.=' and `type`=0';
		}
		if($lxs=='myfkd'){
			$where.=' and `type`=1';
		}
		//所有的
		if($lxs=='allskd'){
			$where='`type`=0';
		}
		if($lxs=='allfkd'){
			$where='`type`=1';
		}
		
		//下属
		if($lxs=='downskd'){
			$where = m('admin')->getdownwheres('uid', $uid, 1).'  and `type`=0';
		}
		if($lxs=='downfkd'){
			$where = m('admin')->getdownwheres('uid', $uid, 1).'  and `type`=1';
		}
		
		if($lx=='yi'){
			$where.=' and `ispay`=1';
		}
		if($lx=='wei'){
			$where.=' and `ispay`=0';
		}
		

		if(!isempt($key))$where.=" and (`custname` like '%$key%' or `htnum` ='$key' or `optname` ='$key')";
		
		return array(
			'where' => 'and '.$where,
			'order' => '`optdt` desc'
		);
	}
}