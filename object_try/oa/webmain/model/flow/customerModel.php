<?php
class flow_customerClassModel extends flowModel
{
	public function initModel()
	{
		$this->statearr		 = c('array')->strtoarray('停用|#888888,启用|green');
	}
	

	
	public function flowrsreplace($rs)
	{
		$zt = $this->statearr[$rs['status']];
		$rs['status']	= '<font color="'.$zt[1].'">'.$zt[0].'</font>';
		if($rs['htshu']==0)$rs['htshu']='';
		if($rs['moneyz']==0)$rs['moneyz']='';
		if($rs['moneyd']==0)$rs['moneyd']='';
		return $rs;
	}
	
	
	protected function flowprintrows($rows)
	{
		foreach($rows as $k=>$rs){
			$zt = $this->statearr[$rs['status']];
			$rows[$k]['status']		= '<font color="'.$zt[1].'">'.$zt[0].'</font>';;
		}
		return $rows;
	}
	
	//是否有查看权限
	protected function flowisreadqx()
	{
		$bo = false;
		$shateid = ','.$this->rs['shateid'].',';
		if(contain($shateid,','.$this->adminid.','))$bo=true;
		return $bo;
	}
	
	protected function flowgetfields($lx)
	{
		$arr = array();
		if($this->uid==$this->adminid){
			$arr['mobile'] 		= '手机号';
			$arr['tel'] 		= '电话';
			$arr['email'] 		= '邮箱';
			$arr['routeline'] 	= '交通路线';
		}
		return $arr;
	}

	
	protected function flowoptmenu($ors, $crs)
	{
		$zt  = $ors['statusvalue'];
		$num = $ors['num'];
		if($num=='ztqh'){
			$this->update('`status`='.$zt.'', $this->id);
		}
		
		if($num=='shate'){
			$cname 	 = $crs['cname'];
			$cnameid = $crs['cnameid'];
			$this->update(array(
				'shateid' 	=> $cnameid,
				'shate' 	=> $cname,
			), $this->id);
			$this->push($cnameid, '客户管理', ''.$this->adminname.'将一个客户【{name}】共享给你');
		}	
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$where 	= '`uid`='.$uid.' and `status`=1';
		$key 	= $this->rock->post('key');
		$lxa 	= explode('_', $lx);
		$lxs 	= $lxa[0];
		if(isset($lxa[1]))$lx = $lxa[1];
		
		if($lxs=='my'){
			$where = '`uid`='.$uid.'';
		}
		if($lxs=='shatemy'){
			$where	= $this->rock->dbinstr('shateid', $uid);
		}
		if($lxs=='down'){
			$where = m('admin')->getdownwheres('uid', $uid, 1);
		}
		if($lxs=='dist'){
			$where = '`fzid`='.$uid.'';
		}
		if($lx=='qy'){
			$where.=' and `status`=1';
		}
		if($lx=='ting'){
			$where.=' and `status`=0';
		}
		if($lx=='stat'){
			$where.=' and `isstat`=1';
		}
		if($lx=='yfp'){
			$where.=' and `uid`>0';
		}
		if($lx=='wfp'){
			$where.=' and `uid`=0';
		}
		
		if($lx=='myty'){
			$where 	= '`uid`='.$uid.' and `status`=0';
		}
		
		if($lx=='myall'){
			$where 	= '`uid`='.$uid.'';
		}
		//共享给我
		if($lx=='gxgw'){
			$where	= $this->rock->dbinstr('shateid', $uid);
		}
		//我共享
		if($lx=='mygx'){
			$where 	= '`uid`='.$uid.' and `shateid` is not null';
		}
		
		//客户统计一览
		if($lx=='totolall'){
			$where = '1=1';
		}
		
		if(!isempt($key))$where.=" and (`name` like '%$key%' or `unitname` like '%$key%' or `optname`='$key')";
	
		return array(
			'where' => 'and '.$where,
			'fields'=> 'id,name,status,laiyuan,isgys,optname,unitname,shate,tel,type,adddt,moneyz,moneyd,htshu',
			'order' => 'status desc,optdt desc'
		);
	}
}