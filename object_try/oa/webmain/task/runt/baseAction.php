<?php
//访问地址：task.php?m=base|runt&a=run
class baseClassAction extends runtAction
{
	public function runAction()
	{
		$dbs 	= m('task');
		$mid 	= (int)$this->get('mid');
		$where  = '';
		if($mid>0)$where='and id='.$mid.'';
		$rows 	= $dbs->getrows('`status`=1 '.$where.' order by `sort`','`id`,`url`,`type`,`time`,`startdt`');
		$dt 	= $this->date;
		$gtimes	= $this->get('time', date('H:i:s'));
		$time  	= strtotime($dt.' '.$gtimes);
		$jgs	= 5;
		$curl 	= c('curl');
		foreach($rows as $k=>$rs){
			$ybbo 	= false;
			$stime	= $this->getruntime($dt, $rs['type'], $rs['time'], $rs['startdt']);
			if($stime > 0){
				if($time>=$stime-$jgs && $time<=$stime+$jgs){
					$ybbo = true;
				}
			}
			if($ybbo || $mid>0){
				$state 	= '2';
				$url 	= $this->showgeurl($rs['url']);
				$cont 	= $curl->getcurl($url);
				if($cont=='success')$state='1';
				$dbs->update(array(
					'state' => $state,
					'lastdt'=> $this->now,
					'lastcont' => $cont
				), $rs['id']);
			}
		}
		echo 'success';
	}
	
	private function getruntimes($dt, $type, $time, $startdt)
	{
		$stime	= 0;
		$sj 	= '';
		if($type=='d'){
			$sj = $dt.' '.$time;
		}
		if($type=='h'){
			
		}
		if($sj!=''){
			$stime = strtotime($sj);
		}
		return $stime;
	}
	
	private function getruntime($dt, $type, $time, $startdt)
	{
		$stime	= 0;
		if(!$this->isempt($type) && !$this->isempt($time)){
			$atype = explode(',', $type);
			$atime = explode(',', $time);
			$len1  = count($atype);
			$len2  = count($atime);
			if($len1 == $len2){
				for($i=0; $i<$len1; $i++){
					$stime = $this->getruntimes($dt, $atype[$i], $atime[$i], $startdt);
					if($stime>0)break;
				}
			}
		}
		return $stime;
	}
	
	private function showgeurl($url)
	{
		if(!$this->contain($url, 'http://')){
			$aurl 	= explode(',', $url);
			$turl	= getconfig('hosturl');
			if($turl=='')$turl = URL;
			$url 	= ''.$turl.'task.php?m='.$aurl[0].'|runt&a='.$aurl[1].'';
		}
		return $url;
	}
}