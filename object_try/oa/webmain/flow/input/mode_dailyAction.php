<?php
class mode_dailyClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		
		$type 	= $arr['type'];
		$uid 	= $arr['uid'];
		$dt 	= $arr['dt'];
		$enddt 	= $arr['enddt'];
		$where  = "id<>$id and `uid`=$uid and `type`='$type' and `dt`='$dt'";
		if(!isempt($enddt))$where.=' and `enddt`='.$enddt.'';
		if($type==0 && $dt>$this->date)return '日期['.$dt.']还是个未来呢';
		$to 	= $this->mdb->rows($where);
		if($to>0)return '该类型日期['.$dt.']段已申请了';
	}
	
		
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	public function getdtstrAjax()
	{
		$type 	= (int)$this->post('type');
		$dt 	= $this->post('dt');
		$dta 	= explode('-', $dt);
		$dtobj	= c('date');
		$startdt= $dt;
		$enddt  = '';
		
		if($type==1){
			$dtw = $dtobj->getweekarr($dt);
			$startdt= $dtw[0];
			$enddt  = $dtw[6];
		}
		if($type==2){
			$startdt= ''.$dta[0].'-'.$dta[1].'-01';
			$enddt= $dtobj->getenddt($dt);
		}
		if($type==3){
			$startdt= ''.$dta[0].'-01-01';
			$enddt	=  ''.$dta[0].'-12-31';
		}
		$a = array($startdt, $enddt);
		$this->returnjson($a);
	}
}	
			