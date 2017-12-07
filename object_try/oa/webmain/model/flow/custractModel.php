<?php

class flow_custractClassModel extends flowModel
{
	public function initModel(){
		$this->typearr		= array('收款合同','付款合同');
		$this->typesarr		= array('收','付');
		$this->dtobj		= c('date');
		$this->crmobj		= m('crm');
	}

	
	public function flowrsreplace($rs){
		$type 		= $rs['type'];
		$rs['type'] = $this->typearr[$type];
		$statetext	= '';
		$dt 		= $this->rock->date;
		if($rs['startdt']>$dt){
			$statetext='待生效';
		}else if($rs['startdt']<=$dt && $rs['enddt']>=$dt){
			$jg = $this->dtobj->datediff('d', $dt, $rs['enddt']);
			$statetext='<font color=green>生效中</font><br><font color=#888888>'.$jg.'天过期</font>';
			if($jg==0)$statetext='<font color=green>今日到期</font>';
		}else if($rs['enddt']<$dt){
			$statetext='<font color=#888888>已过期</font>';
		}
		$rs['statetext']	= $statetext;
		$nrss	 			= $this->crmobj->ractmoney($rs);
		$ispay 				= $nrss[0];
		$moneys 			= $nrss[1];
		$dsmoney			= '';
		$ts 				= $this->typesarr[$type];
		if($ispay==1){
			$dsmoney		= '<font color=green>已全部'.$ts.'款</font>';
		}else{
			$dsmoney		= '待'.$ts.'<font color=#ff6600>'.$moneys.'</font>';
		}
		$rs['moneys']		= $dsmoney;
		return $rs;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$where 	= '`uid`='.$uid.'';
		$lxa 	= explode('_', $lx);
		if($lxa[0]=='down'){
			$where = m('admin')->getdownwheres('uid', $uid, 1);
		}
		$lx 	= $lxa[1];
		$key	= $this->rock->post('key');
		if($lx=='ygq'){
			$where.=" and `enddt`<'{$this->rock->date}'";
		}
		//全部收付款
		if($lx=='qbsfk'){
			$where.= ' and `ispay`=1';
		}
		//部分收付款
		if($lx=='bfsfk'){
			$where.= ' and `ispay`=2';
		}
		//待收付款
		if($lx=='daisfk'){
			$where.= ' and `ispay`=0';
		}
		if($key!=''){
			$where.=" and (`num`='$key' or `custname` like '%$key%' or `optname`='$key')";
		}
		return array(
			'where' => 'and '.$where,
			'order' => '`optdt` desc'
		);
	}
	
	protected function flowoptmenu($ors, $arrs)
	{
		//创建待收付款单
		if($ors['num']=='cjdaishou'){
			$moneys 		= m('crm')->getmoneys($this->rs['id']);
			$money			= $this->rs['money'] - $moneys;
			if($money > 0){
				$arr['htid'] 	= $this->rs['id'];
				$arr['htnum'] 	= $this->rs['num'];
				$arr['uid'] 	= $this->rs['uid'];
				$arr['custid'] 	= $this->rs['custid'];
				$arr['custname']= $this->rs['custname'];
				$arr['dt']		= $this->rock->date;
				$arr['optdt'] 	= $this->rock->now;
				$arr['createdt']= $this->rock->now;
				$arr['optname'] = $this->adminname;
				$arr['createname']= $this->adminname;
				$arr['createid']  = $this->adminid;
				$arr['optid'] 	= $this->adminid;
				$arr['type'] 	= $this->rs['type'];
				$arr['explain'] = $arrs['sm'];
				$arr['money'] 	= $money;
				m('custfina')->insert($arr);
			}
		}
	}
}