<?php
class agent_scheduleClassModel extends agentModel
{
	public function gettotal()
	{
		return array('stotal'=>0,'titles'=>'');
	}
	
	protected function agentrows($rows, $rowd, $uid)
	{
		foreach($rowd as $k=>$rs){
			if(!isempt($rs['enddt']) && $rs['enddt']<$this->rock->now){
				$rows[$k]['ishui'] = 1;
			}
		}
		return $rows;
	}
}