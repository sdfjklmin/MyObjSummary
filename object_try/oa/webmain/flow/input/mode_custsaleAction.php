<?php

class mode_custsaleClassAction extends inputAction{
	
	public function selectcust()
	{
		$rows = m('crm')->getmycust($this->adminid);
		return $rows;
	}
}	
			