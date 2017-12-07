<?php
/**
	通知公告的
*/
class agent_gongClassModel extends agentModel
{
	
	public function gettotal()
	{
		$stotal	= $this->getwdtotal($this->adminid);
		$titles	= '';
		return array('stotal'=>$stotal,'titles'=> $titles);
	}
	
	private function getwdtotal($uid)
	{
		$ydid 	= m('log')->getread('infor', $uid);
		$where	= "id not in($ydid)";
		$meswh	= m('admin')->getjoinstr('receid', $uid);
		$where .= $meswh;
		$stotal	= m('infor')->rows($where);
		return $stotal;
	}
	
	
	protected function agenttotals($uid)
	{
		$a = array(
			'weidu' => $this->getwdtotal($uid)
		);
		return $a;
	}
	protected function agentrows($rows, $rowd, $uid){
		if($rows){
			$ydarr	= explode(',', m('log')->getread('infor', $uid));
			foreach($rowd as $k=>$rs){
				if(!in_array($rs['id'], $ydarr)){
					$rows[$k]['statustext'] 	= '未读';
					$rows[$k]['statuscolor'] 	= '#ED5A5A';
				}else{
					$rows[$k]['ishui']			= 1;
				}
			}
		}
		return $rows;
	}
}