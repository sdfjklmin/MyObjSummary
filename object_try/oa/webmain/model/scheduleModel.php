<?php
class scheduleClassModel extends Model
{
	private $datarows = array();
	
	public function getlistdata($uid, $startdt, $enddt, $whe='')
	{
		$arr 		= array();
		$dtobj		= c('date');
		$jg			= $dtobj->datediff('d', $startdt, $enddt)+1;
		$where 		= '';
		if($uid>0)$where = 'and `uid`='.$uid.'';
		$sql 		= "select id,title,enddt,startdt,rate,rateval,`uid` from `[Q]schedule` where `status`=1 $where $whe and `startdt`<'$enddt 23:59:59' and (`enddt` is null or `enddt`>'$startdt')";
		$rows 		= $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			$endtime = 2999999999;
			if(!isempt($rs['enddt']))$endtime = strtotime($rs['enddt']);
			$rows[$k]['endtime'] 	= $endtime;
			$rows[$k]['starttime'] 	= strtotime($rs['startdt']);
			$rows[$k]['time']  		= explode('-', date('Y-m-d-H-i-s', $rows[$k]['starttime']));
		}
		for($i=0;$i<$jg; $i++){
			if($i==0)$dt= $startdt;
			if($i>0)$dt = $dtobj->adddate($dt,'d', 1);
			$dttime = strtotime($dt);
			$dta	= explode('-', $dt);
			$_d 	= (int)$dta[2];
			$nw 	= (int)date('w', $dttime);
			$row 	= array();
			foreach($rows as $k=>$rs){
				$rate = $rs['rate'];
				$uid  = $rs['uid'];
				$ratev= ','.$rs['rateval'].',';
				if($rs['endtime']<$dttime)continue;
				$dts  = $rs['time'];
				$time = '';
				if($rate=='d'){
					$time = ''.$dt.' '.$dts[3].':'.$dts[4].':00';
				}else if($rate=='m'){
					if(contain($ratev,','.$_d.',')){
						$time = ''.$dt.' '.$dts[3].':'.$dts[4].':00';
					}
				}else if($rate=='w'){
					if(contain($ratev,','.$nw.',')){
						$time = ''.$dt.' '.$dts[3].':'.$dts[4].':00';
					}
				}else{
					if(contain($rs['startdt'], $dt))$time=$rs['startdt'];
				}
				if($time!=''){
					$barsa = array('id'=>$rs['id'],'title'=>$rs['title'],'time'=>$time);
					$row[] = $barsa;
					if(!isset($this->datarows[$uid]))$this->datarows[$uid] = array();
					$this->datarows[$uid][] = $barsa;
				}
			}
			$arr[$dt] = $row;
		}
		return $arr;
	}
	
	public function gettododata($dt='')
	{
		if($dt=='')$dt=$this->rock->date;
		$this->datarows = array();
		$this->getlistdata(0, $dt, $dt,'and txsj=1');
		$barr = $this->datarows;
		$time = time();
		$flow = m('flow')->initflow('schedule');
		foreach($barr as $uid=>$rows){
			$str ='';
			$sid = 0;
			foreach($rows as $k=>$rs){
				$txsj 	= strtotime($rs['time']);
				$jg 	= $txsj-$time;
				if($jg <= 305 && $jg>0){
					$str   .= ','.$rs['title'];
					$sid 	= $rs['id'];
				}
			}
			if($str!=''){
				$flow->id = $sid;
				$flow->push($uid, '', substr($str, 1));
			}
		}
	}
}