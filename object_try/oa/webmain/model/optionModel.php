<?php
class optionClassModel extends Model
{
	/**
		获取选项
	*/
	public function getval($num, $dev='', $lx=0)
	{
		$val= '';
		$rs = $this->getone("`num`='$num'", '`name`,`value`,`id`');
		if($rs){
			if($lx==0)$val=$rs['value'];
			if($lx==1)$val=$rs['name'];
			if($lx==2)$val=$rs['id'];
		}
		if($this->rock->isempt($val))$val=$dev;
		return $val;
	}
	
	public function getdata($num, $whe='')
	{
		if(!is_numeric($num)){
			$id  = (int)$this->getmou('id', "`num`='$num'");
			if($id == 0)$id = -1;
		}else{
			$id = $num;
		}
		return $this->getall("`pid`='$id' and `valid`=1 $whe order by `sort`,`id`");
	}
	
	public function getmnum($num)
	{
		return $this->getdata($num);
	}
	
	public function getselectdata($num, $tbo=false)
	{
		$arr = $this->getdata($num);
		$rows= array();
		foreach($arr as $k=>$rs){
			$rows[] = $rs;
			if($tbo){
				$sarr = $this->getdata($rs['id']);
				foreach($sarr as $k1=>$rs1){
					$rs1['name'] = '&nbsp;&nbsp;├'.$rs1['name'].'';
					$rows[] = $rs1;
				}
			}
		}
		return $rows;
	}
	
	public function setval($num, $val='', $name=null, $isub=true)
	{
		$where  = "`num`='$num'";
		$id 	= (int)$this->getmou('id', $where);
		if($id==0)$where='';
		$arr 	= array(
			'num'	=> $num,
			'value'	=> $val,
			'optid'	=> $this->adminid,
			'optdt'	=> $this->rock->now
		);
		if($name!=null)$arr['name'] = $name;
		if($id==0 || $isub)$this->record($arr, $where);
		if($id==0)$id = $this->db->insert_id();
		return $id;
	}
	
	
	public function gettreedata($pid)
	{
		$rows 	= $this->getfoldrowsss($pid);
		return $rows;
	}
	
	private function getfoldrowsss($pid)
	{
		$rows 	= $this->db->getall("select `id`,`pid`,`name`,`optdt`,`sort` from [Q]option where `pid`='$pid' and `valid`=1 order by `sort`,`id`");
		foreach($rows as $k=>$rs){
			$rows[$k]['expanded']	= true;
			$rows[$k]['children'] 	= $this->getfoldrowsss($rs['id']);
		}
		return $rows;
	}
	
	public function getnumtoid($num, $name='', $isub=true)
	{
		$idd = $this->setval($num,'', $name, $isub);
		return $idd;
	}
}