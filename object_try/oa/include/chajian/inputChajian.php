<?php 
/**
*	系统表单插件
*/
class inputChajian extends Chajian
{
	public $fieldarr 	= array();
	public $flow 		= null;
	public $ismobile 	= 0;
	public $urs 		= array();
	public $mid 		= 0;
	
	protected function initChajian()
	{
		$this->date = $this->rock->date;
		$this->now 	= $this->rock->now;
	}
	
	public function initUser($uid)
	{
		$this->adminid 	= $uid;
		$this->urs  	= m('admin')->getone($uid, '`name`,`deptname`,`ranking`,`deptid`');
		$this->adminname= $this->urs['name'];
	}
	
	public function initFields($stwhe='')
	{
		$fieldarr 	= m('flow_element')->getrows($stwhe,'`fields`,`fieldstype`,`name`,`dev`,`data`,`isbt`,`islu`,`attr`,`savewhere`,`iszb`','`sort`');
		foreach($fieldarr as $k=>$rs){
			$this->fieldarr[$rs['fields']] = $rs;
		}
		return $fieldarr;
	}
	
	public function getfieldcont($fid, $objs=null, $leox='', $iszb=0)
	{
		$fida= explode(',', $fid);$xu0='0';
		$ism = $this->ismobile;
		$fid = $fida[0];
		$str = $val ='';
		if(isset($fida[1]))$xu0=$fida[1];
		if($fid=='base_name'){
			$str = '<input class="inputs" style="border:none;background:none" name="base_name" value="'.$this->adminname.'" readonly>';
		}
		if($fid=='base_deptname'){
			$str = '<input class="inputs" style="border:none;background:none" name="base_deptname" value="'.$this->urs['deptname'].'" readonly>';
		}
		if($fid=='file_content'){
			$str = '<input name="fileid" type="hidden" id="fileidview-inputEl"><div id="view_fileidview" style="width:97%;height:80px;border:1px #cccccc solid; background:white;overflow:auto"></div><div id="fileupaddbtn"><a href="javascript:;" class="blue" onclick="c.upload()"><u>＋添加文件</u></a></div>';
		}
		if($fid=='删'){
			$str='<a href="javascript:;" onclick="c.delrow(this,'.$xu0.')">删</a>';
		}
		if($fid=='新增'){
			$str='<a href="javascript:;" onclick="c.addrow(this,'.$xu0.')">＋新增</a>';
		}
		if($str!='')return $str;
		if(!isset($this->fieldarr[$fid]))return '';
		
		$isasm 	= 1;
		$a 		= $this->fieldarr[$fid];
		$fname 	= $fid.$leox;
		$type 	= $a['fieldstype'];
		$data 	= $a['data'];
		$val 	= $a['dev'];
		if(isset($a['value']))$val=$a['value'];
		$attr 	= $a['attr'];
		$fnams 	= @$a['name'];
		if($a['isbt']==1)$fnams='*'.$fnams.'';
		if($this->isempt($val))$val='';
		if($this->isempt($attr))$attr='';
		if($val!=''){
			$val = str_replace(array('{now}','{date}','{admin}','{adminid}','{deptname}','{ranking}','{month}'),array($this->now,$this->date,$this->adminname,$this->adminid, $this->urs['deptname'], $this->urs['ranking'],substr($this->date,0,7)),$val);
			if($val=='{sericnum}' && $this->flow!=null)$val = $this->flow->createnum();
		}
		if($type=='num'){
			if($this->flow != null)$val = $this->flow->createbianhao($data, $fid);
			$attr='readonly';
		}
		
		$str 	= '<input class="inputs" value="'.$val.'" '.$attr.' name="'.$fname.'">';
		
		
		if($type=='fixed'||$type=='hidden'){
			$str  = '<input value="'.$val.'" '.$attr.' type="hidden" name="'.$fname.'">';
			$isasm=0;
		}
		if($type=='textarea'){
			$str = '<textarea class="textarea" style="height:100px" '.$attr.' name="'.$fname.'">'.$val.'</textarea>';
		}
		if($type=='rockcombo' || $type=='select' || $type=='checkboxall'){
			$str ='<select style="width:99%" '.$attr.' name="'.$fname.'" class="inputs">';
			$str.='<option value="">-请选择-</option>';
			$str1= '';
			$datanum = $data;
			if(!isempt($datanum)){
				$fopt	= array();
				if($objs!=null && method_exists($objs, $datanum)){
					$fopt	= $objs->$datanum($fid,$this->mid);
					foreach($fopt as $k=>$rs){
						$sel = ($rs['value']==$val)?'selected':'';
						$str.='<option value="'.$rs['value'].'" '.$sel.'>'.$rs['name'].'</option>';
						$str1.='<label><input name="'.$fname.'[]" value="'.$rs['value'].'" type="checkbox">'.$rs['name'].'</label>&nbsp;&nbsp;';
					}
					$fopt = true;
				}
				if(($type=='rockcombo' ||$type=='checkboxall') && !$fopt){
					$_ars= explode(',', $datanum);
					$fopt= m('option')->getselectdata($_ars[0], isset($_ars[2]));
					$fvad= 'name';
					if(isset($_ars[1])&&($_ars[1]=='value'||$_ars[1]=='id'||$_ars[1]=='num'))$fvad=$_ars[1];
					foreach($fopt as $k=>$rs){
						$cb  = $rs[$fvad];
						$sel = ($cb==$val)?'selected':'';
						$str.='<option value="'.$cb.'" '.$sel.'>'.$rs['name'].'</option>';
						$str1.='<label><input name="'.$fname.'[]" value="'.$cb.'" type="checkbox">'.$rs['name'].'</label>&nbsp;&nbsp;';
					}
				}
				if(($type=='select' ||$type=='checkboxall') && !$fopt){
					$fopt= c('array')->strtoarray($datanum);
					foreach($fopt as $k=>$rs){
						$sel = ($rs[0]==$val)?'selected':'';
						$str.='<option value="'.$rs[0].'" '.$sel.'>'.$rs[1].'</option>';
						$str1.='<label><input name="'.$fname.'[]" value="'.$rs[0].'" type="checkbox">'.$rs[1].'</label>&nbsp;&nbsp;';
					}
				}
			}
			$str.='</select>';
			if($type=='checkboxall')$str = $str1;
		}
		
		if($type=='datetime'||$type=='date'||$type=='time'||$type=='month'){
			$str = '<input onclick="js.datechange(this,\''.$type.'\')" value="'.$val.'" '.$attr.' class="inputs datesss" inputtype="'.$type.'" readonly name="'.$fname.'">';
		}
		if($type=='number'||$type=='xuhao'){
			$str = '<input class="inputs" '.$attr.' value="'.$val.'" type="number" onfocus="js.focusval=this.value" maxlength="10" onblur="js.number(this)" name="'.$fname.'">';
			if($type=='xuhao')$str.='<input value="0" type="hidden" name="'.$a['fieldss'].$leox.'">';
		}
		if($type=='changeusercheck'||$type=='changeuser'||$type=='changedept'||$type=='changedeptusercheck'){
			$str = '<table width="98%" cellpadding="0" border="0"><tr><td width="100%"><input '.$attr.' class="inputs" style="width:98%" id="change'.$fname.'" readonly type="text" name="'.$fname.'"><input name="'.$data.'" id="change'.$fname.'_id" type="hidden"></td>';
			$str .= '<td nowrap><a href="javascript:;" style="border-right:1px #0AA888 solid" onclick="js.changeclear(\'change'.$fname.'\')" class="webbtn">×</a><a href="javascript:;" onclick="js.changeuser(\'change'.$fname.'\',\''.$type.'\')" class="webbtn">选择</a></td></tr></table>';
		}
		if($type=='htmlediter'){
			$str = '<textarea class="textarea" style="height:130px" '.$attr.' name="'.$fname.'">'.$val.'</textarea>';
		}
		if($type=='checkbox'){
			$chk = '';
			if($val=='1'||$val=='true')$chk='checked';
			$str = '<input name="'.$fname.'" '.$chk.' '.$attr.' type="checkbox" value="1"> ';
		}
		if($type=='auto'){
			$datanum = $data;
			if(!isempt($datanum)){
				if($objs!=null && method_exists($objs, $datanum)){
					$str = $objs->$datanum($fid, $this->mid);
				}
			}
		}
		if($iszb>0)return $str;
		if($isasm==1){
			$lx  = 'span';if($ism==1)$lx='div';
			$str = '<'.$lx.' id="div_'.$fname.'" class="divinput">'.$str.'</'.$lx.'>';
			if($ism==1 && $iszb==0){
				$str = '<tr><td class="lurim" nowrap>'.$fnams.':</td><td width="90%">'.$str.'</td></tr>';
			}
		}
		return $str;
	}
}                                                                                                                                                            