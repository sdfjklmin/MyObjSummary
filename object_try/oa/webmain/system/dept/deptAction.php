<?php
class deptClassAction extends Action
{
	
	
	public function dataAjax()
	{
		$this->rows	= array();
		$this->getdept(0, 1);
		
		echo json_encode(array(
			'totalCount'=> 0,
			'rows'		=> $this->rows
		));
	}
	
	private function getdept($pid, $oi)
	{
		$db		= m('dept');
		$menu	= $db->getall("`pid`='$pid' order by `sort`",'*');
		foreach($menu as $k=>$rs){
			$sid			= $rs['id'];
			$rs['level']	= $oi;
			$rs['stotal']	= $db->rows("`pid`='$sid'");
			$this->rows[] = $rs;
			
			$this->getdept($sid, $oi+1);
		}
	}
	
	public function publicbeforesave($table, $cans, $id)
	{
		$pid = (int)$cans['pid'];
		if($pid>0 && m($table)->rows($pid)==0)return '上级ID不存在';
		if($pid==0 && $id != 1)return '上级ID不能为0';
		return '';
	}
	
	public function publicaftersave($table, $cans, $id)
	{
		$name 	= $cans['name'];
		$db 	= m('admin');
		$db->update("deptname='$name'", "`deptid`=$id");
		$db->updateinfo();
	}
	
	public function deptuserdataAjax()
	{
		$type 	= $this->request('changetype');
		$val 	= $this->request('value');
		$pid	= 0;
		$rows	= $this->getdeptmain($pid, $type, ','.$val.',');
		echo json_encode($rows);
	}
	
	private function getdeptmain($pid, $type, $val)
	{
		$sql	= $this->stringformat('select `id`,`name` from `?0` where `pid`=?1 order by `sort`', array($this->T('dept'), $pid));
		$arr	= $this->db->getall($sql);
		$rows	= array();
		foreach($arr as $k=>$rs){
			$children		= $this->getdeptmain($rs['id'], $type, $val);
			$uchek			= $this->contain($type, 'check');
			$expanded		= false;
			if($this->contain($type, 'user')){
				$sql	= $this->stringformat('select `id`,`name`,`sex`,`ranking`,`deptname` from `?0` where `deptid`=?1 and `status`=1 order by `sort`', array($this->T('admin'), $rs['id']));			
				$usarr	= $this->db->getall($sql);
				foreach($usarr as $k1=>$urs){
					$usarr[$k1]['leaf'] = true;
					$usarr[$k1]['uid']  = $urs['id'];
					$usarr[$k1]['id']   = 'u'.$urs['id'];
					$usarr[$k1]['type'] = 'u';
					$usarr[$k1]['icons'] = 'user';
					if($uchek){
						$bo = false;
						if($this->contain($type, 'dept')){
							$bo = $this->contain($val, $usarr[$k1]['id']);
						}else{
							$bo = $this->contain($val, $usarr[$k1]['uid']);
						}
						$usarr[$k1]['checked']=$bo;
						if(!$expanded)$expanded = $bo;
					}	
				}
				$children= array_merge($children, $usarr);
			}
			if($pid==0)$expanded = true;
			$ars['children']= $children;
			$ars['name'] 	= $rs['name'];
			$ars['id'] 		= 'd'.$rs['id'];
			$ars['did'] 	= $rs['id'];
			$ars['type'] 	= 'd';
			$ars['expanded'] = $expanded;
			
			if($this->contain($type, 'dept')){
				if($uchek){
					$bo = false;
					if($this->contain($type, 'user')){
						$bo = $this->contain($val, $ars['id']);
					}else{
						$bo = $this->contain($val, $ars['did']);
					}
					$ars['checked']=$bo;
				}	
			}
			$rows[]	= $ars;
		}
		return $rows;
	}
}