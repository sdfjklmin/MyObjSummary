<?php
class kaoqinClassAction extends Action
{
	//打卡的
	public function kqdkjlbeforeshow($table)
	{
		$atype	= $this->post('atype');
		$dt1	= $this->post('dt1');
		$dt2	= $this->post('dt2');
		$key	= $this->post('key');
		$s 		= '';
		if($atype=='my')$s.=' and b.id='.$this->adminid.'';
		if(!isempt($dt1))$s.=" and a.`dkdt`>='$dt1'";
		if(!isempt($dt2))$s.=" and a.`dkdt`<='$dt2 23:59:59'";
		if(!isempt($key))$s.=" and (b.`name` like '%$key%' or b.`deptname` like '%$key%')";
		$fields = 'a.*,b.name,b.deptname';
		$table  = '[Q]'.$table.' a left join `[Q]admin` b on a.uid=b.id';
		return array('where'=>$s,'table'=>$table, 'fields'=>$fields);
	}
	public function kqdkjlaftershow($table, $rows)
	{
		$types = explode(',','在线打卡,考勤机,手机定位,手动添加,异常添加,数据导入,接口导入');
		$dtobj = c('date');
		foreach($rows as $k=>$rs){
			$rows[$k]['type'] = $types[$rs['type']];
			$rows[$k]['week'] = $dtobj->cnweek($rs['dkdt']);
		}
		return array('rows'=>$rows);
	}
	
	//定位打卡的
	public function locationbeforeshow($table)
	{
		$atype	= $this->post('atype');
		$dt1	= $this->post('dt1');
		$dt2	= $this->post('dt2');
		$key	= $this->post('key');
		$s 		= '';
		if($atype=='my')$s.=' and b.id='.$this->adminid.'';
		if(!isempt($dt1))$s.=" and a.`optdt`>='$dt1'";
		if(!isempt($dt2))$s.=" and a.`optdt`<='$dt2 23:59:59'";
		if(!isempt($key))$s.=" and (b.`name` like '%$key%' or b.`deptname` like '%$key%')";
		$fields = 'a.*,b.name,b.deptname';
		$table  = '[Q]'.$table.' a left join `[Q]admin` b on a.uid=b.id';
		return array('where'=>$s,'table'=>$table, 'fields'=>$fields);
	}
	public function locationaftershow($table, $rows)
	{
		$dtobj = c('date');
		foreach($rows as $k=>$rs){
			$rows[$k]['week'] = $dtobj->cnweek($rs['optdt']);
		}
		return array('rows'=>$rows);
	}
	
	
	//考勤信息
	public function kqinfobeforeshow($table)
	{
		$dt1	= $this->post('dt1');
		$atype	= $this->post('atype');
		$key	= $this->post('key');
		$keys	= $this->post('keys');
		$uid	= $this->adminid;
		$s 		= '';
		
		if($atype=='all'){
			$s = '';
		}
		if($atype=='my'){
			$s = 'and uid='.$uid.'';
		}
		
		
		if(!isempt($dt1))$s.=" and a.`stime` like '$dt1%'";
		if(!isempt($key))$s.=" and (b.`name` like '%$key%' or b.`deptname` like '%$key%')";
		if(!isempt($keys))$s.=" and (a.`kind`='$keys' or a.`qjkind`='$keys')";
		$fields = 'a.*,b.name,b.deptname';
		$table  = '[Q]'.$table.' a left join `[Q]admin` b on a.uid=b.id';
		return array('where'=>$s,'table'=>$table, 'fields'=>$fields,'order'=>'a.stime desc');
	}
	
	public function kqinfoaftershow($table, $rows)
	{
		$uid 	= $this->adminid;
		$types 	= explode(',','<font color=blue>待审核</font>,<font color=green>已审核</font>,<font color=red>未通过</font>');
		foreach($rows as $k=>$rs){
			$rows[$k]['status'] = $types[$rs['status']];
			$modenum  = 'leavehr';
			$modename = '考勤信息';
			if($rs['kind']=='请假'){
				$modenum  = 'leave';
				$modename = '请假条';
			}
			if($rs['kind']=='加班'){
				$modenum  = 'jiaban';
				$modename = '加班单';
			}
			$rows[$k]['modenum'] 	= $modenum;
			$rows[$k]['modename'] 	= $modename;
		}
		
		$str = '';
		if($this->post('atype')=='my'){
			$kqm = m('kaoqin');
			$njs = $kqm->getqjsytime($uid, '年假');
			$tx  = $kqm->getqjsytime($uid, '调休');
			$str='剩余年假('.$njs.'小时)，可调休('.$tx.'小时)';
		}
		return array('rows'=>$rows,'totalstr'=> $str);
	}
	
	
	
	
	
	
	
	
	
	
	public function kqsjgzdataAjax()
	{
		$this->rows = array();
		$this->getkqdat(0, 1);
		$this->returnjson(array(
			'rows' => $this->rows
		));
	}
	private function getkqdat($pid, $oi)
	{
		$db		= m('kqsjgz');
		$menu	= $db->getall("`pid`='$pid' order by `sort`",'*');
		foreach($menu as $k=>$rs){
			$sid			= $rs['id'];
			$rs['level']	= $oi;
			$rs['stotal']	= $db->rows("`pid`='$sid'");
			$this->rows[] = $rs;
			$this->getkqdat($sid, $oi+1);
		}
	}
	public function kqsjgzdatadelAjax()
	{
		$type	= (int)$this->post('type','0');
		$id 	= (int)$this->post('id');
		if($id==1 && $type!=3)showreturn('','此记录不能删除',201);
		if($type==0)m('kqsjgz')->delete("`id`='$id' or pid='$id'");
		if($type==1)m('kqdist')->delete("`id`='$id'");
		if($type==2)m('kqxxsj')->delete("`id`='$id' or pid='$id'");
		if($type==3)m('kqxxsj')->delete("`id`='$id'");
		showreturn();
	}
	
	
	
	
	
	
	
	//考勤时间分配
	public function kqdistbefore($table)
	{
		$type	= (int)$this->post('type','0');
		return array(
			'where' => 'and `type`='.$type.'',
			'order' => 'id desc'
		);
	}
	public function kqdistafter($table, $rows)
	{
		$type	= (int)$this->post('type','0');
		$db 	= m('kqsjgz');
		if($type==1)$db = m('kqxxsj');
		foreach($rows as $k=>$rs){
			
			$rows[$k]['mid'] 	= $db->getmou('name', $rs['mid']);
			$rows[$k]['mids'] 	= $rs['mid'];
		}
		if($type==0){
			$gzdata	= $db->getall('pid=0','id,name','`sort`');
		}else{
			$gzdata	= $db->getall('pid=0','id,name','`id`');
		}
		return array(
			'rows' 		=> $rows,
			'gzdata' 	=> $gzdata
		);
	}
	
	
	public function kqxxsjdtbefore($table)
	{
		$pid 	= (int)$this->post('pid','0');
		$month 	= $this->post('month', date('Y-m'));
		$s 		= 'and `pid`='.$pid.'';
		if(!isempt($month))$s.=" and `dt` like '$month%'";
		return array(
			'where' => $s,
			'order' => 'dt asc'
		);
	}
	public function kqxxsjdtafter($table, $rows)
	{
		$dtobj = c('date');
		foreach($rows as $k=>$rs){
			$rows[$k]['week'] = $dtobj->cnweek($rs['dt']);
		}
		return array('rows'=>$rows);
	}
	public function setxiugdateAjax()
	{
		$month 	= $this->post('month');
		$pid 	= (int)$this->post('pid','0');
		if(isempt($month) || $pid==0)return;
		$dtobj 	= c('date');
		$max 	= $dtobj->getmaxdt($month);
		$db 	= m('kqxxsj');
		for($i=1; $i<=$max; $i++){
			$oi = $i;if($oi<10)$oi='0'.$i.'';
			$dt = ''.$month.'-'.$oi.'';
			$we = $dtobj->cnweek($dt);
			if($we=='六' || $we=='日'){
				$where = "pid='$pid' and `dt`='$dt'";
				if($db->rows($where)==0)$db->insert("pid='$pid',`dt`='$dt'");
			}
		}
	}
	
	
	//考勤分析
	public function kqanaybeforeshow($table)
	{
		$dt1	= $this->post('dt1');
		$key	= $this->post('key');
		$iswork	= $this->post('iswork','1');
		$s 		= '';
		if($iswork=='1')$s.=" and a.`iswork`=$iswork";
		if(!isempt($dt1))$s.=" and a.`dt` like '$dt1%'";
		if(!isempt($key))$s.=" and (b.`name` like '%$key%' or b.`deptname` like '%$key%')";
		$fields = 'a.*,b.name,b.deptname';
		$table  = '[Q]'.$table.' a left join `[Q]admin` b on a.uid=b.id';
		return array('where'=>$s,'table'=>$table, 'fields'=>$fields,'order'=>'a.`dt` desc,`sort`');
	}
	public function kqanayaftershow($table, $rows)
	{
		$dtobj = c('date');
		foreach($rows as $k=>$rs){
			$rows[$k]['status'] 	= $rs['iswork'];
			$rows[$k]['week']	 	= $dtobj->cnweek($rs['dt']);
			$miaocn	= '';
			if($rs['emiao']>0){
				$stssa = explode(':', $dtobj->sjdate($rs['emiao'],'H:i:s'));
				if($stssa[0]>0)$miaocn=''.$stssa[0].'时';
				$miaocn.=''.$stssa[1].'分'.$stssa[2].'秒';
			}
			$rows[$k]['miaocn'] = $miaocn;
		}
		return array('rows'=>$rows);
	}
	public function kqanayallAjax()
	{
		$dt 	= $this->post('dt');
		$atype 	= $this->post('atype');
		$whe 	= '';
		if($atype=='my')$whe=' and id='.$this->adminid.'';
		m('kaoqin')->kqanayall($dt, $whe);
		echo 'ok';
	}
	
	
	//个人考勤数据库
	public function getmyanaykqAjax()
	{
		$uid 	= (int)$this->post('uid', $this->adminid);
		$month 	= $this->post('month');
		$barr 	= m('kaoqin')->getanay($uid, $month);
		$barrs	=  $toarr	= array();
		foreach($barr as $dt=>$dtrows){
			$str = '';
			foreach($dtrows as $k=>$rs){
				$s 	 	= $rs['state'];
				$state 	= $rs['state'];
				$iswork = $rs['iswork'];
				if($state != '正常' && $iswork==1)$s='<font color=red>'.$s.'</font>';
				if(!isempt($rs['miaocn'])){
					$s.='['.$rs['miaocn'].']';
				}
				if(!isempt($rs['time']))$s.='('.substr($rs['time'],11).')';
				
				if(!isempt($rs['states'])){
					$s		= $rs['states'];
				}else if($iswork==1){
					if(!isset($toarr[$state]))$toarr[$state]=0;
					$toarr[$state]++;
				}
				$str.= ''.$rs['ztname'].'：'.$s.'';
				$str.= '<br>';
				if($iswork==0)$str='<font color=#888888>休息日</font>';
			}
			$barrs[$dt] = $str;
		}
		$barrs['total']	= $toarr;
		$this->returnjson($barrs);
	}
	public function reladanaymyAjax()
	{
		$uid 	= (int)$this->post('uid', $this->adminid);
		$month 	= $this->post('month');
		m('kaoqin')->kqanaymonth($uid, $month);
	}
	
	
	
	
	
	
	
	
	
	
	//考勤统计
	public function kqtotalbeforeshow($table)
	{
		$dt1			= $this->post('dt1', date('Y-m'));
		$this->months 	= $dt1;
		$key	= $this->post('key');
		$atype	= $this->post('atype');
		$dt 	= $dt1.'-01';
		$enddt	= c('date')->getenddt($dt1);
		$s 		= "and (`quitdt` is null or `quitdt`>='$dt') and (`workdate` is null or `workdate`<='$enddt')";
		if($atype=='my'){
			$s = 'and id='.$this->adminid.'';
		}
		
		if(!isempt($key))$s.=" and (`name` like '%$key%' or `deptname` like '%$key%')";
		
		$fields = 'id,name,deptname,ranking,workdate';
		return array('where'=>$s,'fields'=>$fields,'order'=>'`sort`');
	}
	public function kqtotalaftershow($table, $rows)
	{
		$dtobj 	= c('date');
		$uids 	= '0';
		foreach($rows as $k=>$rs)$uids.=','.$rs['id'].'';
		
		$darr	= $this->db->getall("SELECT uid,state,states FROM `[Q]kqanay` where iswork=1 and dt like '$this->months%' and `uid` in($uids)");
		$sarr 	= array();
		foreach($darr as $k=>$rs){
			$state 	= $rs['state'];
			$uid 	= $rs['uid'];
			if(!isempt($rs['states']))$state='正常';
			if(!isset($sarr[$uid]))$sarr[$uid]=array();
			if(!isset($sarr[$uid][$state]))$sarr[$uid][$state]=0;
			$sarr[$uid][$state]++;
		}
		$farrs = array('正常'=>'state0','未打卡'=>'state1','迟到'=>'state2','早退'=>'state3','请假'=>'qingjia','加班'=>'jiaban');
		
		$kqarr	= $this->db->getall("select sum(totals)as totals,kind,uid from `[Q]kqinfo` where `status`=1 and `uid` in($uids) and `stime` like '$this->months%' and `kind` in('请假','加班') group by `uid`,`kind`");
		foreach($kqarr as $k=>$rs){
			$uid 	= $rs['uid'];
			if(!isset($sarr[$uid]))$sarr[$uid]=array();
			$sarr[$uid][$rs['kind']] = $rs['totals'];
		}
		
		foreach($rows as $k=>$rs){
			$uid 	= $rs['id'];
			if(isset($sarr[$uid])){
				foreach($sarr[$uid] as $zt=>$v){
					if(isset($farrs[$zt])){
						$rows[$k][$farrs[$zt]] = $v;
					}
				}
			}
			$outci	= $this->db->rows('[Q]kqout',"`status`=1 and `uid`=$uid and `outtime` like '$this->months%'");
			if($outci==0)$outci='';
			$rows[$k]['outci'] = $outci;
		}
		return array('rows'=>$rows);
	}
	
	/**
	*	批量导入打卡记录
	*/
	public function addpldkjlAjax()
	{
		$val = $this->post('val');
		if(isempt($val))backmsg('error');
		$arrs 	= explode("\n", $val);
		$oi 	= 0;$uarr = array();
		$dtobj 	= c('date');$adb 	= m('admin');$db = m('kqdkjl');
		foreach($arrs as $valss){
			$name = '';
			$dkdt = '';
			$uid  = 0;
			if(!isempt($valss)){
				$a 		= $this->adtewe(explode('	', $valss),2);
				$name 	= $a[0];
				$dkdt 	= $a[1];
			}
			if(!isempt($name) && !isempt($dkdt)){
				if(!$dtobj->isdate($dkdt))continue;
				if(isset($uarr[$name])){
					$uid = $uarr[$name];
				}else{
					$usar 	= $adb->getrows("`name`='$name'",'id');
					if($this->db->count!=1)continue;
					$uid	= $usar[0]['id'];
					$uarr[$name] = $uid;
				}
				if($db->rows("`uid`='$uid' and `dkdt`='$dkdt'")>0)continue;
				$oi++;
				$db->insert(array(
					'uid'	=> $uid,
					'dkdt'	=> $dkdt,
					'optdt'	=> $this->now,
					'type'	=> 5
				));
			}
		}
		backmsg('','成功导入'.$oi.'条数据');
	}
	private function adtewe($a, $len){
		for($i=0;$i<$len;$i++){
			if(!isset($a[$i]))$a[$i] = '';
		}
		return $a;
	}
	
	
	
	
	
	
	
	
	
	
	public function locationAction()
	{
		$id = (int)$this->get('id');
		$rs = m('location')->getone($id);
		if(!$rs)exit('not found record');
		if($rs['scale']<=0)$rs['scale']=12;
		$this->smartydata['rs'] = $rs;
	}
}