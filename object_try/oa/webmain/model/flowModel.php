<?php
class flowClassModel extends Model
{
	public $flow = null;
	public function initflow($num,$mid=null, $isqx=true)
	{
		$this->flow = m('flow:'.$num.'');
		$this->flow->initdata($num);
		if($mid != null)$this->flow->loaddata($mid, $isqx);
		return $this->flow;
	}
	
	
	public function opt($act,$num,$mid,$cs1='',$cs2='',$cs3='',$cs4='')
	{
		$this->initflow($num, $mid);
		return $this->flow->$act($cs1, $cs2, $cs3, $cs4);
	}
	
	public function getdatalog($num, $mid, $lx)
	{
		return $this->opt('getdatalog', $num, $mid, $lx);
	}
	
	public function submit($num, $mid, $na='', $sm='')
	{
		$this->initflow($num,$mid, false);
		return $this->flow->submit($na, $sm);
	}
	
	public function deletebill($num, $mid, $sm='')
	{
		$this->initflow($num,$mid, false);
		return $this->flow->deletebill($sm);
	}
	
	public function getoptmenu($num, $mid, $lx=0)
	{
		$this->initflow($num,$mid, false);
		return $this->flow->getoptmenu($lx);
	}
	
	public function optmenu($num, $mid, $optid, $zt, $sm)
	{
		$this->initflow($num,$mid, false);
		return $this->flow->optmenu($optid, $zt, $sm);
	}
	
	public function getdataedit($num, $mid)
	{
		return $this->opt('getdataedit', $num, $mid);
	}
	
	public function addlog($num, $mid,$na,$barr=array())
	{
		$darr = array(
			'name' 			=> $na
		);
		foreach($barr as $k=>$v)$darr[$k]=$v;
		return $this->opt('addlog', $num, $mid, $darr);
	}
	
	public function printexecl($num, $event)
	{
		return $this->opt('printexecl', $num, null, $event);
	}
	
	public function repipei($whe='')
	{
		$srows 	= $this->db->getrows('[Q]flow_set','status=1 and isflow=1 '.$whe.'','`num`,`name`,`table`,id,`where`','sort');
		$str 	= '';
		foreach($srows as $k=>$rs){
			$where = $rs['where'];
			if(!isempt($where)){
				$where = $this->rock->covexec($where);
				$where = "and $where";
			}
			$flow = $this->initflow($rs['num']);
			$rows = $this->db->getrows('[Q]'.$rs['table'].'','status in(0,2) and isturn=1 '.$where.'');
			$hshu = 0;
			foreach($rows as $k1=>$rs1){
				$flow->loaddata($rs1['id'], false);
				$flow->getflow(true);
				$hshu+=$this->db->row_count();
			}
			if($hshu>0)$str.=''.$rs['name'].'匹配('.$hshu.')条;';
		}
		if($str=='')$str = '无从新匹配记录';
		return $str;
	}
}