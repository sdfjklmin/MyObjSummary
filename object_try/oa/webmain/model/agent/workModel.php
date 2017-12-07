<?php

class agent_workClassModel extends agentModel
{
	
	public function gettotal()
	{
		$stotal	= $this->getwdtotal($this->adminid);
		$titles	= '';
		return array('stotal'=>$stotal,'titles'=> $titles);
	}
	
	private function getwdtotal($uid)
	{
		$to	= m('work')->getwwctotals($uid);
		return $to;
	}
	
	protected function agenttotals($uid){
		return array(
			'myrw' => $this->getwdtotal($uid)
		);
	}
	
	protected function agentrows($rows, $rowd, $uid)
	{
		$statea = $this->flow->statearr;
		foreach($rowd as $k=>$rs){
			$state 	 = $rs['state'];
			if($state==3){
				$rows[$k]['ishui']		=1;
			}
			$ztarr	 = $statea[$state];
			$rows[$k]['statustext']		= $ztarr[0];
			$rows[$k]['statuscolor']	= $ztarr[1];
		}
		return $rows;
	}
}