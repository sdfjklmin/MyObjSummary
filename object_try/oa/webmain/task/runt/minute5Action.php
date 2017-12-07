<?php 
class minute5ClassAction extends runtAction
{
	
	public function runAction()
	{
		$time 	= time();
		$time1 	= $time;
		$time2 	= $time1+5*60;
		$this->startdt	= date('Y-m-d H:i:s', $time1);	
		$this->enddt	= date('Y-m-d H:i:s', $time2);
		$this->scheduletodo();
		$this->meettodo();
		echo 'success';
	}
	
	private function scheduletodo()
	{
		m('schedule')->gettododata();
	}
	
	private function meettodo()
	{
		$db		= m('meet');
		$rows 	= $db->getall("`state` in(0,1) and `type`=0 and `startdt` like '".$this->date."%' and `status`=1");
		$time	= time();
		$adm	= m('admin');
		$flow	= m('flow')->initflow('meet');
		foreach($rows as $k=>$rs){
			$zt 	= $rs['state'];
			$dts	= explode(' ', $rs['startdt']);
			$sttime = strtotime($rs['startdt']);
			$ettime = strtotime($rs['enddt']);

			$nzt	= -1;
			if($ettime <= $time){
				$nzt = 2;
			}else{
				if($time >= $sttime && $time< $ettime){
					if($zt==0)$nzt = 1;
				}else{
					$jg = $sttime - $time;
					if($jg <= 305 && $zt==0){
						$tzuid = $adm->gjoin($rs['joinid']);
						$cont  = '['.$rs['title'].']会议将在5分钟后'.$dts[1].'开始，请做好准备,会议室['.$rs['hyname'].']';
						$flow->id = $rs['id'];
						$flow->push($tzuid, '', $cont);
					}
				}
			}
			if($nzt != -1)$db->update("`state`='$nzt'", $rs['id']);
		}
	}
}