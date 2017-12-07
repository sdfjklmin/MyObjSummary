<?php
class inputAction extends ActionNot
{
	public $mid = 0;
	public $flow;
	
	public function initAction()
	{
		$aid 	= (int)$this->get('adminid');
		$token 	= $this->get('token');
		m('login')->autologin($aid, $token);
		$this->getlogin(1);
	}
	
	private $fieldarr = array();
	private $ismobile = 0;
	
	protected $moders = array();
	
	
	//保存前处理，主要用于判断是否可以保存
	protected function savebefore($table,$arr, $id, $addbo){}
	
	//保存后处理，主要用于判断是否可以保存
	protected function saveafter($table,$arr, $id, $addbo){}


	public function saveAjax()
	{
		$id				= (int)$this->request('id');
		$modenum		= $this->request('modenum');
		$uid			= $this->adminid;
		$this->flow		= m('flow')->initflow($modenum);
		$this->moders	= $this->flow->moders;
		$modeid			= $this->moders['id'];
		$isflow			= $this->moders['isflow'];
		$flownum		= $this->moders['num'];
		$table			= $this->moders['table'];
		if($this->isempt($table))$this->backmsg('模块未设置表名');
		$fieldsarr		= m('flow_element')->getrows("`mid`='$modeid' and `islu`=1 and `iszb`=0",'`name`,`fields`,`isbt`,`fieldstype`,`savewhere`,`data`,`iszb`','`sort`');
		if(!$fieldsarr)$this->backmsg('没有录入元素');
		$db	   = m($table);$subna = '提交';$addbo = false;$where = "`id`='$id'"; $oldrs = false;
		$this->mdb = $db;
		if($id==0){
			$where = '';
			$addbo = true;
		}else{
			$oldrs = $db->getone($id);
			if(!$oldrs)$this->backmsg('记录不存在');
			if($isflow==1){
				$bos = false;
				if($oldrs['uid']==$uid||$oldrs['optid']==$uid)$bos=true;
				if($oldrs['status']==1)$bos=false;
				if(!$bos)$this->backmsg('不允许编辑,可能已审核通过/不是你的单据');
			}
			$subna = '编辑';
		}
		$uaarr = $farrs = array();
		foreach($fieldsarr as $k=>$rs){
			$fid = $rs['fields'];
			$val = $this->post($fid);
			if($rs['isbt']==1&&$this->isempt($val))$this->backmsg(''.$rs['name'].'不能为空');
			$uaarr[$fid] = $val;
			$farrs[$fid] = array('name' => $rs['name']);
		}
		
		//人员选择保存的
		foreach($fieldsarr as $k=>$rs){
			if(substr($rs['fieldstype'],0,6)=='change'){
				if(!$this->isempt($rs['data'])){
					$fid = $rs['data'];
					if(isset($uaarr[$fid]))continue;
					$val = $this->post($fid);
					if($rs['isbt']==1&&$this->isempt($val))$this->backmsg(''.$rs['name'].'id不能为空');
					$uaarr[$fid] = $val;
					$farrs[$fid] = array('name' => $rs['name'].'id');
				}
			}
			if($rs['fieldstype']=='num'){
				$fid = $rs['fields'];
				if($this->flow->rows("`$fid`='{$uaarr[$fid]}' and `id`<>$id")>0)$uaarr[$fid]=$this->flow->createbianhao($rs['data'], $fid);
			}
		}
		
		//默认字段保存
		$allfields = $this->db->getallfields('[Q]'.$table.'');
		if(in_array('optdt', $allfields))$uaarr['optdt'] = $this->now;
		if(in_array('optid', $allfields))$uaarr['optid'] = $this->adminid;
		if(in_array('optname', $allfields))$uaarr['optname'] = $this->adminname;
		if(in_array('uid', $allfields))$uaarr['uid'] = $this->post('uid', $this->adminid);
		if(in_array('applydt', $allfields) && $id==0)$uaarr['applydt'] = $this->post('applydt', $this->date);
		if($addbo){
			if(in_array('createdt', $allfields))$uaarr['createdt'] = $this->now;
			if(in_array('adddt', $allfields))$uaarr['adddt'] = $this->now;
			if(in_array('createid', $allfields))$uaarr['createid'] = $this->adminid;
			if(in_array('createname', $allfields))$uaarr['createname'] = $this->adminname;
		}
		if($isflow==1){
			$uaarr['status']= '0';
		}else{
			if(in_array('status', $allfields))$uaarr['status'] = (int)$this->post('status', '1');
			if(in_array('isturn', $allfields))$uaarr['isturn'] = 1;
		}
		
		//保存条件的判断
		foreach($fieldsarr as $k=>$rs){
			$ss  = $this->flow->savedatastr($uaarr[$rs['fields']], $rs, $uaarr);
			if($ss!='')$this->backmsg($ss);
		}
		
		//判断保存前的
		$ss 	= '';
		$befa 	= $this->savebefore($table, $uaarr, $id, $addbo);
		if(is_string($befa)){
			$ss = $befa;
		}else{
			if(isset($befa['msg']))$ss=$befa['msg'];
			if(isset($befa['rows'])){
				if(is_array($befa['rows']))foreach($befa['rows'] as $bk=>$bv)$uaarr[$bk]=$bv;
			}
		}
		if(!$this->isempt($ss))$this->backmsg($ss);
		
		$bo = $db->record($uaarr, $where);;
		if(!$bo)$this->backmsg($this->db->error());
		
		if($id==0)$id = $this->db->insert_id();
		m('file')->addfile($this->post('fileid'), $table, $id);
		
		$this->savesubtable($id,'0', $addbo);//保存子表
		
		//保存后处理
		$this->saveafter($table,$uaarr, $id, $addbo);
		
		$msg 	= '';
		$this->flow->loaddata($id, false);
		$this->flow->submit($subna);
		
		$this->backmsg('', $msg, $id);
	}
	
	public function getsubtabledata($xu)
	{
		$arr 	= array();
		$oi 	= (int)$this->post('sub_totals'.$xu.'');
		if($oi<=0)return $arr;
		$modeid		= $this->moders['id'];
		$iszb		= $xu+1;
		$farr		= m('flow_element')->getrows("`mid`='$modeid' and `islu`=1 and `iszb`=$iszb",'`name`,`fields`,`isbt`,`savewhere`,`dev`','`sort`');
		$sort 		= 0;
		for($i=0; $i<$oi; $i++){
			$sid  = (int)$this->post('sid'.$xu.'_'.$i.'');
			$bos  = true;
			$uaarr['id'] = $sid;
			foreach($farr as $k=>$rs){
				$fid= $rs['fields'];
				$na = ''.$fid.''.$xu.'_'.$i.'';
				$val= $this->post($na);
				if($rs['isbt']==1&&$this->isempt($val))$bos=false;
				$uaarr[$fid] = $val;
			}
			if(!$bos)continue;
			$uaarr['sort'] 	= $sort;
			$sort++;
			$arr[] = $uaarr;
		}
		return $arr;
	}
	
	private function savesubtable($mid, $xu, $addbo)
	{
		$data 	= $this->getsubtabledata($xu);
		$len 	= count($data);
		if($len<=0)return;
		$table		= $this->moders['table'];
		$tables		= $this->moders['tables'];if(isempt($tables))$tables='items';
		$idss		= '0';
		$dbs 		= m($tables);
		
		$allfields 	= $this->db->getallfields('[Q]'.$tables.'');
		$oarray 	= array();
		if(in_array('optdt', $allfields))$oarray['optdt'] 		= $this->now;
		if(in_array('optid', $allfields))$oarray['optid'] 		= $this->adminid;
		if(in_array('optname', $allfields))$oarray['optname'] 	= $this->adminname;
		if(in_array('uid', $allfields))$oarray['uid'] 			= $this->post('uid', $this->adminid);
		if(in_array('applydt', $allfields) && $addbo)$oarray['applydt']	= $this->post('applydt', $this->date);
		if(in_array('status', $allfields))$oarray['status']		= 0;
		
		foreach($data as $k=>$uaarr){
			$sid 			= $uaarr['id'];
			$where			= "`id`='$sid'";
			$uaarr['mid'] 	= $mid;
			if($sid==0)$where = '';
			foreach($oarray as $k1=>$v1)$uaarr[$k1]=$v1;
			
			$dbs->record($uaarr, $where);
			if($sid==0)$sid = $this->db->insert_id();
			$idss.=','.$sid.'';
		}
		$delwhere = "`mid`='$mid' and `id` not in($idss)";
		$dbs->delete($delwhere);
	}
	
	//获取数据
	public function getdataAjax()
	{
		$flownum = $this->request('flownum');
		$id		 = (int)$this->request('mid');
		$arr 	 = m('flow')->getdataedit($flownum, $id);
		$tables	 = $arr['tables'];
		$modeid	 = $arr['modeid'];
		$subdata0	 = array();
		if(!isempt($tables)){
			$subdata0 = m($tables)->getall('mid='.$id.'','*','`sort`');
		}
		$arr['subdata'] = array(
			'subdata0'	=> $subdata0
		);
		$this->backmsg('', '', $arr);
	}
	
	
	public function lumAction()
	{
		$this->ismobile = 1;
		$isheader = 0;
		if($this->web != 'wxbro' && $this->get('show')=='we')$isheader=1;
		$this->assign('isheader', $isheader);
		$this->luactions();
	}
	
	public function luAction()
	{
		$this->ismobile = 0;
		$this->luactions();
	}

	public function lusAction()
	{
		$this->ismobile = 1;
		$menuid	= (int)$this->get('menuid');
		$fields 	= m('flow_menu')->getmou('fields', $menuid);
		if(isempt($fields))exit('sorry;');
		$fields	= str_replace(',',"','", $fields);
		$stwhe	= "and `fields` in('$fields')";
		$this->luactions(1, $stwhe);
	}	
	
	private function luactions($slx=0, $stwhe='')
	{
		$this->tpltype = 'html';
		$uid		= $this->adminid;
		$num		= $this->jm->gettoken('num');
		$mid		= (int)$this->jm->gettoken('mid');
		$this->mid  = $mid;
		$this->flow = m('flow')->initflow($num);
		$moders		= $this->flow->moders;
		$this->smartydata['moders']	= array(
			'num' 	=> $moders['num'],
			'id' 	=> $moders['id'],
			'name' 	=> $moders['name'],
			'names' => $moders['names'],
			'isflow'=> $moders['isflow'],
		);
		$modeid 	= $moders['id'];
		if($mid==0){
			$isadd = m('view')->isadd($modeid, $uid);
			if(!$isadd)exit('无权添加['.$moders['name'].']的数据;');
		}
		
		$content 	= '';
		$oldrs 		= m($moders['table'])->getone($mid);

		
		$fieldarr 	= m('flow_element')->getrows("`mid`='$modeid' and `iszb`=0 $stwhe",'fields,fieldstype,name,dev,data,isbt,islu,attr,iszb','`sort`');
		$modelu		= '';
		foreach($fieldarr as $k=>$rs){
			if($slx==1 && $oldrs){
				$rs['value'] = $oldrs[$rs['fields']];
			}
			$this->fieldarr[$rs['fields']] = $rs;
			if($rs['islu'] || $stwhe!='')$modelu.='{'.$rs['fields'].'}';
		}
		
		$this->smartydata['fieldsjson']	= json_encode($fieldarr);
		$this->moders	= $moders;
		
		if($this->ismobile==0){
			$path 			= ''.P.'/flow/page/input_'.$num.'.html';
			if(file_exists($path)){
				$content 	= file_get_contents($path);
			}
		}else{
			$content = $modelu;
			$zbstr 	 = m('input')->getsubtable($modeid,1,1,1);
			if($zbstr!='')$content.='<tr><td  style="padding:5px;" colspan="2"><div><b>'.$moders['names'].'</b></div><div>'.$zbstr.'</div></td></tr>';
		}
		
		if($content=='')exit('未设置录入页面');
		
		
		$this->actclss	= $this;
		$pathss 		= ''.P.'/flow/input/mode_'.$num.'Action.php';
		if(file_exists($pathss)){
			include_once($pathss);
			$clsnam 	= 'mode_'.$num.'ClassAction';
			$this->actclss 	= new $clsnam();
		}
		
		//初始表单插件元素
		$this->inputobj	= c('input');
		$this->inputobj->ismobile 	= $this->ismobile;
		$this->inputobj->fieldarr 	= $this->fieldarr;
		$this->inputobj->flow 		= $this->flow;
		$this->inputobj->mid 		= $this->mid;
		$this->inputobj->initUser($this->adminid);
		
		preg_match_all('/\{(.*?)\}/', $content, $list);
		foreach($list[1] as $k=>$nrs){
			$str		= $this->inputobj->getfieldcont($nrs, $this->actclss);
			$content	= str_replace('{'.$nrs.'}', $str, $content);
		}
		
		$content 	 	= $this->pisubduolie($content, $modeid, 1);//多列子表匹配的是[]
		$content		= str_replace('*','<font color=red>*</font>', $content);
		
		$course			= array();
		if($moders['isflow']==1){
			$course[]= array('name'=>'提交','id'=>0);
			$courses = m('flow_course')->getall('setid='.$modeid.'','name,checktype,id,checktypename,`explain`');
			foreach($courses as $k=>$rs1){
				$na = $rs1['name'];
				if(!$this->isempt($rs1['explain']))$na.= '<br><span style="font-size:12px">('.$rs1['explain'].')</span>';
				$rs1['name'] = $na;
				$course[]=$rs1;
			}
			$course[]= array('name'=>'结束','id'=>-1);
		}
		$this->title  	= $moders['name'];
		$this->smartydata['content']	= $content;
		$this->smartydata['mid']		= $mid;
		$this->smartydata['course']		= $course;
	}
	
	//多行子表的
	private function pisubduolie($content, $modeid, $xu)
	{
		$oi 		= $xu-1;
		$fieldarr 	= m('flow_element')->getrows("`mid`='$modeid' and `iszb`='$xu'",'fields,fieldstype,name,dev,data,isbt,islu,attr','`sort`');
		if(!$fieldarr)return $content;
		$this->fieldarr = array();
		$this->fieldarr['xuhao'.$oi.''] = array(
			'fields' 	=> 'xuhao'.$oi.'',
			'fieldstype'=> 'xuhao',
			'data' 		=> '',
			'attr' 		=> 'style="text-align:center" readonly temp="xuhao"',
			'dev'	 	=> '1',
			'isbt'		=> '0',
			'fieldss'	=> 'sid'.$oi.''
		);
		foreach($fieldarr as $k=>$rs){
			$this->fieldarr[$rs['fields'].''.$oi.''] = $rs;
		}
		$this->inputobj->fieldarr 	= $this->fieldarr;
		preg_match_all('/\[(.*?)\]/', $content, $list);
		foreach($list[1] as $k=>$nrs){
			if(!$this->isempt($nrs)){
				$fida= explode(',', $nrs);$xu0='0';
				if(isset($fida[1]))$xu0=$fida[1];
				
				$str		= $this->inputobj->getfieldcont($fida[0], $this->actclss,'_'.$xu0.'', $xu);
				$content	= str_replace('['.$nrs.']', $str, $content);
			}
		}
		return $content;
	}
}

class inputClassAction extends inputAction{}