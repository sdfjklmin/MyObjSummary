<?php

class flow_custsaleClassModel extends flowModel
{
	public function initModel(){
		$this->statearr		 = c('array')->strtoarray('跟进中|blue,已成交|green,已丢失|#888888');
	}
	

	
	public function flowrsreplace($rs)
	{
		$zt = $this->statearr[$rs['state']];
		$rs['state']	 = '<font color="'.$zt[1].'">'.$zt[0].'</font>';
		if($rs['htid']>0)$rs['state'].=',<font color=#888888>并建立合同</font>';
		return $rs;
	}
	
	protected function flowoptmenu($ors, $crs)
	{
		$zt  = $ors['statusvalue'];
		$num = $ors['num'];
		if($num=='ztqh'){
			$sarr['state'] = $zt;
			if($zt==1)$sarr['dealdt'] = $this->rock->now;	
			$this->update($sarr, $this->id);
		}
		
		if($num=='zhuan'){
			$cname 	 = $crs['cname'];
			$cnameid = $crs['cnameid'];
			$this->update(array(
				'uid' 		=> $cnameid,
				'optname' 	=> $cname
			), $this->id);
			$this->push($cnameid, '客户销售', ''.$this->adminname.'将一个客户【{custname}】的一个销售单转移给你');
		}	
	}
	
	protected function flowprintrows($rows)
	{
		foreach($rows as $k=>$rs){
			$zt = $this->statearr[$rs['state']];
			$rows[$k]['state']		= '<font color="'.$zt[1].'">'.$zt[0].'</font>';;
		}
		return $rows;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$key 	= $this->rock->post('key');
		$zt 	= $this->rock->post('zt');
		$where 	= '`uid`='.$uid.'';
		if($lx=='down'){
			$where = m('admin')->getdownwheres('uid', $uid, 1);
		}
		
		if($lx=='def'){
			$where.=' and `state`=0';
		}
		if($lx=='saleycj'){
			$where 	= '`uid`='.$uid.' and `state`=1';
		}
		if($lx=='saleyds'){
			$where 	= '`uid`='.$uid.' and `state`=2';
		}
		if($lx=='saleall'){
			$where 	= '`uid`='.$uid.'';
		}
		
		if($zt!='')$where.=" and `state`='$zt'";
		if(!isempt($key)){
			$where.=" and (`custname` like '%$key%' or `optname`='$key')";
		}
		return array(
			'where' => 'and '.$where,
			'fields'=> 'id,custname,laiyuan,optname,state,money,optdt,createname,`explain`,htid',
			'order' => '`state`,`optdt` desc'
		);
	}
}