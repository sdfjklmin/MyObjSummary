<?php
class logClassModel extends Model
{
	public function addlog($type='', $remark='', $sarr=array())
	{
		$arr['type']	= $type;
		$arr['ip']		= $this->rock->ip;
		$arr['web']		= $this->rock->web;
		$arr['optdt']	= $this->rock->now();
		$arr['remark']	= $remark;
		foreach($sarr as $k=>$v)$arr[$k]=$v;
		$this->insert($arr);
	}
	
	public function addread($table, $mid, $uid=0)
	{
		if($uid==0)$uid=$this->adminid;
		$arr['table']	= $table;
		$arr['mid']		= $mid;
		$arr['ip']		= $this->rock->ip;
		$arr['web']		= $this->rock->web;
		$arr['optdt']	= $this->rock->now();
		$arr['optid']	= $uid;
		m('reads')->insert($arr);
	}
	
	public function getreadarr($table, $mid)
	{
		$rows = $this->db->getrows('[Q]reads',"`table`='$table' and `mid`='$mid' group by `optid`",'optid,max(optdt)optdt,count(1)stotal','`id` desc');
		$uids = '0';
		foreach($rows as $k=>$rs){
			$uids.=','.$rs['optid'].'';
		}
		$usarr = array();
		if($uids!='0'){
			$uarr = $this->db->getarr('[Q]admin',"`id` in($uids) and `status`=1", '`name`,`face`');
			foreach($rows as $k=>$rs){
				$uid = $rs['optid'];
				if(isset($uarr[$uid])){
					$usarr[] = array(
						'uid' 		=> $uid,
						'optdt' 	=> $rs['optdt'],
						'stotal' 	=> $rs['stotal'],
						'name'	=> $uarr[$uid]['name'],
						'face'	=> $this->rock->repempt($uarr[$uid]['face'],'images/noface.png')
					);
				}
			}
		}
		return $usarr;
	}
	
	public function getread($table, $uid=0)
	{
		if($uid==0)$uid=$this->adminid;
		$sid = $this->db->getjoinval('[Q]reads','mid',"`table`='$table' and `optid`=$uid group by `mid`");
		if($sid=='')$sid = '0';
		return $sid;
	}
	
	public function isread($table, $mid, $uid=0)
	{
		if($uid==0)$uid=$this->adminid;
		$where  = "`table`='$table' and `mid`='$mid' and `optid`=$uid";
		$to 	= $this->db->rows('[Q]reads', $where);
		return $to;
	}
}