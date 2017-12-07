<?php
class scheduleClassAction extends Action
{
	public function loadscheduleAjax()
	{
		$month	= $this->request('month');
		$startdt= ''.$month.'-01';
		$endddt	= c('date')->getenddt($month);
		$arr 	= m('schedule')->getlistdata($this->adminid, $startdt, $endddt);
		$this->returnjson($arr);
	}
	
	public function schedulebefore($table)
	{
		$s 		= 'and `uid`='.$this->adminid.'';
		$dt 	= $this->post('dt1');
		$key 	= $this->post('key');
		if($dt!='')$s.=" and `startdt` like '$dt%'";
		if($key!='')$s.=" and `title` like '%$key%'";
		return $s;
	}
	
	public function scheduleafter($table, $rows)
	{
		foreach($rows as $k=>$rs){
			$rate = $rs['rate'];
			if($rate=='w')$rate='每周'.$rs['rateval'].'';
			if($rate=='d')$rate='每天';
			if($rate=='m')$rate='每月'.$rs['rateval'].'号';
			$rows[$k]['rate'] = $rate;
		}
		return array('rows'=>$rows);
	}
}