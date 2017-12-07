<?php
class mode_meetClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		return m('meet')->isapplymsg($arr['startdt'], $arr['enddt'], $arr['hyname'], $id);
	}
	

	protected function saveafter($table, $arr, $id, $addbo){
		
	}
}		