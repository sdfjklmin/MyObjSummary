<?php
class salaryClassAction extends Action
{
	public function biaoshiffAjax()
	{
		$sid = $this->post('sid');
		if($sid=='')return;
		m('flow')->initflow('hrsalary')->gongzifafang($sid);
	}
	
	public function createdataAjax()
	{
		$month = $this->post('month');
		if($month=='')return;
		$lastdt = c('date')->getenddt($month);
		if($lastdt>$this->date)exit(''.$month.'月份超前了');
		$barr 	= m('flow')->initflow('hrsalary')->createdata($month);
		echo $barr;
	}
}