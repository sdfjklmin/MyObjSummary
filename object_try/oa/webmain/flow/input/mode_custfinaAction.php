<?php

class mode_custfinaClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		
		$narr	= array();
		$htid 	= (int)$arr['htid'];
		$money 	= floatval($arr['money']);
		if($money<=0)return '金额必须大于0';
		if($htid>0){
			$htrs = m('custract')->getone($htid);
			$narr['htnum'] 		= $htrs['num'];
			$narr['custid'] 	= $htrs['custid'];
			$narr['type'] 		= $htrs['type'];
			$narr['custname'] 	= $htrs['custname'];
			$zmoney				= floatval($htrs['money']);
			$omoney	= m('crm')->getmoneys($htid, $id);
			$chaojg	= $omoney + $money - $zmoney;
			if($chaojg>0)return '金额已超过合同上金额';
		}
		$narr['htid'] = $htid;
		return array('rows'=> $narr);
	}
	
		
	protected function saveafter($table, $arr, $id, $addbo){
		$htid 	= $arr['htid'];
		if($htid>0)m('crm')->ractmoney($htid);
	}
	
	public function selectcust()
	{
		$rows = m('crm')->getmycust($this->adminid);
		return $rows;
	}
	
	public function hetongdata()
	{
		$rows = m('crm')->getmyract($this->adminid, (int)$this->get('mid'));
		$arr  = array();
		foreach($rows as $k=>$rs){
			$arr[] = array(
				'value' => $rs['id'],
				'name' 	=> '['.$rs['num'].']'.$rs['custname'],
			);
		}
		return $arr;
	}
	
	public function ractchangeAjax()
	{
		$htid 	= (int)$this->get('ractid');
		$cars 	= m('custract')->getone($htid, 'id,custid,custname,money,type,num,signdt');
		$omoney	= m('crm')->getmoneys($htid);
		$cars['money'] = $cars['money']-$omoney;
		$this->returnjson($cars);
	}
}	
			