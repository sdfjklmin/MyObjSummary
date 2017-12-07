<?php 
/**
	网址管理后台控制处理
*/
require(ROOT_PATH.'/include/Action.php');

class Action extends mainAction
{
	public $adminid		= 0;
	public $adminuser	= '';
	public $adminname	= '';
	public $admintoken	= '';
	public $loadci		= 0;
	public $flow;
	
	protected $ajaxbool 	= 'false';

	public function getlogin($lx=0)
	{
		$this->ajaxbool		= $this->rock->jm->gettoken('ajaxbool', 'false');
		$this->adminid		= (int)$this->getsession('adminid',0);
		$this->adminuser	= $this->getsession('adminuser');
		$this->adminname	= $this->getsession('adminname');
		$this->admintoken	= $this->getsession('admintoken');
		
		$this->setNowUser($this->adminid, $this->adminname, $this->adminuser);
		if($lx==0)$this->logincheck();
	}

	public function setNowUser($uid, $uname, $user='')
	{
		$this->rock->adminid	= $uid;
		$this->rock->adminname	= $uname;
		$this->rock->adminuser	= $user;
	}
	
	protected function logincheck()
	{
		if($this->adminid==0){
			if(isajax()){
				echo 'sorry! not sign';
			}else{
				$this->rock->location('?m=login');
			}
			exit();
		}
	}
	
	public function initProject()
	{
		$this->getlogin();
		if($this->rock->get('ajaxbool')=='true')return;
		$this->smartydata['adminid']	= $this->adminid;
		$this->smartydata['adminuser']	= $this->adminuser;
		$this->smartydata['adminname']	= $this->adminname;
	}
	
	private function iszclogin()
	{
		$token = $this->admintoken;
		if($this->isempt($token))exit('sorry1');
		$lastt = date('Y-m-d H:i:s',time()-24*3600);
		$rs = m('logintoken')->getone("`uid`='$this->adminid' and `token`='$token' and `cfrom` in('pc','reim') and `moddt`>='$lastt'",'`moddt`');
		if(!$rs)$this->backmsg('登录失效，请重新登录');
	}
	
	public function backmsg($msg='', $demsg='保存成功', $da=array())
	{
		backmsg($msg,$demsg,$da);
	}
	
	public function limitRows($table,$fields='*',$wherea='1=1',$order='',$arr=array())
	{
		$where		= $this->request('where');
		$keywhere	= $this->request('keywhere');
		$where 		= $this->jm->uncrypt($this->rock->iconvsql($where));
		$keywhere 	= $this->jm->uncrypt($this->rock->iconvsql($keywhere));
		$where  	= $this->rock->covexec($where);
		$keywhere  	= $this->rock->covexec($keywhere);
		
		$wherea	  .= " $where $keywhere";
		$wherea	   = $this->db->filterstr($wherea);
		$order	   = $this->getOrder($order);
		$group	   = '';
		if(isset($arr['group']))$group=" group by ".$arr['group']." ";
		
		$limitall	= false;
		if(isset($arr['all']))$limitall= $arr['all'];
		
		if(isset($arr['sou'])){
			$wherea		= str_replace($arr['sou'],$arr['rep'],$wherea);
			$order		= str_replace($arr['sou'],$arr['rep'],$order);
		}
		$sql		= "select SQL_CALC_FOUND_ROWS $fields from $table where $wherea $group $order ";
		if(!$limitall)$sql.=' '.$this->getLimit();
		$rows		= $this->db->getall($sql);
		$total		= $this->db->found_rows();
		if(!is_array($rows))$rows = array();
		return array(
			'total'	=> $total,
			'rows'	=> $rows,
			'sql'	=> $sql
		);
	}
	
	public function getLimit()
	{
		$start  = (int)$this->rock->post('start',0);
		$limit  = (int)$this->rock->post('limit',15);
		$str	= '';
		if($limit > 0)$str =" limit $start,$limit";	
		return $str;
	}
	
	public function getOrder($order='')
	{
		$sort  		= $this->rock->iconvsql($this->post('sort'),1);
		$dir  		= strtolower($this->post('dir'));
		$highorder	= $this->rock->iconvsql($this->post('highorder'));
		$asort		= '';
		if($sort != '' && $dir !=''){
			if(!contain('ascdesc',$dir))$dir='desc';
			$sorta	= $sort;
			$asort=' '.$sorta.' '.$dir.'';
		}
		if($asort != '')$order = $asort;
		if($highorder != '')$order = $highorder;
		if($order != '')$order=" order by $order ";
		return $order;
	}

	public function publicdelAjax()
	{
		$this->iszclogin();
		$id		= $this->rock->post('id');
		$table	= $this->rock->iconvsql($this->rock->post('table','',1),1);
		$modenum= $this->rock->post('modenum');
		if(getconfig('systype')=='demo')$this->showreturn('', '演示数据禁止删除', 201);
		if($id=='')$this->showreturn('', 'sorry', 201);
		$isadmin= (int)$this->getsession('isadmin');
		if($modenum==''){
			if($isadmin!=1)$this->showreturn('','只有管理员才能操作' , 201);
			if(substr($table,0,5)=='flow_'||$table=='todo'||$table=='option'||$table=='menu'){
				m($table)->delete("`id` in($id)");
			}else{
				$this->showreturn('','未设置删除权限' , 201);
			}
		}else{
			$aid	= explode(',', $id);
			foreach($aid as $mid){
				$msg 	= m('flow')->opt('deletebill', $modenum, $mid, '');
				if($msg != 'ok')$this->showreturn('', $msg, 201);
			}
		}
		$this->showreturn('');
	}	
	
	public function publicstoreAjax()
	{
		$this->iszclogin();
		$table			= $this->rock->iconvsql($this->request('tablename_abc','',1),1);
		$fields			= '*';
		$order			= $this->rock->iconvsql($this->request('defaultorder'));
		$aftera			= $this->request('storeafteraction');
		$modenum		= $this->post('modenum');
		$atype			= $this->post('atype');
		$execldown		= $this->request('execldown');
		$this->loadci	= (int)$this->request('loadci');
		$where			= '1=1 ';
		$beforea		= $this->request('storebeforeaction');
		$tables 		= $this->T($table);
		if($modenum != ''){
			$this->flow = m('flow')->initflow($modenum);
			$nas		= $this->flow->billwhere($this->adminid, $atype);
			$_wehs		= $nas['where'];
			if(!isempt($nas['order']))$order 	= $nas['order'];
			if(!isempt($nas['fields']))$fields 	= $nas['fields'];
			if($_wehs!='')$where .= ' '.$_wehs.' ';
			$_tabsk		= $nas['table'];
			if(contain($_tabsk,' ')){
				$tables	= $_tabsk;
			}else{
				$table	= $_tabsk;
				$tables = $this->T($table);
			}
		}
		if($beforea != ''){
			if(method_exists($this, $beforea)){
				$nas	= $this->$beforea($table);
				if(is_array($nas)){
					if(isset($nas['where']))$where .= $nas['where'];
					if(isset($nas['order']))$order = $nas['order'];
					if(isset($nas['fields']))$fields = $nas['fields'];
					if(isset($nas['table']))$tables = $nas['table'];
				}else{
					$where .= $nas;
				}
			}
		}
		$arr	= $this->limitRows($tables, $fields, $where, $order);
		$total	= $arr['total'];
		$rows	= $arr['rows'];

		$bacarr	= array(
			'totalCount'=> $total,
			'rows'		=> $rows
		);
		if(method_exists($this, $aftera)){
			$narr	= $this->$aftera($table, $rows);
			if(is_array($narr)){
				foreach($narr as $kv=>$vv)$bacarr[$kv]=$vv;
			}
		}
		$statusarr = explode(',','<font color=blue>待审核</font>,<font color=green>已审核</font>,<font color=red>未通过</font>');
		if($this->flow){
			$rows = $bacarr['rows'];
			foreach($rows as $k=>$rs){
				if($this->flow->isflow==1 && isset($rs['status']))$rs['statustext'] 	= $statusarr[$rs['status']];
				$rows[$k] 			= $this->flow->flowrsreplace($rs);
			}
			$bacarr['rows'] = $rows;
		}
		if($execldown == 'true'){
			$this->exceldown($bacarr);
			return;
		}
		$this->returnjson($bacarr);
	}
	
	public function publictreestoreAjax()
	{
		$table	= $this->rock->iconvsql($this->rock->post('tablename_abc'),1);
		$order	= $this->rock->iconvsql($this->rock->get('order'));
		$fistid	= $this->rock->get('fistid','0');
		$rows	= $this->publictreestore($fistid, $table, $order);
		echo json_encode(Array(
			'root'=>'.','children'=>$rows
		));
	}
	public function publictreestore($pid, $table, $order){
		$db 		= m($table);
		$expandall	= $this->rock->get('expandall');
		$pidfields	= $this->rock->get('pidfields','pid');
		$idfields	= $this->rock->get('idfields','id');
		$wheres		= $this->rock->iconvsql($this->rock->post('where'));
		
		$where	= "`$pidfields`='$pid' $wheres";
		if($order!='')$where.=" order by `$order`";
		$rows = $db->getall($where);
		foreach($rows as $k=>$rs){
			$id	= $rs['id'];
			$rows[$k]['leaf'] 	= true;
			$rows[$k]['sid']	= $id;
			if($expandall=='true')$rows[$k]['expanded']	= true;
			$total	= $db->rows("`$pidfields`='".$rs[$idfields]."' $wheres");
			if($total >0){
				$rows[$k]['leaf'] = false;
				$rows[$k]['children'] = $this->publictreestore($rs[$idfields], $table, $order);
			}else{
				$rows[$k]['children'] = array();
			}
		}
		return $rows;
	}
	
	/**
		公共保存页面
	*/
	public function publicsaveAjax()
	{
		$this->iszclogin();
		$msg	= '';
		$success= false;
		$table	= $this->rock->iconvsql($this->post('tablename_postabc'),1);
		$id		= (int)$this->post('id');
		$oldrs  = false;
		if($table !='' ){
			$db		= m($table);
			$where	= "`id`='$id'";
			if($id==0)$where='';
			$msgerrortpl 		= $this->post('msgerrortpl');
			$aftersavea			= $this->post('aftersaveaction', 'publicaftersave');
			$beforesavea		= $this->post('beforesaveaction', 'publicbeforesave');
			$submditfi 			= $this->post('submitfields_postabc');
			$editrecord			= $this->post('editrecord_postabc');
			$fileid 			= $this->post('fileid', '0');
			$isturn 			= (int)$this->post('isturn_postabc', '1');
			$int_type 			= ','.$this->post('int_filestype').',';
			$md5_type 			= ','.$this->post('md5_filestype').',';
			if($submditfi !=''){
				$fields	= explode(',', $submditfi);
				$uaarr	= array();
				foreach($fields as $field){
					$val	= $this->post(''.$field.'');
					$type	= $this->post(''.$field.'_fieldstype');
					$boa	= true;
					if($this->contain($int_type, ','.$field.',')){
						$val = (int)$val;
					}
					if($this->contain($md5_type, ','.$field.',')){
						if($val=='')$boa=false;
						$val = md5($val);
					}
					if($boa)$uaarr[$field]=$val;
				}
				
				$otherfields		= $this->post('otherfields');
				$addotherfields		= $this->post('add_otherfields');
				$editotherfields	= $this->post('edit_otherfields');
				if($id == 0)$otherfields.=','.$addotherfields.'';
				if($id > 0)$otherfields.=','.$editotherfields.'';
				if($otherfields != ''){
					$otherfields = str_replace(array('{now}','{date}','{admin}','{adminid}'),array($this->now,date('Y-m-d'),$this->adminname,$this->adminid),$otherfields);
					$fiarsse = explode(',', $otherfields);
					foreach($fiarsse as $ffes){
						if($ffes!=''){
							$ssare = explode('=', $ffes);
							$lea	= substr($ssare[1],0,1);
							if($lea == '['){
								$uaarr[$ssare[0]]=$uaarr[substr($ssare[1],1,-1)];
							}else{
								$uaarr[$ssare[0]]=$ssare[1];
							}
						}
					}
				}
				
				$ss 	= '';
				if(!$this->isempt($beforesavea)){
					if(method_exists($this, $beforesavea)){
						$befa = $this->$beforesavea($table, $uaarr, $id);
						if(is_string($befa)){
							$ss = $befa;
						}else{
							if(isset($befa['msg']))$ss=$befa['msg'];
							if(isset($befa['rows'])){
								foreach($befa['rows'] as $bk=>$bv)$uaarr[$bk]=$bv;
							}
						}
					}	
				}
				$msg 	= $ss;	
				$idadd 	= false;
				if($msg == ''){
					if($id>0 && $editrecord=='true')$oldrs = $db->getone($id);
					if($db->record($uaarr, $where)){
						$msg	= '处理成功';
						$success= true;
						if($id == 0){
							$id = $this->db->insert_id();
							$idadd = true;
						}
						if($fileid !='0')m('file')->addfile($fileid,$table,$id);
						if(!$this->isempt($aftersavea)){
							if(method_exists($this, $aftersavea)){
								$this->$aftersavea($table, $uaarr, $id, $idadd);
							}
						}
						if($oldrs){
							$newrs = $db->getone($id);
							//c('edit')->record($table,$id, $oldrs, $newrs, 2);
						}
					}else{
						$msg = 'Error:'.$this->db->error();
					}
				}
			}
		}else{
			$msg = '错误表名';
		}
		if($msg=='')$msg='处理失败';
		$arr = array('success'=>$success,'msg'=>$msg,'id'=>$id);
		echo json_encode($arr);
	}
	
	public function publicsavevalueAjax()
	{
		$this->iszclogin();
		$table	= $this->rock->iconvsql($this->rock->post('tablename','',1),1);
		$id		= $this->rock->post('id', '0');
		$fields	= $this->rock->post('fieldname');
		$value	= $this->rock->post('value');
		$where	= "`id` in($id)";
		m($table)->record(array($fields=>$value), $where);
		$fiesa  = $this->rock->request('fieldsafteraction');
		if($fiesa!=''){
			if(method_exists($this, $fiesa)){
				$this->$fiesa($table, $fields, $value, $id);
			}	
		}
		echo 'success';
	}
	
	public function exceldown($arr)
	{
		$fields = explode(',', $this->post('excelfields','',1));
		$header = explode(',', $this->post('excelheader','',1));
		$title	= $this->post('exceltitle','',1);
		$rows	= $arr['rows'];
		$headArr	= array();
		for($i=0; $i<count($fields); $i++){
			$headArr[$fields[$i]] = $header[$i];
		}
		$url 		= c('html')->execltable($title, $headArr, $rows);
		$this->returnjson(array(
			'url'		=> $url, 
			'totalCount'=> $arr['totalCount'],
			'downCount' => count($rows)
		));
	}
	
	public function getoptionAjax()
	{
		$num = $this->get('num');
		$arr = m('option')->getdata($num);
		echo json_encode($arr);
	}
}

class ActionNot extends Action
{
	public function publicsavevalueAjax(){}
	public function publicsaveAjax(){}
	public function publicdelAjax(){}
	public function publicstoreAjax(){}
	public function publictreestoreAjax(){}
	protected function logincheck(){}
	
	protected function mweblogin($lx=0)
	{
		$uid = $this->adminid;
		if($uid==0){
			$uid = (int)$this->getcookie('mo_adminid');
			if($uid>0){
				$db   = m('login');
				$onrs = $db->getone("`uid`=$uid and `cfrom`='mweb' and `online`=1",'`name`,`token`,`id`');
				if($onrs){
					$db->setsession($uid, $onrs['name'], $onrs['token']);
					$db->update("moddt='$this->now'", $onrs['id']);
				}else{
					$uid = 0;
				}
			}
		}
		if($uid==0){
			if(isajax()){
				echo 'sorry! not sign';
			}else{
				$this->rock->location('index.php?m=login&d=we&ltype='.$lx.'');
			}
			exit();
		}
	}
}