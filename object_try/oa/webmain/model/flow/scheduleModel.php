<?php
class flow_scheduleClassModel extends flowModel
{

	protected function flowinit(){
		$this->ratearr		 = array('d'=>'天','w'=>'周','m'=>'月');
	}
	

	public function flowrsreplace($rs)
	{
		$txsj = (int)$rs['txsj'];
		$str  = '不提醒';
		if($txsj==1)$str  = '提醒';
		$rs['txsj'] = $str;
		$rate = $rs['rate'];
		if(isset($this->ratearr[$rate])){
			$rs['rate'] = '每'.$this->ratearr[$rate];
		}
		return $rs;
	}
	
	protected function flowprintrows($rows)
	{
		foreach($rows as $k=>$rs){
			$rows[$k] = $this->flowrsreplace($rs);
		}
		return $rows;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$where	= 'and uid='.$uid.'';
		return array(
			'where' => $where,
			'order' => 'optdt desc'
		);
	}
}