<?php

class flow_meetClassModel extends flowModel
{
	public function initModel()
	{
		$this->hyarra 	= array('正常','会议中','结束','取消');
		$this->hyarrb 	= array('green','blue','#ff6600','#888888');
		$this->dbobj	= c('date');
	}
	
	public function flowrsreplace($rs)
	{
		$rs['week']  = $this->dbobj->cnweek($rs['startdt']);
		$rs['state'] = $this->getstatezt($rs['state']);
		return $rs;
	}
	
	public function getstatezt($zt)
	{
		return '<font color="'.$this->hyarrb[$zt].'">'.$this->hyarra[$zt].'</font>';
	}
	
	protected function flowsubmit($na, $sm)
	{
		$cont  = '{optname}发起会议预定从{startdt}→{enddt},在{hyname},主题:{title}';
		$this->push($this->rs['joinid'], '会议', $cont);
	}
	
	protected function flowaddlog($a)
	{
		$actname = $a['name'];
		if($actname == '取消会议'){
			$this->push($this->rs['joinid'], '会议', ''.$this->adminname.'取消会议【{title}】{startdt}→{enddt}');
			$this->update('`state`=3', $this->id);
		}
		if($actname == '结束会议'){
			$this->update('`state`=2', $this->id);
		}
	}
	
	
	protected function flowbillwhere($uid, $lx)
	{
		$dt 	= $this->rock->post('dt');
		$key 	= $this->rock->post('key');
		
		$where	= 'and 1=2';
		if($lx=='my' || $lx=='mybz' || $lx=='myall'){
			$where	= m('admin')->getjoinstr('joinid', $uid);
		}
		$where	= 'and 1=1';
		if($lx=='my'){
			$where.=" and startdt like '{$this->rock->date}%'";
		}
		
		if($lx=='mybz'){
			$listdt	= c('date')->getweekfirst($this->rock->date);
			$where.=" and startdt >='$listdt'";
		}
		
		if($lx=='myfq'){
			$where =" and optid='$uid'";
		}
		
		m($this->mtable)->update('state=2',"`state`=0 and `enddt`<'{$this->rock->now}'");

		if($dt!='')$where.=" and startdt like '$dt%'";
		if(!isempt($key))$where.=" and (`joinname` like '%$key%' or `title` like '%$key%')";
		
		
		return array(
			'where' => "and type=0 and `status`=1 $where",
			'order' => 'startdt desc'
		);
	}
}