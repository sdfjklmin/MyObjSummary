<?php

class mode_carmreseClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		$msg 	 	= '';
		$startdt 	= $arr['startdt'];
		$enddt 		= $arr['enddt'];
		$carid		= $arr['carid'];
		if($startdt>=$enddt)$msg='截止时间小于开始时间，不科学啊';
		if($msg==''){
			$where = "id <>'$id' and `carid` = '$carid' and `status` in(0,1) and ((`startdt`<='$startdt' and `enddt`>='$startdt') or (`startdt`<='$enddt' and `enddt`>='$enddt') or (`startdt`>='$startdt' and `enddt`<='$enddt'))";
			if(m($table)->rows($where)>0)$msg='车辆该时间段已被预定了';
		}
		return array('msg'=>$msg);
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	public function getcardata()
	{
		$rows = m('carm')->getall("`ispublic`=1 and `state`=1",'carnum as name,id as value');
		return $rows;
	}
}	
			