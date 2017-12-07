<?php
class finaClassModel extends Model
{

	public function initModel()
	{
		$this->settable('fininfom');
	}
	
	public function totaljie($uid, $id=0)
	{
		$where 	= 'and id<>'.$id.'';
		$to1  	= floatval($this->getmou('sum(money)money',"`uid`='$uid' and `type`=2 and `status`=1"));
		$to2  	= floatval($this->getmou('sum(money)money',"`uid`='$uid' and `type`=3 $where"));
		$to 	= $to1-$to2;
		return $to;
	}
}