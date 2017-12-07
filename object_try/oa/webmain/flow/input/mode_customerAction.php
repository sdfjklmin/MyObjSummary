<?php
 
class mode_customerClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		$name = $arr['name'];
		m('custfina')->update("`custname`='$name'", "`custid`='$id'");
		m('custract')->update("`custname`='$name'", "`custid`='$id'");
		m('custsale')->update("`custname`='$name'", "`custid`='$id'");
	}
}	
			