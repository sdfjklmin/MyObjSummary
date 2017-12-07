<?php
class reimClassModel extends Model
{
	private $groupname = '';
	public $serverrecid 	= 'rockreim';
	public $serverpushurl 	= '';
	public $serverhosturl 	= '';
	public $servertitle		= '';
	public $wxchattb		= 0;//聊天是否同步到微信
	public $wxcorpid		= '';//聊天是否同步到微信
	
	public function initModel()
	{
		$this->settable('im_mess');
		$this->inithost();
	}
	
	private function inithost()
	{
		if($this->serverpushurl!='')return;
		$dbs = m('option');
		$this->serverrecid		= $dbs->getval('reimrecidsystem');
		$this->serverpushurl	= $dbs->getval('reimpushurlsystem');
		$this->serverhosturl	= $dbs->getval('reimhostsystem');
		$this->servertitle		= $dbs->getval('reimtitlesystem');
		$this->wxcorpid			= $dbs->getval('weixin_corpid');
		$this->wxchattb			= (int)$dbs->getval('weixin_chattb','0');
		if(getconfig('systype')=='demo')$this->serverhosturl = $this->rock->jm->base64decode('d3M6Ly93d3cueGg4MjkuY29tOjY1NTIv');
		if($this->isempt($this->servertitle))$this->servertitle='信呼';
	}
	
	public function isanwx()
	{
		$bo = false;
		if($this->wxcorpid!='')$bo=true;
		return $bo;
	}
	
	public function getreims()
	{
		$this->inithost();
		return array(
			'recid' => $this->serverrecid,			
			'title' => $this->servertitle,			
			'wsurl' => $this->rock->jm->base64encode($this->serverhosturl)			
		);
	}

	private function getgroupid($gname)
	{
		$agesta = explode(',', $gname);
		$name 	= $agesta[0];
		$sid 	= (int)$this->db->getmou('[Q]im_group','id', "`name`='$name' and `type`=2");
		if($sid==0 && count($agesta)>1)$sid = $this->getgroupid($agesta[1]);
		$this->groupname = $name;
		return $sid;
	}
		
	/**
	*	REIM推送的
	*/
	public function sendsystem($sendid, $receid, $gname, $cont, $table='',$mid='', $url='')
	{
		$gid	= $this->getgroupid($gname);
		$gname	= $this->groupname;
		if($gid==0)return false;
		
		if($this->isempt($receid))return 'not receuid';
		$receids = $receid;
		
		$wheres	 = " and `id` in($receid)";
		if($receid=='all')$wheres='';
		$allsid 	= '';
		$recrarr 	= $this->db->getall("select id from [Q]admin where `status`=1  $wheres");
		foreach($recrarr as $k=>$rs){
			$allsid.=','.$rs['id'].'';
		}
		$messid		= 0;
		if($allsid != ''){
			$allsid = substr($allsid, 1);
			$this->insert(array(
				'type'	=> 'agent',
				'optdt'	=> $this->rock->now,
				'zt'	=> 0,
				'cont'	=> $this->rock->jm->base64encode($cont),
				'sendid'=> $sendid,
				'receid'=> $gid,
				'optid'	=> $sendid,
				'receuid' => $allsid,
				'table'	=> $table,
				'mid'	=> $mid,
				'url'	=> $url
			));
			$messid	= $this->db->insert_id();
			$this->db->insert('[Q]im_messzt','`mid`,`uid`,`gid`','select '.$messid.',id,'.$gid.' from `[Q]admin` where id in('.$allsid.') and `status`=1 ', true);
		}
		$resid = $receids;
		if($resid!='all')$resid = m('admin')->getonline($resid);
		if($resid!='' && $messid>0)$this->sendpush($sendid, $resid, array(
			'agent'		=> $gname,
			'optdt'		=> $this->rock->now,
			'type'		=> 'agent',
			'messid'	=> $messid,
			'agentid'	=> $gid,
			'cont'		=> $this->rock->jm->base64encode($cont),
			'table'		=> $table,
			'mid'		=> $mid,
			'url'		=> $url
		));
		//if($messid>0)$this->addhistory('agent', $gid, $allsid);
		return true;
	}
	
	/**
	*	应用信息推送
	*	$slx 0,1发送给pc，0,2发送给移动端,3不发送
	*/
	public function pushagent($receid, $gname, $cont, $title='', $url='', $slx=0)
	{
		if($slx==3)return false;
		$gid	= $this->getgroupid($gname);
		$gname	= $this->groupname;
		$sarr	= array(
			'gname'		=> $gname,
			'optdt'		=> $this->rock->now,
			'type'		=> 'agent',
			'title'		=> $title,
			'gid'		=> $gid,
			'cont'		=> $this->rock->jm->base64encode($cont),
			'url'		=> $url
		);
		$resid = $receid;
		if($slx == 0 || $slx==1){
			if($resid != 'all')$resid = m('admin')->getonline($resid);
			if($resid != '')$this->sendpush($this->adminid, $resid, $sarr);//PC端
		}
		if($slx == 0 || $slx==2){
			if($title=='')$title = $gname;
			c('JPush')->send($receid, $title, $cont);
		}
	}
	
	
	//获取REIM未读的
	public function getwdarr($mid=0, $ldt='')
	{
		$rows 	= array();
		if($mid==0)$mid	= $this->adminid;
		$whes	= $this->rock->dbinstr('receuid', $mid);
		$wher	= '';
		if(!$this->isempt($ldt))$wher=" and `optdt`>='$ldt' ";
		$arr 	= $this->getall("`zt`=0 and receid='$mid' and `type`='user' $wher group by `sendid`", "`sendid`,count(1) as stotal,max(optdt) as optdts,cont");
		foreach($arr as $k=>$rs){
			$uid 	= $rs['sendid'];
			$urs 	= $this->db->getone('[Q]admin',"`id`='$uid'",'`name`,`face`');
			if($urs){
				$rows[] = array(
					'type' 	=> 'user',
					'id'	=> $uid,
					'stotal'=> $rs['stotal'],
					'optdt'	=> $rs['optdts'],
					'name'	=> $urs['name'],
					'cont'	=> $rs['cont'],
					'face'	=> $this->getface($urs['face'])
				);
			}
		}
		
		// 讨论组 群
		$groupa	= $this->db->getarr('[Q]im_group','1=1','`name`,`face`,`type`');
		$gid 	= '0';
		foreach($groupa as $_gid=>$kvs)$gid.=','.$_gid.'';
		$arr 	= $this->getall("`type`='group' and `receid` in($gid) and $whes $wher and id in(select mid from [Q]im_messzt where uid='$mid') group by `receid`", "`receid`,count(1) as stotal,max(optdt) as optdts,cont");
		$typea	= array('group','group');
		foreach($arr as $k=>$rs){
			$grs	= $groupa[$rs['receid']];
			$typ 	= $typea[$grs['type']];
			$rows[] = array(
				'type' 	=> 'group',
				'id'	=> $rs['receid'],
				'stotal'=> $rs['stotal'],
				'optdt'	=> $rs['optdts'],
				'name'	=> $grs['name'],
				'cont'	=> $rs['cont'],
				'face'	=> $this->getface($grs['face'],'images/'.$typ.'.png')
			);
		}
		
		//应用的信息	
		$arr 	= $this->getall("`type`='agent' and `receid` in($gid) and $whes $wher and id in(select mid from [Q]im_messzt where uid='$mid') group by `receid`", "`receid`,count(1) as stotal,max(optdt) as optdts,cont");
		foreach($arr as $k=>$rs){
			$grs	= $groupa[$rs['receid']];
			$rows[] = array(
				'type' 	=> 'agent',
				'id'	=> $rs['receid'],
				'stotal'=> $rs['stotal'],
				'optdt'	=> $rs['optdts'],
				'cont'	=> $rs['cont'],
				'name'	=> $grs['name'],
				'face'	=> $this->getface($grs['face'])
			);
		}
		
		return $rows;
	}
	
	public function getweitotal($uid, $type, $sid=0, $blx=0)
	{
		$whes	= $this->rock->dbinstr('receuid', $uid);
		$where  = "`type`='$type' and `receid` ='$sid' and $whes and id in(select mid from [Q]im_messzt where uid='$uid')";
		if($type == 'user'){
			$where  = "`zt`=0 and `receid`='$uid' and `type`='user' and $whes";
		}
		if($blx==1)return $where;
		$to 	= $this->rows($where);
		return $to;
	}
	
	public function getgroup($uid)
	{
		$ids	= '0';
		$idrsa	= m('im_groupuser')->getall("uid='$uid'",'gid');
		foreach($idrsa as $k=>$rs){
			$ids.=','.$rs['gid'];
		}
		$rows 	= m('im_group')->getall("`id`>0 and ((`type` in(0,1) and `id` in($ids) ) ) order by `type`,`sort` ",'`id`,`type`,`name`,`face`,`sort`');
		$facarr = array('images/group.png','images/group.png','images/system.png');
		foreach($rows as $k=>$rs){
			$rows[$k]['face'] = $this->getface($rs['face'], $facarr[$rs['type']]);
		}
		return $rows;
	}
	
	public function getgroupuser($gid, $type)
	{
		$sql = "select b.id,b.name,b.face from `[Q]im_groupuser` a left join `[Q]admin` b on a.uid=b.id where a.gid='$gid' and b.status=1";
		if($type=='user')$sql = "select id,name,face from `[Q]admin` where id in(".$gid.",".$this->adminid.")";
		$rows = $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			$rows[$k]['face'] = $this->getface($rs['face']);
		}
		$arr['uarr'] 	= $rows;
		if($type=='user'){
			$arr['infor'] 	= array();
		}else{
			$arr['infor'] 	= $this->getgroupxinxi($gid);
		}
		return $arr;
	}
	
	public function getgroupxinxi($gid)
	{
		$rs = m('im_group')->getone($gid,'`id`,`type`,`name`,`face`');
		$facarr = array('images/group.png','images/group.png','images/system.png');
		$rs['face'] = $this->getface($rs['face'], $facarr[$rs['type']]);
		return $rs;
	}
	
	private function getface($face, $mr='')
	{
		if($mr=='')$mr 	= 'images/noface.png';
		if(substr($face,0,4)!='http' && !$this->isempt($face))$face = URL.''.$face.'';
		$face 			= $this->rock->repempt($face, $mr);
		return $face;
	}
	
	/**
		设置已读
	*/
	public function setyd($ids, $receid)
	{
		$this->update("`zt`=1", "`id` in($ids) and receid='$receid' and `type` ='user' ");
		m('im_messzt')->delete("uid='$receid' and `mid` in($ids)");
	}
	
	public function setallyd($type,$uid, $gid)
	{
		if($type=='user'){
			$this->update("`zt`=1", "`sendid` ='$gid' and receid='$uid' and `type`='user'");
		}else{
			m('im_messzt')->delete("uid='$uid' and `gid`=$gid");
		}
		m('im_history')->update('stotal=0', "`type`='$type' and `uid`='$uid' and `receid`='$gid'");
	}
	
	
	/**
	*	获取应用
	*/
	public function getagent($uid=0, $whe='', $pid=0)
	{
		if($uid==0)$uid = $this->adminid;
		$where	= m('admin')->getjoinstr('receid', $this->adminid);
		if($whe=='')$whe=' and `pid`='.$pid.'';
		$rows 	= $this->db->getrows('[Q]im_group',"`valid`=1 and `type`=2 $where $whe",'`id`,`name`,`url`,`face`,`num`,`pid`','`sort`');
		$dbs 	= m('im_menu');
		$barr	= array();
		foreach($rows as $k=>$rs){
			$rs['face'] 	= $this->getface($rs['face']);
			$stotal			= $totals= 0;
			if(!isempt($rs['num'])){
				$btosr = m('agent:'.$rs['num'].'')->gettotal();
				$stotal			= $btosr['stotal'];
				$rs['titles'] 	= $btosr['titles'];
			}
			if($pid==0){
				$submenu = $this->getagent($uid, '', $rs['id']);
				$totals	 = count($submenu);
				$rs['subagent'] = $submenu;
				if($totals>0){
					$stotal = 0;
					foreach($submenu as $k1=>$rs1)$stotal+=$rs1['stotal'];
				}
			}
			if($totals == 0){
				$menu	= $dbs->getall("mid='".$rs['id']."' and `pid`=0",'`id`,`name`,`type`,`url`,`num`,`color`','`sort`', 3);
				foreach($menu as $k1=>$rs1)$menu[$k1]['submenu'] = $dbs->getall("pid='".$rs1['id']."'",'`id`,`name`,`type`,`url`,`num`,`color`','`sort`');
				$rs['menu'] 	= $menu;
			}
			$rs['totals'] 	= $totals;
			$rs['stotal'] 	= $stotal;
			if(!isempt($rs['num']) || $totals>0)$barr[] = $rs;
		}
		return $barr;
	}
	
	/**
	*	获取历史记录
	*/
	public function gethistory($uid=0, $optdt='')
	{
		if($uid==0)$uid = $this->adminid;
		$where 	= '';
		if($optdt!='')$where = "and `optdt`>='$optdt'";
		$rows 	= $this->db->getall("select * from `[Q]im_history` where `uid`=$uid $where order by `optdt` desc");
		$dt 	= $this->rock->date;
		foreach($rows as $k=>$rs){
			$rows[$k]['optdts'] = substr($rs['optdt'],11,5);
			$rows[$k]['id'] 	= $rs['receid'];
			$name = '';
			$rson = false;
			if($rs['type']=='user'){
				$rson  	= $this->db->getone('[Q]admin', $rs['receid'], 'name,face');
				$face 	= 'images/noface.png';
			}else{
				$face	= 'images/group.png';
				$rson  	= $this->db->getone('[Q]im_group', $rs['receid'], 'name,face');
			}
			if($rson){
				$name = $rson['name'];
				$face = $this->getface($rson['face'], $face);
			}
			$rows[$k]['face'] = $face;
			$rows[$k]['name'] = $name;
		}
		return $rows;
	}
	
	
	/**
	*	添加到历史记录,用户不显示历史记录让从新显示
	*/
	public function addhistory($type, $receid, $uids,$optdt, $cont,$sendid=0)
	{
		$uidsas = explode(',', $uids);
		$db 	= m('im_history');
		foreach($uidsas as $uid){
			$where 	= "`type`='$type' and `receid`='$receid' and `uid`='$uid'";
			$one 	= $db->getone($where);
			$arr 	= array();
			$arr['optdt'] 	= $optdt;
			$arr['cont'] 	= $cont;
			$arr['sendid'] 	= $sendid;
			if(!$one){
				$arr['type'] 	= $type;
				$arr['receid'] 	= $receid;
				$arr['uid'] 	= $uid;
				$arr['stotal'] 	= 1;
				$where 	= '';
			}else{
				$arr['stotal'] 	= $one['stotal']+1;
			}
			if($this->adminid==$uid)$arr['stotal']=0;
			$db->record($arr, $where);
		}
	}
	
	public function delhistory($type, $receid, $uid=0)
	{
		$where  = "`type`='$type' and `receid`='$receid'"; 
		if($uid>0)$where.=" and `uid`='$uid'";
		if($type=='all'){
			$where  = "`uid`='$uid'"; 
		}
		m('im_history')->delete($where);
	}
	
	
	public function getrecord($type, $uid, $gid, $minid=0)
	{
		$arr = array();
		$rows= array();
		if($type == 'user'){
			$arr 	= $this->getuserinfor($uid, $gid, $minid);
		}
		if($type=='group'){
			$arr 	= $this->getgroupinfor($uid, $gid, $minid);
		}
		$arr['receinfor'] = $this->getreceinfor($type, $gid);
		if(isset($arr['rows']))$arr['rows'] = $this->replacefileid($arr['rows']);
		m('im_history')->update('stotal=0',"`type`='$type' and `receid`='$gid' and `uid`='$uid'");
		return $arr;
	}
	public function getreceinfor($type, $gid)
	{
		$info = array();
		if($type == 'user'){
			$info = m('admin')->getinfor($gid);
		}
		if($type=='group'){
			$info = $this->getgroupxinxi($gid);
		}
		$info['type'] = $type;
		$info['gid']  = $gid;
		return $info;
	}
	private function replacefileid($rows)
	{
		$fileids = '0';
		if($rows)foreach($rows as $k=>$rs){
			if($rs['fileid'])$fileids.=','.$rs['fileid'].'';
		}
		$imgext = ',gif,png,jpg,jpeg,bmp,';
		if($fileids!='0'){
			$farr  = array();
			$frows = m('file')->getrows("id in ($fileids)", 'id,fileext,filepath,filename,thumbpath,filesizecn,optid,optname,adddt,filesize');
			foreach($frows as $k=>$rs)$farr[$rs['id']]=$rs;
			if($farr)foreach($rows as $k=>$rs){
				$frs  = array();
				$fid  = $rs['fileid'];
				if(isset($farr[$fid]))$frs=$farr[$fid];
				if($frs){
					$type = $frs['fileext'];
					$path = $frs['filepath'];
					if(!$this->isempt($path)&&file_exists($path)){
						if($this->contain($imgext, ','.$type.',')){
							$cont = '<img fid="'.$fid.'" src="{url}'.$frs['thumbpath'].'">';
							$rows[$k]['cont'] = $this->rock->jm->base64encode($cont);
						}else{
							
						}
						$rows[$k]['filers']	  = $frs;
					}else{
						$rows[$k]['fileid']	  = 0;
					}
				}
			}
		}
		return $rows;
	}
	
	
	/**
	*	获取人员信息
	*	$uid 当前用户
	*/
	public function getuserinfor($uid, $receid, $minid=0)
	{
		$type 	= 'user';
		$whes	= $this->rock->dbinstr('receuid', $uid);
		
		$where1	= "`type`='$type' and `zt`=0 and `receid`='$uid' and `sendid`='$receid' and $whes";
		$wdtotal= $this->rows($where1);
		
		if($wdtotal > 0){
			$where	= "$where1 order by `id` desc limit 10";
		}else{
			$where 	= "`type`='$type' and ((`receid`='$uid' and `sendid`='$receid') or (`sendid`='$uid' and `receid`='$receid')) and $whes ";
			if($minid==0){
				$where .= ' order by `id` desc limit 5';
			}else{
				$where .= ' and `id`<'.$minid.' order by `id` desc limit 10';
			}
		}
		$rows 	= $this->getall($where, 'optdt,zt,id,cont,sendid,fileid');
		$len	= 0;
		$suids	= '0';
		$ids 	= '0';
		foreach($rows as $k=>$rs){
			$len++;
			if($rs['zt']==0)$ids .= ','.$rs['id'].'';
			$suids.= ','.$rs['sendid'];
			$wdtotal--;
		}
		$rows 		= $this->ivaregarr($suids, $rows);
		if($ids!='0')$this->setyd($ids, $uid);
		if($wdtotal<0)$wdtotal=0;
		
		return array(
			'rows' 		=> $rows,
			'wdtotal' 	=> $wdtotal
		);
	}
	
	public function getgroupinfor($uid, $receid, $minid=0)
	{
		$whes		= $this->rock->dbinstr('receuid', $uid);
		$order 		= '';
		$type		= 'group';
		$wdtotal= $this->getweitotal($uid, $type, $receid);
		$wdwhere= $this->getweitotal($uid, $type, $receid, 1);
		if($wdtotal > 0){
			$zwhere = " $wdwhere order by `id` desc limit 10";
		}else{
			$zwhere = " `receid`='$receid' and `type`='$type' and $whes";
			if($minid==0){
				$zwhere .= ' order by `id` desc limit 5';
			}else{
				$zwhere .= ' and `id`<'.$minid.' order by `id` desc limit 10';
			}
		}
		$rows	= $this->getall($zwhere, 'optdt,zt,id,cont,sendid,fileid');
		$ids 	= '0';
		$suids	= '0';
		$len 	= 0;
		foreach($rows as $k=>$rs){
			$len++;
			$ids .= ','.$rs['id'].'';
			$suids.= ','.$rs['sendid'];
			$wdtotal--;
		}
		$rows 	= $this->ivaregarr($suids, $rows);
		if($ids!='0')$this->setyd($ids, $uid);
		if($wdtotal<0)$wdtotal=0;
		return array(
			'rows' 		=> $rows,
			'wdtotal' 	=> $wdtotal
		);
	}
	
	private function ivaregarr($suids,$rows,$fid='')
	{
		if($suids=='' || $suids=='0')return $rows;
		if($fid=='')$fid='sendid';
		$farr	= $this->db->getarr('[Q]admin', "`id` in($suids)",'`face`,`name`');
		foreach($rows as $k=>$rs){
			$face =  $name = '';
			if(isset($farr[$rs[$fid]])){
				$face = $farr[$rs[$fid]]['face'];
				$name = $farr[$rs[$fid]]['name'];
			}	
			$rows[$k]['face'] 	  = $this->getface($face);
			$rows[$k]['sendname'] = $name;
		}
		return $rows;
	}
	
	private function ivarggarr($sgids,$rows, $fid='')
	{
		if($sgids=='' || $sgids=='0')return $rows;
		if($fid=='')$fid='receid';
		$farr	= $this->db->getarr('[Q]im_group', "`id` in($suids)",'`face`,`name`');
		foreach($rows as $k=>$rs){
			$face =  $name = '';
			if(isset($farr[$rs[$fid]])){
				$face = $farr[$rs[$fid]]['face'];
				$name = $farr[$rs[$fid]]['name'];
			}	
			$rows[$k]['face'] 	  = $this->getface($face);
			$rows[$k]['sendname'] = $name;
		}
		return $rows;
	}
	
	/**
		发送单人信息
		$lx = 0 app发送 1web客户端
	*/
	public function senduser($sendid,$receid, $cans=array(), $lx=0)
	{
		$cont		= '';
		if(isset($cans['cont']))$cont=$cans['cont'];
		$optdt		= $this->rock->now;
		$fileid		= 0;
		$msgid		= '';
		if(isset($cans['optdt']))$optdt=$cans['optdt'];
		if(isset($cans['sendid']))$sendid=$cans['sendid'];
		if(isset($cans['fileid']))$fileid=$cans['fileid'];
		if(isset($cans['msgid']))$msgid=$cans['msgid'];
		$arr = array(
			'cont'		=> $cont,
			'sendid'	=> $sendid,
			'receid'	=> $receid,
			'type'		=> 'user',
			'optdt'		=> $optdt,
			'zt'		=> '0',
			'fileid'	=> $fileid,
			'msgid'		=> $msgid
		);
		$arr['receuid'] = $arr['sendid'].','.$arr['receid'];
		$bo = $this->insert($arr);
		$arr['id'] 		= $this->db->insert_id();
		$arr['nuid'] 	= $this->rock->request('nuid');
		$farr			= array();
		if($fileid>0){
			m('file')->addfile($fileid, 'im_mess', $arr['id']);
			$farr = m('file')->getone($fileid,'filesizecn,fileext,thumbpath,filename');
			if($farr)foreach($farr as $fk=>$fv)$arr[$fk]		= $fv;
		}
		//给客户端发送0
		if($lx==0){
			$receids = m('admin')->getonline($arr['receid']);
			if($receids != ''){
				$pusharr 	= array(
					'cont' 	=> $cont,
					'type' 	=> 'user',
					'optdt' => $optdt,
					'messid' => $arr['id'],
					'fileid' => $fileid
				);
				if($farr)foreach($farr as $fk=>$fv)$pusharr[$fk] = $fv;
				$this->sendpush($arr['sendid'], $receids , $pusharr);
			}
		}
		
		$this->addhistory('user', $receid, $sendid, $optdt, $cont, $sendid);
		if($sendid!=$receid)$this->addhistory('user', $sendid, $receid, $optdt, $cont, $sendid);
		
		$last	= date('Y-m-d H:i:s', time()-15);
		$where 	= "`uid`='$receid' and `online`=1 and `cfrom` in('appandroid','appios') and `moddt`<'$last'";
		$tos 	= m('logintoken')->rows($where);
		if($tos>0){//没有打开应用
			$conts = substr($this->rock->jm->base64decode($cont),0,99);
			c('JPush')->send($receid,'['.$this->adminname.']发来一条消息', ''.$this->adminname.':'.$conts, 1);
		}
		
		return $arr;
	}
	
	/**
		发送群讨论信息
		$lx = 0 app发送 1web客户端
	*/
	public function sendgroup($sendid, $gid, $cans=array(), $lx=0)
	{
		$cont		= '';
		if(isset($cans['cont']))$cont=$cans['cont'];
		$receid		= $gid;
		$gname		= m('im_group')->getmou('name', $gid);
		$type		= 'group';
		$fileid		= 0;
		$msgid		= '';
		$optdt		= $this->rock->now;
		if(isset($cans['optdt']))$optdt=$cans['optdt'];
		if(isset($cans['type']))$type=$cans['type'];
		if(isset($cans['sendid']))$sendid=$cans['sendid'];
		if(isset($cans['fileid']))$fileid=$cans['fileid'];
		if(isset($cans['msgid']))$msgid=$cans['msgid'];
		$aors		= m('im_groupuser')->getall("`gid`='$receid'",'uid');
		$asid		= $asids =  '';
		foreach($aors as $k=>$rs){
			$_uid = $rs['uid'];
			if($_uid != $sendid)$asid.=','.$_uid;
			$asids.=','.$_uid;
		}
		
		if($asids != '')$asids = substr($asids, 1);	
		$arr = array(
			'cont'		=> $cont,
			'sendid'	=> $sendid,
			'receid'	=> $receid,
			'receuid'	=> $asids,
			'type'		=> $type,
			'optdt'		=> $optdt,
			'zt'		=> '1',
			'fileid'	=> $fileid,
			'msgid'		=> $msgid
		);
		$bo = $this->insert($arr);
		$arr['id'] 		= $this->db->insert_id();
		$arr['nuid'] 	= $this->rock->request('nuid');
		$arr['gid'] 	= $receid;
		if($asid != ''){
			$asid = substr($asid, 1);
			$this->db->insert('[Q]im_messzt','`mid`,`uid`,`gid`','select '.$arr['id'].',`id`,'.$gid.' from `[Q]admin` where id in('.$asid.') and `status`=1', true);
		}
		$arr['receid']	= $asid;
		$farr			= array();
		if($fileid>0){
			m('file')->addfile($fileid, 'im_mess', $arr['id']);
			$farr = m('file')->getone($fileid,'filesizecn,fileext,thumbpath,filename');
			if($farr)foreach($farr as $fk=>$fv)$arr[$fk]		= $fv;
		}
		
		//推送到PC客户端上
		if($lx==0 && $asid != ''){
			$receids = m('admin')->getonline($asid);
			if($receids != ''){
				$pusharr	= array(
					'cont' 	=> $cont,
					'type' 	=> 'group',
					'gid'	=> $gid,
					'gname'	=> $gname,
					'optdt' => $optdt,
					'messid' => $arr['id'],
					'fileid' => $fileid
				);
				if($farr)foreach($farr as $fk=>$fv)$pusharr[$fk] = $fv;
				$this->sendpush($arr['sendid'], $receids , $pusharr);
			}
		}
		$this->addhistory('group', $gid, $arr['receuid'], $optdt, $cont,$sendid);
		
		if($asid != ''){
			$last	= date('Y-m-d H:i:s', time()-15);
			$where 	= "`uid` in($asid) and `online`=1 and `cfrom` in('appandroid','appios') and `moddt`<'$last'";
			$tos 	= m('logintoken')->rows($where);
			if($tos>0){//有打开应用
				$conts = substr($this->rock->jm->base64decode($cont),0,99);
				c('JPush')->send($asid,'['.$gname.']发来一条消息', ''.$this->adminname.':'.$conts, 1);
			}
		}
		$arr['gname'] = $gname;
		return $arr;
	}
	
	public function sendinfor($type, $sendid, $gid, $cans=array(), $lx=0)
	{
		$arr 		= array();
		if($type == 'user'){
			$arr 	= $this->senduser($sendid, $gid, $cans, $lx);
		}
		if($type == 'group'){
			$arr 	= $this->sendgroup($sendid, $gid, $cans, $lx);
		}
		if($this->wxchattb==1 && isset($arr['id'])){
			$msgid 	= $arr['msgid'];
			if(isempt($msgid))$this->asynurl('asynrun','wxchattb', array(
				'id' => $arr['id']
			));
		}
		return $arr;
	}
	
	/**
	*	推送到PC提醒
	*/
	public function sendpush($sendid, $receid, $conarr=array())
	{
		$bsarr 	= array('msg'=>'notpushurl','code'=>2);
		$bstt	= json_encode($bsarr);
		if($sendid==0)$sendid = 1;
		$sers 	= $this->db->getone('[Q]admin',"`id`='$sendid'", "`name`,`face`");
		if(!$sers)return $bstt;
		$face 	= $sers['face']; 
		$carr['adminid'] 	= $sendid;
		$carr['optdt'] 		= $this->rock->now;
		$carr['sendname'] 	= $sers['name'];
		$carr['face'] 		= $this->getface($face);
		$carr['receid'] 	= $receid;
		foreach($conarr as $k=>$v)$carr[$k]=$v;
		return $this->pushserver('send', $carr);
	}
	
	/**
	*	向服务端发送异步任务
	*	$runtime 运行的时间贞
	*/
	public function asynurl($m, $a,$can=array(), $runtime=0)
	{
		$runurl	= getconfig('localurl');
		if($runurl=='')$runurl = URL;
		$key 	 = getconfig('asynkey');
		if($key!='')$key = md5(md5($key));
		$runurl .= 'api.php?m='.$m.'&a='.$a.'&adminid='.$this->adminid.'&asynkey='.$key.'';
		if(is_array($can))foreach($can as $k=>$v)$runurl.='&'.$k.'='.$v.'';
		return $this->pushserver('runurl', array(
			'url' => $runurl,
			'runtime' => $runtime
		));
	}
	
	public function pushserver($atype, $cans=array())
	{
		$bsarr 	= array('msg'=>'notpushurl','code'=>2);
		$bstt	= json_encode($bsarr);
		if($this->serverpushurl=='')return $bstt;
		$url 	= $this->serverpushurl.'?reimrecid='.$this->serverrecid.'';
		$carr['from'] 	= $this->serverrecid;
		$carr['adminid']= $this->adminid;
		$carr['atype'] 	= $atype;
		foreach($cans as $k=>$v)$carr[$k]=$v;
		$str 			= json_encode($carr);
		return c('curl')->postcurl($url, $str);
	}
	
	
	
	//创建群等
	public function creategroup($name, $receid, $type=1, $explain='')
	{
		$arr['name'] 		= $name;
		$arr['type'] 		= $type;
		$arr['createid'] 	= $this->adminid;
		$arr['createname'] 	= $this->adminname;
		$arr['createdt'] 	= $this->rock->now;
		$arr['explain'] 	= $explain;
		$arr['valid'] 		= 1;
		$gid 	= m('im_group')->insert($arr);
		$this->db->insert('[Q]im_groupuser','gid,uid','select '.$gid.',id from [Q]admin where id in('.$receid.') and `status`=1', true);
		$arr['id']			= $gid;
		$arr['type']		= 'group';
		return $arr;
	}
	
	//下载同步聊天记录
	public function downrecord($uid, $maxid=0, $minid=999999999)
	{
		$whes	= $this->rock->dbinstr('receuid', $uid);
		$lastdt = date('Y-m-d 00:00:00', time()-20*24*3600);
		$limit	= 20;
		$fields	= 'optdt,zt,id,`type`,receid,cont,sendid,fileid';
		$sql1	= "select $fields from `[Q]im_mess` where `id`> $maxid and $whes";
		if($maxid==0){
			$sql1.=' order by id desc';
		}else{
			$sql1.=' order by id asc';
		}
		$sql1.=' limit '.$limit.'';
		$rows 	= $this->db->getall($sql1);
		$nsaid	= '0';
		foreach($rows as $k=>$rs)$nsaid.=','.$rs['id'].'';
		$olimie	= $limit-$this->db->count;
		if($olimie>0 && $minid>0){
			$sql2	= "select $fields from `[Q]im_mess` where `id`< $minid and `optdt`>='$lastdt' and `id` not in($nsaid) and $whes order by id desc limit $olimie";
			$rowss 	= $this->db->getall($sql2);
			if($rowss)$rows 	= array_merge($rows, $rowss);
		}
		$suids	= '0';
		$dbs 	= m('im_messzt');
		foreach($rows as $k=>$rs){
			$suids.= ','.$rs['sendid'];
			if($rs['type'] != 'user'){
				$zt = 0;
				if($dbs->rows("`mid`='".$rs['id']."' and `uid`='$uid'")==0)$zt=1;
				$rows[$k]['zt'] = $zt;
			}
			if($rs['sendid']==$uid)$rows[$k]['zt'] = 1;
		}
		$rows 	= $this->ivaregarr($suids, $rows);
		$rows	= $this->replacefileid($rows);
		$arr['rows'] = $rows;
		return $arr;
	}
	
	
	/**
	*	删除服务器上记录
	*/
	public function clearrecord($type,$gid, $uid, $ids='',$day=0)
	{
		$this->setallyd($type,$uid, $gid);
		$whes	= $this->rock->dbinstr('receuid', $uid);
		if($type=='user'){
			$where1	= "`type`='$type' and ((`receid`='$uid' and `sendid`='$gid') or (`receid`='$gid' and `sendid`='$uid')) and $whes";
		}else{
			$where1	= "`type`='$type' and `receid`='$gid' and $whes";
		}
		if($ids!='')$where1.=" and `id` in($ids)";
		if($day>0){
			$dts = date('Y-m-d H:i:s',time()-$day*24*3600);
			$where1.=" and `optdt`< '$dts'";
		}
		$rows 	= $this->getall($where1, '`receuid`,`id`');
		$xids	= '0';
		foreach($rows as $k=>$rs){
			$sid = $rs['id'];
			if($this->isempt($rs['receuid'])){
				$xids.=','.$sid.'';
			}else{
				$ssid  = '';
				$uidsa = explode(',', $rs['receuid']);
				foreach($uidsa as $suid){
					if($suid != $uid){
						$ssid.=','.$suid.'';
					}
				}
				if($ssid==''){
					$xids.=','.$sid.'';
				}else{
					$ssid = substr($ssid,1);
					$this->update("`receuid`='$ssid'", $sid);
				}
			}
		}
		if($xids!='0')$this->delete("`id` in($xids)");
		if($ids=='' && $day==0)$this->delhistory($type,$gid, $uid);
	}
	
	/**
	*	转发
	*/
	public function forward($tuid, $type, $cont, $fid=0)
	{
		$uid 	= $this->adminid;
		if($fid>0){
			$frs	= m('file')->getone($fid, '`filepath`,`filename`,`filesizecn`,`fileext`');
			$msg 	= '文件不存在了';
			if(!$frs)return $msg;
			if(!file_exists($frs['filepath']))return $msg;
			$cont 	= '';$jpgallext	= '|jpg|png|gif|bmp|jpeg|';
			if(contain($jpgallext,'|'.$frs['fileext'].'|')){
				$cont = '[图片 '.$frs['filesizecn'].']';
			}else{
				$cont = '['.$frs['filename'].' '.$frs['filesizecn'].']';
			}
			$cont	  = $this->rock->jm->base64encode($cont);
		}
		$tuids	= explode(',', $tuid);
		foreach($tuids as $gid)$this->sendinfor($type, $uid, $gid, array(
			'optdt' => $this->rock->now,
			'cont'  => $cont,
			'fileid'=> $fid
		));
		return 'ok';
	}
	
	//会话管理的
	public function createchat($name, $aid, $uids='', $na='', $optdt='', $iscjwx=false)
	{
		if($optdt=='')$optdt=$this->rock->now;
		if($na=='')$na = $this->adminname;
		if($uids=='')$uids = $aid;
		$this->db->record('[Q]im_group', array(
			'type'			=> 1,
			'name'			=> $name,
			'createid'		=> $aid,
			'createname'	=> $na,
			'createdt'		=> $optdt,
			'valid'			=> '1'
		));
		$gid	= $this->db->insert_id();
		$this->adduserchat($gid, $uids, false);
		return $gid;
	}
	public function adduserchat($gid, $uids, $isadd=false)
	{
		if(isempt($uids))return '';
		$ids 	= '';
		$uidss	= explode(',', $uids);
		$db		= m('im_groupuser');
		foreach($uidss as $aid){
			if($db->rows("gid='$gid' and `uid`='$aid'")==0){
				$db->insert(array('gid' => $gid,'uid' => $aid));
				$ids .= ','.$aid.'';
			}
		}
		if($ids!='')$ids = substr($ids,1);
		if($isadd && $this->isanwx())m('weixin:chat')->chatupdate($gid);
		return $ids;
	}
	public function deluserchat($gid, $uids)
	{
		if(isempt($uids))return;
		m('im_groupuser')->delete("`gid`='$gid' and `uid` in($uids)");
	}
	public function deletechat($gid)
	{
		m('im_group')->delete($gid);
		m('im_groupuser')->delete("`gid`='$gid'");
		m('im_messzt')->delete("`gid`='$gid'");
		$this->delhistory('group',$gid, 0);
	}
	public function exitchat($gid, $aid)
	{
		$dbs = m('im_groupuser');
		$dbs->delete("`gid`='$gid' and `uid`='$aid'");
		m('im_messzt')->delete("`gid`='$gid' and `uid`='$aid'");
		if($this->isanwx())m('weixin:chat')->chatquit($gid, $aid);
		if($dbs->rows('gid='.$gid.'')==0)m('im_group')->delete($gid);
		$this->delhistory('group',$gid, $aid);
	}
	
	
	
	
	
	
	
	//微信消息回调
	public function getwxchat($arr)
	{
		$this->rock->debugs(json_encode($arr),'cccc');if(!isset($arr['MsgType']))return;
		$MsgType		= $arr['MsgType'];
		$FromUserName	= $arr['FromUserName'];
		$user 			= $FromUserName;
		$urs		 	= m('admin')->getone("`user`='$FromUserName'",'id,name');
		if(!$urs)return;
		$sendid			= $urs['id'];
		$sendname		= $urs['name'];
		$this->adminid	= $sendid;
		$this->adminname= $sendname;
		if($MsgType == 'event'){
			$event	= $arr['Event'];
			if($event=='create_chat')m('weixin:chat')->addchat($sendid, $sendname,$arr);
			if($event=='update_chat')m('weixin:chat')->updatechat($arr);
			if($event=='quit_chat')m('weixin:chat')->quitchat($arr);
			if($event=='subscribe')m('weixin:user')->subscribe($user,1);
			if($event=='unsubscribe')m('weixin:user')->subscribe($user,4);
			return;
		}
		if(!isset($arr['Type']))return;
		$Type			= $arr['Type'];
		$gid 			= 0;
		$optdt 			= date('Y-m-d H:i:s', $arr['CreateTime']);
		$cont 			= '';
		if($Type=='single'){
			$gid = (int)m('admin')->getmou('id', "`user`='".$arr['Id']."'");
			$type= 'user';
		}
		if($Type=='group'){
			$gid = m('weixin:chat')->getchatid($arr['Id'], $sendid, $sendname);
			$type= 'group';
		}
		if($gid==0)return;
		
		@$msgid	= $arr['MsgId'];if(isempt($msgid))return;
		if($this->rows("`msgid`='$msgid'")>0)return;
		
		if($MsgType=='text'){
			$cont = $arr['Content'];
		}
		if($MsgType=='location'){
			$cont = '位置：'.$arr['Label'];
		}
		if($MsgType=='voice'){
			$cont = '语音,请用微信收听';
		}
		if($MsgType=='image'){
			$cont = '[图片]';
			$PicUrl = $this->rock->jm->encrypt($arr['PicUrl']);
			$this->asynurl('asynrun','downwxpic', array(
				'picurl' 	=> $PicUrl,
				'msgid' 	=> $msgid,
			));
		}
		if($MsgType=='link'){
			if(isempt($arr['Title']))$arr['Title']='链接';
			$cont = '[A]'.$arr['Title'].'|'.$arr['Url'].'[/A]';
		}
		if($cont!='')$this->sendinfor($type,$sendid, $gid, array(
			'cont'  => $this->rock->jm->base64encode($cont),
			'optdt' => $optdt,
			'msgid' => $msgid
		));
	}
	
	//下载微信上图片
	public function downwximg($url, $msgid)
	{
		if($url=='' || $msgid=='')return;
		$cont	= c('curl')->getcurl($url);
		$barr 	= c('down')->createimage($cont,'jpg','微信图片');
		if($barr){
			$fileid 	= $barr['id'];
			$filesize 	= $barr['filesizecn'];
			$mors 		= $this->getone("`msgid`='$msgid'",'id');
			if($mors){
				$id = $mors['id'];
				$this->update(array('fileid' => $fileid), $id);
				m('file')->addfile($fileid, 'im_mess', $id);
			}
		}
	}
}