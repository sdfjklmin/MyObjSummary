<?php
class whereClassModel extends Model
{
	
	public function initModel()
	{
		$this->settable('flow_where');
	}
	
	public function getstrwhere($str, $uid=0)
	{
		if($uid==0)$uid = $this->adminid;
		$str = str_replace(array('{uid}','{now}','{date}'), array($uid, $this->rock->now, $this->rock->date), $str);
		return $str;
	}
	
	public function getflowwhere($id, $uid=0, $fid='')
	{
		$rs 		= $this->getone($id);
		if(!$rs)return false;
		$wheresstr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['wheresstr']), $uid);
		$whereustr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['whereustr']), $uid);
		$wheredstr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['wheredstr']), $uid);
		$str 		= $wheresstr;if(isempt($str))$str='';
		if($fid=='')$fid='`uid`';
		$ustr 		= $nstr = '';
		if(!isempt($rs['receid'])){
			$tsrt 	= m('admin')->gjoin($rs['receid'],'ud', 'where');
			if($tsrt=='all'){
				$tsrt 	= '1=1';
			}else{
				$tsrt 	= '('.$tsrt.')';
			}
			$ustr = $tsrt;
		}
		if(!isempt($whereustr)){
			if($ustr!='')$ustr.=' and ';
			$ustr .= $whereustr;
		}
		
		if(!isempt($rs['nreceid'])){
			$tsrt 	= m('admin')->gjoin($rs['nreceid'],'ud', 'where');
			if($tsrt=='all'){
				$tsrt 	= '1=1';
			}
			$nstr = $tsrt;
		}
		if(!isempt($wheredstr)){
			if($nstr!='')$nstr.=' or ';
			$nstr .= $wheredstr;
		}
		$astr 	= $str;
		if($ustr != '' || $nstr != ''){
			$_sar= '1=1';
			if($ustr!='')$_sar.=' and '.$ustr.'';
			if($nstr!='')$_sar.=' and not ('.$nstr.')';
			if(!isempt($astr))$astr.=' and ';
			$astr .= ' '.$fid.' in(select `id` from `[Q]admin` where '.$_sar.')';
		}
		return array(
			'str'	=> $str,
			'utr'	=> $ustr,
			'ntr'	=> $nstr,
			'atr'	=> $astr
		);
	}
	
	public function getwherestr($id, $uid=0, $fid='', $lx=0)
	{
		$where 	= '';
		$arr 	= $this->getflowwhere($id, $uid, $fid);
		if($arr){
			$where = $arr['atr'];
			if($lx==0)$where = ' and '.$where;
		}
		return $where;
	}
}