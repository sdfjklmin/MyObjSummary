<?php
class wordClassAction extends Action
{
	
	
	public function getmywordtypeAjax()
	{
		$pid 	= m('word')->getfolderid($this->adminid);
		$rows 	= m('word')->getfoldrows($this->adminid);
		$rows	= array(
			'rows' 	=> $rows,
			'pid'	=> $pid
		);
		$this->returnjson($rows);
	}
	
	public function getwordAjax()
	{
		$typeid = (int)$this->post('typeid',0);
		$pid 	= m('word')->getfolderid($this->adminid);
		$where 	= " and a.optid=".$this->adminid."";
		if($pid==$typeid || $typeid==0){
			//$where.=" and a.typeid in(0,$pid)";
		}else{
			$where.=" and a.typeid='$typeid'";
		}
		$rows 	= $this->db->getall("select b.id,a.shate,a.typeid,b.filepath,a.optname,a.optid,a.optdt,b.filename,b.fileext,b.filesizecn,b.downci from `[Q]word` a left join `[Q]file` b on a.fileid=b.id where b.id is not null $where order by a.`id` desc");
		$this->returnjson(array(
			'rows' => $rows
		));
	}
	
	public function savefileAjax()
	{
		$typeid = (int)$this->post('typeid',0);
		$sid 	= $this->post('sid');
		$sadid	= explode(',', $sid);
		
		$arr['optid'] 	= $this->adminid;
		$arr['optname'] = $this->adminname;
		$arr['optdt'] 	= $this->now;
		$arr['typeid'] 	= $typeid;
		$file 			= m('file');
		foreach($sadid as $fid){
			$arr['fileid'] = $fid;
			$sid = m('word')->insert($arr);
			$file->addfile($fid, 'word', $sid);
		}
		echo 'ok';
	}
	
	public function sharefileAjax()
	{
		$fileid 		= $this->post('fid','0');
		$arr['shateid'] = $this->post('sid');
		$arr['shate']   = $this->post('sna');
		m('word')->update($arr, "optid='$this->adminid' and fileid in($fileid)");
	}
	
	
	public function shatebefore($talbe)
	{
		$key	= $this->post('key');
		$atype	= $this->post('atype');
		$where  = m('admin')->getjoinstrs('a.shateid', $this->adminid, 1);
		if($atype=='wfx')$where 	= " and a.optid=".$this->adminid." and a.shate is not null";
		if($key!=''){
			$where.=" and (a.`optname` like '%$key%' or b.filename like '%$key%')";
		}
		return array(
			'table' => '`[Q]word` a left join `[Q]file` b on a.fileid=b.id',
			'where' => 'and b.id is not null '.$where.'',
			'fields'=> 'b.id,a.shate,a.typeid,a.optname,a.optid,b.filepath,a.optdt,b.filename,b.fileext,b.filesizecn,b.downci',
			'order' => 'a.id desc'
		);
	}
	
	public function delwordAjax()
	{
		$fid	= $this->post('id','0');
		m('word')->delete("`optid`='$this->adminid' and `fileid`='$fid'");
		m('file')->delfile($fid);
		backmsg();
	}

}