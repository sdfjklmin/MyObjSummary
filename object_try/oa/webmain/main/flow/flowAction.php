<?php
class flowClassAction extends Action
{
	public function loaddataAjax()
	{
		$id		= (int)$this->get('id');
		$data	= m('flow_set')->getone($id);
		$arr 	= array(
			'data'		=> $data
		);
		echo json_encode($arr);
	}
	
	public function modeafter($table, $rows)
	{
		return array(
			'qian' => PREFIX
		);
	}
	
	public function loaddatacourseAjax()
	{
		$id		= (int)$this->get('id');
		$setid	= (int)$this->get('setid');
		$data	= m('flow_course')->getone($id);
		$arr 	= array(
			'data'		=> $data,
			'wherelist' => m('flow_where')->getall('setid='.$setid.'','id,name','sort')
		);
		echo json_encode($arr);
	}
	
	public function loaddatawhereAjax()
	{
		$id		= (int)$this->get('id');
		$data	= m('flow_where')->getone($id);
		$arr 	= array(
			'data'		=> $data
		);
		echo json_encode($arr);
	}
	
	public function flowsetsavebefore($table, $cans)
	{
		$tab = $cans['table'];
		if(!c('check')->iszgen($tab))return '表名格式不对';
		if($cans['isflow']==1 && isempt($cans['sericnum'])) return '有流程必须有写编号规则，请参考其他模块填写';
	}
	
	public function flowsetsaveafter($table, $cans)
	{
		$isflow = $cans['isflow'];
		$tab  	= $cans['table'];
		$tabs  	= $cans['tables'];
		$alltabls = array();
		if(!isempt($tabs)){
			$alltabls 	= $this->db->getalltable();
			if(!in_array(''.PREFIX.''.$tabs.'', $alltabls)){
				$sql = "CREATE TABLE `[Q]".$tabs."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` smallint(6) DEFAULT '0' COMMENT '对应主表".$tab.".id',
  `sort` smallint(6) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`),KEY `mid` (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
				$bo = $this->db->query($sql);
			}else{
				$fields = $this->db->getallfields(''.PREFIX.''.$tabs.'');
				$str 	= '';
				if(!in_array('mid', $fields))$str.=",add `mid` smallint(6) DEFAULT '0' COMMENT '对应主表".$tab.".id";
				if(!in_array('sort', $fields))$str.=",add `sort` smallint(6) DEFAULT '0' COMMENT '排序号'";
				if($str!=''){
					$sql = 'alter table `'.PREFIX.''.$tabs.'` '.substr($str,1).'';
					$this->db->query($sql);
				}
			}
		}
		
		if(isempt($tab))return;
		if(!$alltabls)$alltabls 	= $this->db->getalltable();
		if($isflow==0){
			if(!in_array(''.PREFIX.''.$tab.'', $alltabls)){
				$sql = "CREATE TABLE `[Q]".$tab."` (`id` int(11) NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`))ENGINE=MyISAM  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
				$bo = $this->db->query($sql);
			}
			return;
		}
		
		if(!in_array(''.PREFIX.''.$tab.'', $alltabls)){
			$sql = "CREATE TABLE `[Q]".$tab."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` smallint(6) DEFAULT '0',
  `optdt` datetime DEFAULT NULL COMMENT '操作时间',
  `optid`  smallint(6) DEFAULT '0',
  `optname` varchar(20) DEFAULT NULL COMMENT '操作人',
  `applydt` date DEFAULT NULL COMMENT '申请日期',
  `explain` varchar(500) DEFAULT NULL COMMENT '说明',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `isturn` tinyint(1) DEFAULT '1' COMMENT '是否提交',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
			$bo = $this->db->query($sql);
		}else{
			$fields = $this->db->getallfields(''.PREFIX.''.$tab.'');
			$str 	= '';
			if(!in_array('uid', $fields))$str.=",add `uid` smallint(6) DEFAULT '0'";
			if(!in_array('optdt', $fields))$str.=",add `optdt` datetime DEFAULT NULL COMMENT '操作时间'";
			if(!in_array('optid', $fields))$str.=",add `optid` smallint(6) DEFAULT '0'";
			if(!in_array('optname', $fields))$str.=",add `optname` varchar(20) DEFAULT NULL COMMENT '操作人'";
			if(!in_array('applydt', $fields))$str.=",add `applydt` date DEFAULT NULL COMMENT '申请日期'";
			if(!in_array('explain', $fields))$str.=",add `explain` varchar(500) DEFAULT NULL COMMENT '说明'";
			if(!in_array('status', $fields))$str.=",add `status` tinyint(1) DEFAULT '1' COMMENT '状态'";
			if(!in_array('isturn', $fields))$str.=",add `isturn` tinyint(1) DEFAULT '1' COMMENT '是否提交'";
			if($str!=''){
				$sql = 'alter table `'.PREFIX.''.$tab.'` '.substr($str,1).'';
				$this->db->query($sql);
			}
		}
	}
	
	public function elementafter($table, $rows)
	{
		$moders = m('flow_set')->getone($this->mid);
		
		$tass 	= $moders['table'];
		$tasss 	= $moders['tables'];
		
		$farr	= $this->db->gettablefields('[Q]'.$tass.'');
		
		$farrs[]= array('id'=>'','name'=>'————↓以下表('.$tass.')的字段————');
		foreach($farr as $k=>$rs){
			$farrs[]= array('id'=>$rs['name'],'name'=>'['.$rs['name'].']'.$rs['explain'].'');
		}
		if(!isempt($tasss)){
			$farr	= $this->db->gettablefields('[Q]'.$tasss.'');
			
			$farrs[]= array('id'=>'','name'=>'————↓以下多行子表('.$tasss.')的字段————');
			foreach($farr as $k=>$rs){
				$farrs[]= array('id'=>$rs['name'],'name'=>'['.$rs['name'].']'.$rs['explain'].'');
			}
		}
		
		return array(
			'flowarr'=>$this->getmodearr(),
			'moders'=>$moders,
			'fieldsarr' => $farrs,
			'fieldstypearr'=> $this->option->getdata('flowinputtype')
		);
	}
	
	public function elementbefore($table)
	{
		$mid = (int)$this->post('mid');
		$this->mid = $mid;
		return 'and `mid`='.$mid.'';
	}
	
	
	public function flowmenubefore($table)
	{
		$mid = (int)$this->post('mid');
		$this->mid = $mid;
		return 'and `setid`='.$mid.'';
	}
	
	public function flowmenuafter($table, $rows)
	{
		
		return array(
			'flowarr'=>$this->getmodearr()
		);
	}
	
	
	private function getmodearr()
	{
		return m('mode')->getmodearr();
	}
	
	
	
	public function inputzsAction()
	{
		$setid	= $this->get('setid');
		$atype	= (int)$this->get('atype','0');
		$rs 	= m('flow_set')->getone("`id`='$setid'");
		if(!$rs)exit('sorry!');
		$this->smartydata['rs'] = $rs;
		$this->title  = $rs['name'].'_展示页面设置';
		$fleftarr 	= m('flow_element')->getrows("`mid`='$setid'",'*','`iszb`,`sort`');
		$modenum	= $rs['num'];
		$fleft[]= array('base_name', '申请人',0);
		$fleft[]= array('base_deptname', '申请部门',0);
		$fleft[]= array('base_sericnum', '单号',0);
		$fleft[] = array('file_content', '相关文件',0);
		$iszb 	= 0;
		foreach($fleftarr as $k=>$brs){
			$bt='';
			if($brs['isbt']==1)$bt='*';
			$iszbs = $brs['iszb'];
			if($iszbs>0&&$iszb==0){
				$fleft[]= array('', '<font color=#ff6600>—第'.$iszbs.'个多列子表—</font>', $iszbs);
				$fleft[]= array('xuhao', '序号', $iszbs);
			}
			$iszb	= $iszbs;
			$fleft[]= array($brs['fields'], $bt.$brs['name'], $iszb);
		}

		
		$this->smartydata['fleft'] = $fleft;
		$this->smartydata['atype'] = $atype;

		$path 		= ''.P.'/flow/page/view_'.$modenum.'_'.$atype.'.html';
		$content 	= '';
		if(file_exists($path)){
			$content = file_get_contents($path);
		}
		$this->smartydata['content'] = $content;
	}
	
	
	
	
	public function inputAction()
	{
		$setid	= $this->get('setid');
		$atype	= $this->get('atype');
		$rs 	= m('flow_set')->getone("`id`='$setid'");
		if(!$rs)exit('sorry!');
		$this->smartydata['rs'] = $rs;
		$this->title  = $rs['name'].'_录入页面设置';
		$fleftarr 	= m('flow_element')->getrows("`mid`='$setid'",'*','`iszb`,`sort`');
		$modenum	= $rs['num'];
		$fleft[]= array('base_name', '申请人',0);
		$fleft[]= array('base_deptname', '申请部门',0);
		$fleft[]= array('base_sericnum', '单号',0);
		$fleft[] = array('file_content', '相关文件',0);
		$iszb 	= 0;
		foreach($fleftarr as $k=>$brs){
			$bt='';
			if($brs['isbt']==1)$bt='*';
			$iszbs = $brs['iszb'];
			if($iszbs>0&&$iszb==0){
				$fleft[]= array('', '<font color=#ff6600>—第'.$iszbs.'个多列子表—</font>', $iszbs);
				$fleft[]= array('xuhao', '序号', $iszbs);
			}
			$iszb	= $iszbs;
			$fleft[]= array($brs['fields'], $bt.$brs['name'], $iszb);
		}

		
		$this->smartydata['fleft'] = $fleft;
		
		$path 		= ''.P.'/flow/page/input_'.$modenum.'.html';
		$content 	= '';
		if(file_exists($path)){
			$content = file_get_contents($path);
		}
		$this->smartydata['content'] = $content;
		$apaths		= ''.P.'/flow/input/inputjs/mode_'.$modenum.'.js';
		if(!file_exists($apaths)){
			$stra='//初始函数
function initbodys(){
	
}';
			$this->rock->createtxt($apaths, $stra);
		}
		
		$apaths		= ''.P.'/flow/input/mode_'.$modenum.'Action.php';
		$apath		= ''.ROOT_PATH.'/'.$apaths.'';
		if(!file_exists($apath)){
			$stra = '<?php
/**
*	此文件是流程模块【'.$modenum.'.'.$rs['name'].'】对应接口文件。
*	可在页面上创建更多方法如：public funciton testactAjax()，用js.getajaxurl(\'testact\',\'mode_'.$modenum.'|input\',\'flow\')调用到对应方法
*/ 
class mode_'.$modenum.'ClassAction extends inputAction{
	
	/**
	*	重写函数：保存前处理，主要用于判断是否可以保存
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id 0添加时，大于0修改时
	*	$addbo Boolean 是否添加时
	*	return array(\'msg\'=>\'错误提示内容\',\'rows\'=> array()) 可返回空字符串，或者数组 rows 是可同时保存到数据库上数组
	*/
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	/**
	*	重写函数：保存后处理，主要保存其他表数据
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id
	*	$addbo Boolean 是否添加时
	*/	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
}	
			';
			$this->rock->createtxt($apaths, $stra);
		}
	}
	
	public function pagesaveAjax()
	{
		$content = $this->post('content');
		$num 	 = $this->post('num');
		$path 	 = ''.P.'/flow/page/input_'.$num.'.html';
		$bo 	 = $this->rock->createtxt($path, $content);
		if(!$bo){
			echo '无法写入文件:'.$path.'';
		}else{
			echo 'success';
		}
	}
	
	public function viewsaveAjax()
	{
		$content = $this->post('content');
		$num 	 = $this->post('num');
		$atype 	 = $this->post('atype','0');
		$path 	 = ''.P.'/flow/page/view_'.$num.'_'.$atype.'.html';
		$bo 	 = $this->rock->createtxt($path, $content);
		if(!$bo){
			echo '无法写入文件:'.$path.'';
		}else{
			echo 'success';
		}
	}
	
	public function getinputAjax()
	{
		$num 	 = $this->post('num');
		$path 	 = ''.P.'/flow/page/input_'.$num.'.html';
		$cont 	 = '';
		if(file_exists($path)){
			$cont = file_get_contents($path);
			$cont = str_replace('*','', $cont);
		}
		echo $cont;
	}
	
	
	
	public function getsubtableAjax()
	{
		$iszb 	= (int)$this->post('iszb');
		$hang 	= (int)$this->post('hang');
		$modeid = (int)$this->post('modeid');
		$str 	= m('input')->getsubtable($modeid, $iszb, $hang);
		if($str=='')$this->backmsg('没有设置第'.$iszb.'个多行子表');
		$this->backmsg('','ok', $str);
	}
	
	
	
	
	
	
	
	public function getmodearrAjax()
	{
		$arr = $this->getmodearr();
		$this->backmsg('','ok', $arr);
	}
	
	
	
	
	public function viewshowbefore($table)
	{
		$this->modeid = (int)$this->post('modeid');
		$this->moders = m('flow_set')->getone($this->modeid);
		$this->isflow = $this->moders['isflow'];
		$table = $this->moders['table'];
		$where = $this->moders['where'];
		if(!isempt($where)){
			$where = $this->rock->covexec($where);
			$where = "and $where";
		}
		return array(
			'table' => '[Q]'.$table,
			'where' => $where
		);
	}
	
	public function viewshowafter($table, $rows)
	{
		$arr = array();
		$ztarr  = explode(',','待处理,已审核,处理不通过');
		$ztarrc = explode(',','blue,green,red');
		foreach($rows as $k=>$rs){
			$zt 	= '';
			if(isset($rs['status']))$zt = $rs['status'];
			$narr['id'] 		= $rs['id'];
			$narr['optname'] 	= @$rs['optname'];
			$narr['modenum'] 	= $this->moders['num'];
			$narr['modename'] 	= $this->moders['name'];
			$narr['optdt'] 		= $rs['optdt'];
			$narr['summary'] 	= $this->rock->reparr($this->moders['summary'], $rs);
			if($this->isflow == 1){
				$zt = '<font color="'.$ztarrc[$zt].'">'.$ztarr[$zt].'</font>';
			}
			$narr['status']		= $zt;
			$arr[] = $narr;
		}
		return array('rows'=>$arr);
	}
	
	public function delmodeshujuAjax()
	{
		$this->modeid 	= (int)$this->post('modeid');
		$mid 			= (int)$this->post('mid');
		$this->moders 	= m('flow_set')->getone($this->modeid);
		if(!$this->moders)backmsg('sorry!');
		$msg	= m('flow')->deletebill($this->moders['num'], $mid);
		if($msg=='ok')$msg='';
		backmsg($msg);
	}
	
	//保存字段判断
	public function elemensavefields($table, $cans)
	{
		$fields = $cans['fields'];
		$name 	= $cans['name'];
		$mid 	= $cans['mid'];
		$type 	= $cans['fieldstype'];
		$lens 	= $cans['lens'];
		$tables = m('flow_set')->getmou('`table`', $mid);
		if(!isempt($tables) && $cans['iszb']==0 && substr($fields,0,5)!='temp_'){
			$allfields = $this->db->getallfields('[Q]'.$tables.'');
			if(!in_array($fields, $allfields)){
				$str = "ALTER TABLE `[Q]".$tables."` ADD `$fields` ";
				if($type=='date' || $type=='datetime' || $type=='time'){
					$str .= ' '.$type.'';
				}else if($type=='number'){
					$str .= ' smallint(6)';
				}else if($type=='checkbox'){
					$str .= ' tinyint(1)';	
				}else if($type=='textarea'){
					$str .= ' varchar(500)';
				}else{
					$str .= ' varchar(50)';
				}
				if(!isempt($cans['dev']))$str.= " DEFAULT '".$cans['dev']."'";
				$str.= " COMMENT '$name'";
				$this->db->query($str);
			}
		}
	}
	
	
	
	
	
	
	public function reloadpipeiAjax()
	{
		$mid 	= (int)$this->post('mid');
		$whe	= '';
		if($mid>0)$whe=' and id='.$mid.'';
		echo m('flow')->repipei($whe);
	}
	
	public function setwherelistafter($table, $rows)
	{
		$dbs = m('flow_where');
		foreach($rows as $k=>$rs){
			$shu = $dbs->rows("`setid`='".$rs['id']."'");
			if($shu>0)$rows[$k]['shu'] = $shu;
		}
		return array('rows'=>$rows);
	}
	
	public function setcourselistafter($table, $rows)
	{
		$dbs = m('flow_course');
		foreach($rows as $k=>$rs){
			$shu = $dbs->rows("`setid`='".$rs['id']."'");
			if($shu>0)$rows[$k]['shu'] = $shu;
		}
		return array('rows'=>$rows);
	}
	
	
	
	public function delmodeAjax()
	{
		if($this->getsession('isadmin')!='1')return;
		$id = (int)$this->post('id','0');
		$mrs = m('flow_set')->getone($id);
		if(!$mrs)return;
		if($mrs['type']=='系统')return;
		$table = $mrs['table'];
		m('flow_bill')->delete("`modeid`='$id'");
		$where = $mrs['where'];
		if(!isempt($where)){
			$where = $this->rock->covexec($where);
			$where = "and $where";
		}
		$rows  = m($table)->getrows('1=1 '.$where.'');
		foreach($rows as $k=>$rs){
			$ssid 	= $rs['id'];
			$dwhere	= "`table`='$table' and `mid`='$ssid'";
			m('flow_log')->delete($dwhere);
			m('reads')->delete($dwhere);
			m('file')->delfiles($table, $ssid);
		}
		m($table)->delete('1=1 '.$where.'');
		m('flow_set')->delete("`id`='$id'");
		$this->db->query("alter table `[Q]$table` AUTO_INCREMENT=1");
	}
}