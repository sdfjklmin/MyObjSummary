<?php
class flow_workClassModel extends flowModel
{

	protected function flowinit()
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
	
	protected function flowdatalog($arr)
	{
		$isaddlog	= 0;
		$distid 	= ','.$this->rs['distid'].',';
		$zt 		= $this->rs['stateid'];
		if($this->contain($distid, ','.$this->adminid.',') && ($zt==0||$zt==2)){
			$isaddlog = 1;
		}
		$arr['isaddlog'] = $isaddlog;
		$arr['state'] 	 = $this->rs['stateid'];
		return $arr;
	}
	
	protected function flowsubmit($na, $sm)
	{
		$this->push($this->rs['distid'], '任务', '[{type}]{title}');
	}
	
	protected function flowaddlog($a)
	{
		if($a['name']=='进度报告'){
			$state 	= $a['status'];
			$arr['state'] = $state;
			$cont = ''.$this->adminname.'添加【[{type}]{title}】的任务进度,说明:'.$a['explain'].'';
			if($state=='1')$cont='【[{type}]{title}】任务'.$this->adminname.'已完成';
			$this->push($this->rs['optid'], '任务', $cont);
			$this->update($arr, $this->id);
		}
		if($a['name']=='指派给'){
			$cname 	 = $this->rock->post('changename');
			$cnameid = $this->rock->post('changenameid');
			$state = '0';
			$arr['state'] 	= $state;
			$arr['distid'] 	= $cnameid;
			$arr['dist'] 	= $cname;
			$this->update($arr, $this->id);
			$this->push($cnameid, '任务', ''.$this->adminname.'指派任务[{type}]{title}给你');
		}
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$where 	= 'and '.$this->rock->dbinstr('distid', $uid);
		if($lx=='def' || $lx=='wwc'){
			$where.=' and state in(0,2)';
		}
		if($lx=='myall'){
			
		}
		if($lx=='all'){
			$where = '';
		}
		if($lx=='ywc'){
			$where.=' and state=1';
		}
		
		if($lx=='wcj'){
			$where = 'and optid='.$uid.'';
		}
		if($lx=='xxrw'){
			$where = 'and 1=2';
		}
		if($lx=='down'){
			$where  = 'and '.m('admin')->getdownwhere('`distid`', $uid, 1);
		}
		
		$key 	= $this->rock->post('key');
		$zt 	= $this->rock->post('zt');
		if($zt!='')$where.=' and `state`='.$zt.'';
		if(!isempt($key))$where.=" and (`title` like '%$key%' or `type` like '%$key%' or `grade` like '%$key%')";
		
		return array(
			'where' => $where,
			'fields'=> 'id,type,grade,dist,startdt,title,enddt,state,optname,projectid',
			'order' => '`optdt` desc'
		);
	}
}