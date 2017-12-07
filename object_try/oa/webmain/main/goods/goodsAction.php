<?php
class goodsClassAction extends Action
{
	public function aftershow($table, $rows)
	{
		$typearr = array();
		if($rows)foreach($rows as $k=>$rs){
			$tid = $rs['typeid'];
			if(isset($typearr[$tid])){
				$rows[$k]['typeid']= $typearr[$tid];
			}else{
				$rows[$k]['typeid']		= $this->db->getpval('[Q]option','pid','name', $tid,'/','id',2);
				$typearr[$tid] = $rows[$k]['typeid'];
			}
		}
		$type	= (int)$this->post('type');
		$typearr= $this->option->getdata('kutype'.$type.'');
		return array('rows' => $rows,'typearr'=>$typearr);
	}
	
	public function beforeshow($table)
	{
		$key = $this->post('key');
		$s 	 = '';
		if($key!=''){
			$s = " and (`name` like '%$key%') ";
		}
		return $s;
	}
	
	public function xiangbeforeshow($table)
	{
		$key = $this->post('key');
		$dt  = $this->post('dt');
		$s 	 = '';
		if($key!=''){
			$s = " and b.`name` like '%$key%' ";
		}
		if($dt!=''){
			$s .= " and a.`applydt` like '$dt%' ";
		}
		$table	= '`[Q]goodss` a left join `[Q]goods` b on a.aid=b.id';
		$fields	= 'b.name,a.count,a.type,a.kind,a.status,a.optname,b.typeid,a.applydt,a.explain';
		return array(
			'where' => $s,
			'table' => $table,
			'fields' => $fields,
		);
	}
	
	public function xiangaftershow($table, $rows)
	{
		if($rows){
			$typearr0= $this->option->getdata('kutype0');
			$typearr1= $this->option->getdata('kutype1');
			$tyeparr = array();
			foreach($typearr0 as $k=>$rs)$tyeparr['a0_'.$rs['value'].''] = $rs['name'];
			foreach($typearr1 as $k=>$rs)$tyeparr['a1_'.$rs['value'].''] = $rs['name'];
			$statusar= array('<font color=blue>待审核</font>','<font color=green>已审核</font>','<font color=red>审核未通过</font>');
			$typearr = array();
			
			foreach($rows as $k=>$rs){
				
				$tid = $rs['typeid'];
				if(isset($typearr[$tid])){
					$rows[$k]['typeid'] = $typearr[$tid];
				}else{
					$rows[$k]['typeid']	= $this->db->getpval('[Q]option','pid','name', $tid,'/','id',2);
					$typearr[$tid] = $rows[$k]['typeid'];
				}
				
				$skey = 'a'.$rs['type'].'_'.$rs['kind'].'';
				$kind = '';
				if(isset($tyeparr[$skey]))$kind = $tyeparr[$skey];
				$rows[$k]['kind']	= $kind;
				$rows[$k]['status']	= $statusar[$rs['status']];
			}
		}
		return array('rows' => $rows);
	}
	
	public function chukuoptAjax()
	{
		$dt 	= $this->post('dt');
		$type 	= (int)$this->post('type');
		$kind 	= (int)$this->post('kind');
		$sm 	= $this->post('sm');
		$cont 	= $this->post('cont');
		$sharr	= c('array')->strtoarray($cont);
		$arr['applydt'] = $dt;
		$arr['type'] 	= $type;
		$arr['kind'] 	= $kind;
		$arr['explain'] = $sm;
		$arr['uid'] 	= $this->adminid;
		$arr['optid'] 	= $this->adminid;
		$arr['optdt'] 	= $this->now;
		$arr['optname'] = $this->adminname;
		$arr['status'] 	= 1;
		$aid 			= '0';
		foreach($sharr as $k=>$rs){
			$arr['aid'] = $rs[0];
			$count = $rs[1];
			if($type==1)$count = 0-$count;
			$arr['count'] = $count;
			$this->db->record('[Q]goodss', $arr);
			$aid.=','.$rs[0].'';
		}
		if($aid!='0')m('goods')->setstock($aid);
		echo 'success';
	}
	
	
	
	
	
	
	
	
	
	public function addplgoodsAjax()
	{
		$rows  	= c('html')->importdata('name,typeid,price,unit,guige,xinghao','name,typeid');
		$oi 	= 0;
		$db 	= m('goods');
		foreach($rows as $k=>$rs){
			$rs['typeid'] 	= $this->gettypeid($rs['typeid']);
			$odi 			= $db->rows("`typeid`=".$rs['typeid']." and `name`='".$rs['name']."'");
			if($odi>0)continue;
			$rs['price']	= floatval($rs['price']);
			$rows[$k]		= $rs;
			$rs['adddt']	= $this->now;
			$rs['optdt']	= $this->now;
			$rs['optid']	= $this->adminid;
			$rs['optname']	= $this->adminname;
			$db->insert($rs);
			$oi++;
		}
		backmsg('','成功导入'.$oi.'条数据');
	}
	private	$getypsarr = array();
	private function gettypeid($s)
	{
		if(isset($this->getypsarr[$s]))return $this->getypsarr[$s];
		$sid = 0;
		$s 	 = str_replace(',','/', $s);
		$djid= $this->option->getval('goodstype','0',2);
		$dsja= $djid;
		$sarr= explode('/', $s);
		foreach($sarr as $safs){
			$pid 	= $djid;
			$djid 	= (int)$this->option->getmou('id',"`pid`='$pid' and `name`='$safs'");
			if($djid==0){
				$djid = $this->option->insert(array(
					'name' => $safs,
					'pid'  => $pid,
					'valid'  => 1,
				));
			}
		}
		if($djid != $dsja)$sid 	= $djid;
		$this->getypsarr[$s] 	= $sid;
		return $sid;
	}
}