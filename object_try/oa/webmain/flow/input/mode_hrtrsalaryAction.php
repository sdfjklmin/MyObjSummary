<?php

class mode_hrtrsalaryClassAction extends inputAction{

	protected function savebefore($table, $arr, $id, $addbo){
		$uid 	= $arr['uid'];
		$floats = (int)$arr['floats'];
		if($floats==0)return '调薪幅度不能为0';
		if(m($table)->rows("`uid`='$uid' and `id`<>'$id' and `status`<>1")>0)return '已有一单在申请中';
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
}	
			