<?php
class fworkClassAction extends Action
{
	
	public function getmodearrAjax()
	{
		$rows = m('mode')->getmoderows($this->adminid,'and islu=1');
		$row  = array();
		$viewobj = m('view');
		foreach($rows as $k=>$rs){
			$lx = $rs['type'];
			if(!$viewobj->isadd($rs['id'], $this->adminid))continue;
			if(!isset($row[$lx]))$row[$lx]=array();
			$row[$lx][] = $rs;
		}
		$this->returnjson(array('rows'=>$row));
	}
	
	
	
	
	
	
	public function flowbillbefore($table)
	{
		$lx 	= $this->post('atype');
		$dt 	= $this->post('dt1');
		$key 	= $this->post('key');
		$zt 	= $this->post('zt');
		$modeid = (int)$this->post('modeid','0');
		$uid 	= $this->adminid;
		$where	= 'and a.uid='.$uid.'';
		//待办
		if($lx=='daib'){
			$where	= 'and a.`status`=0 and '.$this->rock->dbinstr('a.nowcheckid', $uid);
		}
		
		if($lx=='xia'){
			$where	= 'and '.$this->rock->dbinstr('b.superid', $uid);
		}
		
		if($lx=='jmy'){
			$where	= 'and '.$this->rock->dbinstr('a.allcheckid', $uid);
		}
		
		if($lx=='mywtg'){
			$where.=" and a.status=2";
		}
		
		if($zt!='')$where.=" and a.status='$zt'";
		if($dt!='')$where.=" and a.applydt='$dt'";
		if($modeid>0)$where.=' and a.modeid='.$modeid.'';
		if(!isempt($key))$where.=" and (b.`name` like '%$key%' or b.`deptname` like '%$key%' or a.sericnum like '$key%')";
		
		
		return array(
			'table' => '`[Q]flow_bill` a left join `[Q]admin` b on a.uid=b.id',
			'where' => " and a.isdel=0 $where",
			'fields'=> 'a.*,b.name,b.deptname',
			'order' => 'a.optdt desc'
		);
	}
	
	public function flowbillafter($table, $rows)
	{
		$rows = m('flowbill')->getbilldata($rows);
		return array(
			'rows'		=> $rows,
			'flowarr' 	=> m('mode')->getmodemyarr($this->adminid)
		);
	}
	

}