<?php
class mode_leaveClassAction extends inputAction{

	protected function savebefore($table, $arr, $id, $addbo){
		$msg 	= m('kaoqin')->leavepan($arr['uid'], $arr['qjkind'], $arr['stime'], $arr['etime'], $arr['totals'], $id);
		return $msg;
	}
	
	public function totalAjax()
	{
		$start	= $this->post('stime');
		$end	= $this->post('etime');
		//$date	= c('date', true);
		//$sj		= $date->datediff('H', $start, $end);
		$sj 	= m('kaoqin')->getsbtime($this->adminid,$start, $end);
		$sj 	= ceil($sj);
		$this->returnjson(array($sj, ''));
	}
}	
			