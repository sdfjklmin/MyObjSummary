<?php
class gerenClassAction extends Action
{
	public function filebefore($table)
	{
		$key	= $this->post('key');
		$atype	= $this->post('atype');
		$where	 = 'and optid='.$this->adminid.'';
		if($atype=='all'){
			$where='';
		}
		if($key!=''){
			$where.=" and (`optname` like '%$key%' or filename like '%$key%')";
		}
		return array(
			'where' => $where,
			'fields' => '`id`,fileext,filename,filesizecn,filepath,adddt,optname,downci,ip,web',
		);
	}
	
	public function delfileAjax()
	{
		$id = $this->post('id','0');
		m('file')->delfile($id);
		backmsg();
	}
	
	public function defaultAction()
	{
		$this->title	= '修改头像';
		$face			= $this->db->getmou($this->T('admin'),'face',"`id`='$this->adminid'");
		$imgurl = '';	
		if(!$this->rock->isempt($face)){
			$imgurl='../../'.preg_replace("/_crop\d{4}/",'',$face);
		}
		//$face			= $this->rock->repempt($face,'images/white.gif');
		$this->smartydata['face']		= $face;
		$this->smartydata['imgurl']		= $imgurl;
	}
	
	public function changestyleAjax()
	{
		$style = $this->post('style','0');
		m('admin')->update('`style`='.$style.'', 'id='.$this->adminid.'');
	}


	public function editpassAjax()
	{
		$id			= $this->adminid;
		if(getconfig('systype')=='demo')exit('演示上不要修改');
		$oldpass	= $this->rock->post('passoldPost');
		$pasword	= $this->rock->post('passwordPost');
		$msg		= '';
		if($this->rock->isempt($pasword))$msg ='新密码不能为空';
		if($msg == ''){
			$oldpassa	= $this->db->getmou($this->T('admin'),"`pass`","`id`='$id'");
			if($oldpassa != md5($oldpass))$msg ='旧密码不正确';
			if($msg==''){
				if($oldpassa == md5($pasword))$msg ='新密码不能和旧密码相同';
			}
		}
		if($msg == ''){
			if(!$this->db->record($this->T('admin'), "`pass`='".md5($pasword)."'", "`id`='$id'"))$msg	= $this->db->error();
		}
		if($msg=='')$msg='success';
		echo $msg;
	}
	
	
	/**
		保存头像
	*/
	public function savefaceAjax()
	{
		$id			= $this->adminid;
		$arr		= array('face'=>$this->rock->post('facePost'));
		$msg		= '';
		if(!$this->db->record($this->T('admin'),$arr, "`id`='$id'"))$msg= $this->db->error();
		if($msg=='')$msg='success';
		echo $msg;
	}
	
	public function todoydAjax()
	{
		m('todo')->update("status=1,`readdt`='$this->now'", "`id` in(".$this->post('s').") and `status`=0");
	}
	
	public function totaldaetods($table, $rows)
	{
		$wdtotal	= m('todo')->rows("`uid`='$this->adminid' and `status`=0 and `tododt`<='$this->now'");
		return array('wdtotal'=>$wdtotal);
	}
	
	public function beforetotaldaetods($table)
	{
		$s = " and `uid`='$this->adminid' and `tododt`<='$this->now'";
		return $s;
	}
	
	public function getlinksAjax()
	{
		$rows = m('links')->getrows('1=1','*','`type`,`sort`');
		echo json_encode($rows);
	}
}