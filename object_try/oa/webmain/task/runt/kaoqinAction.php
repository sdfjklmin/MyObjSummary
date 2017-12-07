<?php
class kaoqinClassAction extends runtAction
{
	/**
	*	定时任务发送昨天考勤异常的啊
	*/
	public function todoAction()
	{
		$dt 	= date('Y-m-d', time()-3600*20);//昨天
		$sql  	= "SELECT a.uid FROM `[Q]kqanay` a left join `[Q]userinfo` b on a.uid=b.id where a.dt='$dt' and b.iskq=1 and a.state<>'正常' and a.states is null and a.iswork=1 group by a.uid;";
		$rows 	= $this->db->getall($sql);
		$ids 	= '';
		foreach($rows as $k=>$rs){
			$ids .=','.$rs['uid'].'';
		}
		if($ids!=''){
			$flow 	= m('flow')->initflow('leavehr');
			$flow->push(substr($ids, 1),'','昨天['.$dt.']的你考勤存在异常，此消息仅供参考！','考勤异常提醒');
		}
		echo 'success';
	}
	
	public function anayAction()
	{
		$dt 	= date('Y-m-d', time()-3600*20);//昨天
		m('kaoqin')->kqanayalldt($dt);
		echo 'success';
	}
}