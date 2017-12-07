<?php
class crmClassModel extends Model
{
	public function initModel()
	{
		$this->settable('customer');
	}
	
	//读取我的客户和分享给我的
	public function getmycust($uid)
	{
		//$s		= $this->rock->dbinstr('shateid', $uid);
		$rows 	= $this->getrows("`status`=1 and uid='$uid'",'id as value,name,id','`name`');
		return $rows;
	}
	
	
	
	//读取我的销售机会
	public function getmysale($uid, $id=0)
	{
		$where 	= '`uid`='.$uid.' and `state`=1 and (`htid`=0 or `id`='.$id.')';
		$rows 	= m('custsale')->getrows($where, 'id,custid,custname,money,laiyuan');
		return $rows;
	}
	
	//读取我的合同
	public function getmyract($uid, $id=0)
	{
		$where 	= '`uid`='.$uid.' and (`isover`=0 or `id`='.$id.')';
		$rows 	= m('custract')->getrows($where, 'id,custid,custname,money,num');
		return $rows;
	}
	
	public function ractmoney($htid)
	{
		if(isempt($htid))return false;
		if(!is_array($htid)){
			$ors  	= $this->db->getone('[Q]custract','id='.$htid.'','money,moneys,ispay,id,isover');
		}else{
			$ors	= $htid;
		}
		if(!$ors)return false;
		$zmoney	= $ors['money']; $moneys	= $ors['moneys'];
		$oispay	= $ors['ispay'];
		$htid	= $ors['id'];
		$money 	= $this->db->getmou('[Q]custfina','sum(money)','htid='.$htid.' and `ispay`=1');
		$moneyy	= $this->getmoneys($htid);
		$symon	= $zmoney - $money;
		$ispay	= 0;
		$isover	= 0;
		if($symon<=0){
			$ispay = 1;
		}else if($money>0){
			$ispay = 2;
		}
		if($moneyy>=$zmoney)$isover = 1;
		if($ispay != $oispay || $symon!= $moneys || $isover != $ors['isover']){
			$this->db->update('[Q]custract','`ispay`='.$ispay.',`moneys`='.$symon.',`isover`='.$isover.'', $htid);
		}
		return array($ispay, $symon);
	}
	
	public function getmoneys($htid, $id=0)
	{
		$moneys = floatval($this->db->getmou('[Q]custfina','sum(money)','`htid`='.$htid.' and `id`<>'.$id.''));
		return $moneys;
	}
	
	public function moneytotal($uid, $month)
	{
		$sql 	= "SELECT uid,type,ispay,sum(money)money,count(1)stotal FROM `[Q]custfina` where `createid`='$uid' and `dt` like '$month%' GROUP BY type,ispay";
		$farr	= explode(',', 'shou_moneyd,shou_moneyz,shou_moneys,shou_moneyn,shou_shu,fu_moneyd,fu_moneyz,fu_moneys,fu_moneyn,fu_shu');
		foreach($farr as $f)$$f= 0;
		$rows 	= $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			$type 	= $rs['type']; $ispay = $rs['ispay']; 
			$money 	= floatval($rs['money']);
			$stotal	= floatval($rs['stotal']);
			if($type==0){
				if($ispay==1){
					$shou_moneys += $money;	
				}else{
					$shou_moneyd += $money;	
				}
				$shou_shu 	 += $stotal;	
				$shou_moneyz += $money;	
			}else{
				if($ispay==1){
					$fu_moneys += $money;	
				}else{
					$fu_moneyd += $money;	
				}
				$fu_shu 	 += $stotal;	
				$fu_moneyz 	 += $money;	
			}
		}
		//当月已收付
		$sql = "SELECT type,sum(money)money FROM `[Q]custfina` where `createid`='$uid' and ispay=1 and paydt like '$month%' GROUP BY type";
		$rows 	= $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			if($rs['type']==0)$shou_moneyn = $rs['money']+0;
			if($rs['type']==1)$fu_moneyn = $rs['money']+0;
		}
		$arr = array();
		foreach($farr as $f)$arr[$f] = $$f;
		return $arr;
	}
	
	//客户转移
	public function movetouser($uid, $sid, $toid)
	{
		$rows 	= $this->getrows("`id` in($sid) and `uid`='$this->adminid'",'id,name');
		$toname = m('admin')->getmou('name',"`id`='$toid'");
		if(isempt($toname))return false;
		
		foreach($rows as $k=>$rs){
			$id  = $rs['id'];
			$uarr			= array();
			$uarr['uid'] 	= $toid;
			$uarr['optname']= $toname;
			
			$this->update($uarr, $id);
			
			m('custract')->update($uarr, "`uid`='$uid' and `custid`='$id'");
			m('custsale')->update($uarr, "`uid`='$uid' and `custid`='$id'");
			$uarr['ismove']=1;
			m('custfina')->update($uarr, "`uid`='$uid' and `custid`='$id'");
		}
	}
	
	//客户统计
	public function custtotal($ids='')
	{
		$wher	= '';
		$ustr 	= '`moneyz`=0,`moneyd`=0,`htshu`=0';
		if($ids!=''){
			$wher=' and `custid` in('.$ids.')';
			$this->update($ustr,'id in('.$ids.')');
		}else{
			$this->update($ustr,'id>0');
		}
		$rows 	= $this->db->getall('SELECT custid,sum(money)as moneys,ispay FROM `[Q]custfina` where `type`=0 '.$wher.' GROUP BY custid,ispay');
		$arr 	= array();
		foreach($rows as $k=>$rs){
			$custid = $rs['custid'];
			if(!isset($arr[$custid]))$arr[$custid] = array(0,0,0);
			$arr[$custid][0]+=$rs['moneys'];
			if($rs['ispay']==0)$arr[$custid][1]+=$rs['moneys'];
		}
		foreach($arr as $id=>$rs){
			$uarr['moneyz'] = $rs[0];
			$uarr['moneyd'] = $rs[1];
			$this->update($uarr, $id);
		}
		$rows 	= $this->db->getall('SELECT custid,count(1)htshu FROM `[Q]custract` where id>0 '.$wher.' GROUP BY custid');
		foreach($rows as $k=>$rs){
			$custid = $rs['custid'];
			$this->update('htshu='.$rs['htshu'].'', $custid);
		}
	}
}