<?php
class meetClassModel extends Model
{

	
	public function getmeet($dts, $uid=0)
	{
		if($uid==0)$uid = $this->adminid;
		$arr 	= array();
		$hyarr 	= array('<font color=green>正常</font>','<font color=blue>会议中</font>','<font color=#ff6600>结束</font>','<font color=#888888>取消</font>');
		
		$narr 	= $this->getall("`type`=0 and `status`=1 and `startdt` like '$dts%' order by `startdt` asc");
		$adb  	= m('admin');
		foreach($narr as $k=>$rs){
			if($adb->containjoin($rs['joinid'], $uid)){
				$zt 	= $rs['state'];
				$state 	= $hyarr[$zt];
				$dt 	= ''.str_replace($dts.' ', '', $rs['startdt']).'至'.str_replace($dts.' ', '', $rs['enddt']).'';
				$arr[]= array(
					'type' 		=> '会议',
					'hyname' 	=> $rs['hyname'],
					'title' 	=> '['.$rs['hyname'].']'.$rs['title'].'',
					'titles' 	=> $rs['title'],
					'joinname' 	=> $rs['joinname'],
					'state' 	=> $state,
					'status' 	=> $zt,
					'startdt' 	=> $dt,
					'starttime' => strtotime($rs['startdt']),
					'endtime' 	=> strtotime($rs['enddt']),
				);	
			}
		}
		return $arr;
	}
	
	public function isapplymsg($startdt, $enddt, $hyname, $id=0)
	{
		$msg 		= '';
		$where 		= "id <> '$id' and `hyname`='$hyname' and type=0 and ((`startdt`<'$startdt' and `enddt`>'$startdt') or (`startdt`<'$enddt' and `enddt`>'$enddt') or (`startdt`>'$startdt' and `enddt`<'$enddt') or (`startdt`='$startdt' and `enddt`='$enddt')) and `status` in(0,1)";
		if($this->rows($where)>0){
			$msg = '该会议室的时间段已经申请过了';
		}
		return $msg;
	}
}