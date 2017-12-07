<?php
class mode_jiabanClassAction extends inputAction{
	
	public function totalAjax()
	{
		$start	= $this->post('stime');
		$end	= $this->post('etime');
		$date	= c('date', true);
		$sj		= $date->datediff('H', $start, $end);
		$this->returnjson(array($sj, ''));
	}
}	
			