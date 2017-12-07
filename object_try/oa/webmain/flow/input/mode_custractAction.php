<?php

class mode_custractClassAction extends inputAction{
	
	
	public function selectcust()
	{
		$rows = m('crm')->getmycust($this->adminid);
		return $rows;
	}
	
	
	
	
	public function selectsale()
	{
		$rows = m('crm')->getmysale($this->adminid, (int)$this->get('mid'));
		$arr  = array();
		foreach($rows as $k=>$rs){
			$arr[] = array(
				'value' => $rs['id'],
				'name' 	=> '['.$rs['laiyuan'].']'.$rs['custname'],
			);
		}
		return $arr;
	}
	
	public function salechangeAjax()
	{
		$saleid = (int)$this->get('saleid');
		$cars 	= m('custsale')->getone($saleid, 'id,custid,custname,money,laiyuan');
		$this->returnjson($cars);
	}
	
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		m('crm')->ractmoney($id); //计算未收/付款
		$saleid = (int)$arr['saleid'];
		$dbs 	= m('custsale');
		$dbs->update('htid=0', "`htid`='$id'");
		if($saleid > 0){
			$dbs->update('htid='.$id.'', "`id`='$saleid'");
		}
	}
}	
			