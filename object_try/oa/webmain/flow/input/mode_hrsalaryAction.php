<?php

class mode_hrsalaryClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		$month 	= $arr['month'];
		$lastdt = c('date')->getenddt($month);
		if($lastdt>$this->date)return ''.$month.'月份超前了';
		$uname 	= $arr['uname'];
		$xuid 	= $arr['xuid'];
		if(m($table)->rows('id<>'.$id.' and `xuid`='.$xuid.' and `month`=\''.$month.'\'')>0)return ''.$month.'月份['.$uname.']的薪资已申请过了'.$lastdt.'';
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	public function changeunameAjax()
	{
		$xuid = (int)$this->post('xuid');
		$a 	 = m('admin')->getone($xuid,'deptname,ranking');
		$this->returnjson($a);
	}
	
	
	/**
	*	薪资初始化，主要计算考勤
	*/
	public function initdatasAjax()
	{
		$xuid 	= (int)$this->post('xuid');
		$month 	= $this->post('month');
		$a		= m('kaoqin')->getkqtotal($xuid, $month);
		$lmonth	= c('date')->adddate($month.'-01','m',-1,'Y-m');
		$sfielss= 'base,postjt,skilljt,travelbt,telbt,socials';
		$lrs 	= m('hrsalary')->getone("`xuid`='$xuid' and `month`='$lmonth'", $sfielss);
		$sm 	= '';
		if($lrs){
			$sfiels 	= explode(',',$sfielss);
			foreach($sfiels as $sfie)$a[$sfie] = $lrs[$sfie];
		}
		$urs 	= m('admin')->getone($xuid,'quitdt,workdate');
		if(contain($urs['workdate'],$month))$sm.=''.$urs['workdate'].'入职;';
		if(contain($urs['quitdt'],$month))$sm.=''.$urs['quitdt'].'离职;';
		$txrs 	= m('hrtrsalary')->getone("`uid`='$xuid' and `effectivedt` like '$month%' and `status`=1");
		if($txrs)$sm.=''.$txrs['effectivedt'].'起调薪'.$txrs['floats'].';';
		
		$a['reward'] = floatval(m('reward')->getmou('sum(money)', "`objectid`='$xuid' and `status`=1 and `type`=0 and `applydt` like '$month%'"));
		$a['punish'] = floatval(m('reward')->getmou('sum(money)', "`objectid`='$xuid' and `status`=1 and `type`=1 and `applydt` like '$month%'"));
		
		if($sm!='')$a['explain'] = $sm;
		$this->returnjson($a);
	}
}	
			