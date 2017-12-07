<?php
class flow_assetmClassModel extends flowModel
{
	public function initModel()
	{
		$this->statearr = c('array')->strtoarray('blue|闲置,#ff6600|在用,red|维修,gray|报废,gray|丢失');
	}
	
	public function flowrsreplace($rs)
	{
		if(isset($rs['typeid']))$rs['typeid'] 	= $this->db->getmou('[Q]option','name',"`id`='".$rs['typeid']."'");
		if(isset($rs['ckid']))$rs['ckid'] 	= $this->db->getmou('[Q]option','name',"`id`='".$rs['ckid']."'");
		if(isset($this->statearr[$rs['state']])){
			$b 			 = $this->statearr[$rs['state']];
			$rs['state'] = '<font color="'.$b[0].'">'.$b[1].'</font>';
		}
		return $rs;
	}

	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$typeid = $this->rock->post('typeid','0');
		$key 	= $this->rock->post('key');
		if($typeid!='0'){
			$where .= ' and `typeid`='.$typeid.'';
		}
		if($key != '')$where.=" and `title` like '%$key%'";
		return array(
			'where' => $where,
			'order' => 'optdt desc',
			'fields'=> 'id,title,num,brand,optdt,usename,state'
		);
	}
}