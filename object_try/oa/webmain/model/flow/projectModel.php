<?php
class flow_projectClassModel extends flowModel
{

	public function initModel()
	{
		$this->statearr		 = c('array')->strtoarray('待执行|blue,已完成|green,执行中|#ff6600,终止|#888888');
	}
	
	protected function flowchangedata(){
		$this->rs['stateid'] = $this->rs['state'];
		$zt = $this->statearr[$this->rs['state']];
		$this->rs['state']	 = '<font color="'.$zt[1].'">'.$zt[0].'</font>';
	}
	
	protected function flowprintrows($rows)
	{
		foreach($rows as $k=>$rs){
			$zt = $this->statearr[$rs['state']];
			$rows[$k]['state']		= '<font color="'.$zt[1].'">'.$zt[0].'</font>';;
		}
		return $rows;
	}
	

	
	protected function flowaddlog($a)
	{
		if($a['name']=='进度报告'){
			$state 	= $a['status'];
			$arr['state'] = $state;
			$this->update($arr, $this->id);
		}
	}
	
	public function flowisreadqx()
	{
		return $this->flowgetoptmenu('');
	}
	
	//显示操作菜单判断
	protected function flowgetoptmenu($num)
	{
		$fuzeid 	= $this->rs['fuzeid'];
		$runuserid 	= $this->rs['runuserid'];
		$where 		= m('admin')->gjoin($fuzeid.','.$runuserid, 'ud', $blx='where');
		$where 		= 'id='.$this->adminid.' and ('.$where.')';
		$bo 		= true;
		if(m('admin')->rows($where)==0)$bo=false;
		return $bo;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$key 	= $this->rock->post('key');
		$zt 	= $this->rock->post('zt');
		$where	= 'and optid='.$uid.'';
		
		if($lx=='my' || $lx=='wwc'){
			$where = m('admin')->getjoinstr('runuserid', $uid);
		}
		
		if($lx=='wwc'){
			$where.=' and `state` in(0,2)';
		}
		
		if($lx=='myfz'){
			$where	= 'and '.$this->rock->dbinstr('fuzeid', $uid).'';
		}
		if($lx=='all'){
			$where	= '';
		}
		
		if($zt!='')$where.=' and `state`='.$zt.'';
		if(!isempt($key))$where.=" and (`title` like '%$key%' or `type` like '%$key%' or `fuze` like '%$key%')";
		
		return array(
			'where' => $where,
			'fields'=> 'id,type,num,fuze,startdt,title,enddt,state,optname,runuser,progress',
			'order' => 'pid,optdt desc'
		);
	}
}