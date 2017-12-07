<?php 
class reimClassAction extends apiAction
{
	public function getrecordAction()
	{
		$uid 		= $this->adminid;
		$type 		= $this->post('type');
		$gid 		= (int)$this->post('gid');
		$minid 		= (int)$this->post('minid');
		$arr 		= m('reim')->getrecord($type, $uid, $gid, $minid);
		$this->showreturn($arr);
	}
	
	public function getreceinforAction()
	{
		$uid 		= $this->adminid;
		$type 		= $this->post('type');
		$gid 		= (int)$this->post('gid');
		$reimdb		= m('reim');
		$arr['receinfor'] 		= $reimdb->getreceinfor($type, $gid);
		$reimdb->setallyd($type, $uid, $gid);
		$this->showreturn($arr);
	}
	
	public function sendinforAction()
	{
		$uid 		= $this->adminid;
		$type 		= $this->post('type');
		$gid 		= (int)$this->post('gid');
		$lx 		= 0;
		if($this->cfrom=='reim')$lx=1;
		if($type=='group'){
			$tos = m('im_groupuser')->rows("`gid`='$gid' and `uid`='$uid'");
			if($tos==0)$this->showreturn('','您不在此会话中，不允许发送', 201);
		}
		$arr 		= m('reim')->sendinfor($type, $uid, $gid, array(
			'optdt' => $this->now,
			'cont'  => $this->post('cont'),
			'fileid'=> (int)$this->post('fileid')
		), $lx);
		$this->showreturn($arr);
	}
	
	public function yiduAction()
	{
		$id = $this->post('id');
		m('reim')->setyd($id, $this->adminid);
		$this->showreturn($id);
	}
	
	public function yiduallAction()
	{
		$type 		= $this->post('type');
		$gid 		= (int)$this->post('gid');
		m('reim')->setallyd($type, $this->adminid, $gid);
		$this->showreturn('');
	}
	
	public function createtaolunAction()
	{
		$name 	= $this->post('title');
		$explain= $this->post('content');
		$receid = $this->post('receid');
		if($name==''||$receid=='')$this->showreturn('','not data',201);
		$arr = m('reim')->creategroup($name, $receid.','.$this->adminid, 1, $explain);
		$this->showreturn($arr);
	}
	
	public function getgroupuserAction()
	{
		$gid 	= (int)$this->post('gid');
		$type 	= $this->post('type');
		$arr 	= m('reim')->getgroupuser($gid, $type);
		$this->showreturn($arr);
	}
	
	public function downrecordAction()
	{
		$minid = floatval($this->post('minid','999999999'));
		$maxid = floatval($this->post('maxid','0'));
		$arr 	= m('reim')->downrecord($this->adminid, $maxid, $minid);
		$this->showreturn($arr);
	}
	
	public function delhistoryAction()
	{
		$gid 	= (int)$this->post('gid');
		$type 	= $this->post('type');
		$arr 	= m('reim')->delhistory($type,$gid,$this->adminid);
		$this->showreturn('');
	}
	
	//邀请人员
	public function yaoqinguidAction()
	{
		$gid	= (int)$this->post('gid');
		$val	= $this->post('val');
		$ids 	= m('reim')->adduserchat($gid, $val, true);
		$msg	= 'success'.$ids.'';
		$this->showreturn($msg);
	}
	
	//退出讨论组
	public function exitgroupAction()
	{
		$aid	= $this->adminid;
		$gid	= (int)$this->post('gid');
		m('reim')->exitchat($gid, $aid);
		$this->showreturn('success');
	}
	
	public function createlunAction()
	{
		$val	= $this->getvals('val');
		m('reim')->createchat($val, $this->adminid,$this->adminid, $this->adminname,'', true);
		$this->showreturn('success');
	}
	
	public function clearrecordAction()
	{
		$gid 	= (int)$this->post('gid');
		$type 	= $this->post('type');
		$ids 	= $this->post('ids');
		$day 	= (int)$this->post('day');
		$arr 	= m('reim')->clearrecord($type,$gid,$this->adminid, $ids, $day);
		$this->showreturn('');
	}
	
	public function changefaceAction()
	{
		$fid 	= (int)$this->post('id');
		$uid 	= $this->adminid;
		$face 	= m('admin')->changeface($uid, $fid); 	
		if(!$face)$this->showreturn('','fail changeface',201);
		$this->showreturn($face);
	}
	
	public function downfileAction()
	{
		$id 	= (int)$this->post('id');
		m('file')->download($id);
	}
	
	public function forwardAction()
	{
		$fid = (int)$this->post('fileid');
		$tuid= $this->post('tuid');
		$msg = m('reim')->forward($tuid, 'user', $this->post('cont'), $fid);
		if($msg!='ok')$this->showreturn('', $msg, 201);
		$this->showreturn('');
	}
}