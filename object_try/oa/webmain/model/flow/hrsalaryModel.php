<?php
class flow_hrsalaryClassModel extends flowModel
{
	protected $flowviewufieds	= 'xuid';
	
	//薪资是个严格的模块，只能设置权限后才可以查看，不管是不是管理员
	protected function flowbillwhere($uid, $lx)
	{
		$key  	= $this->rock->post('key');
		$dt  	= $this->rock->post('dt');
		$ispay  = $this->rock->post('ispay');
		$isturn  = $this->rock->post('isturn');
		$where 	= '';
		$where 	= $this->viewmodel->viewwhere($this->moders, $this->adminid, $this->flowviewufieds);
		if($lx=='faf'){
			$where.=' and `status`=1';
		}
		if($ispay!='')$where.=' and `ispay`='.$ispay.'';
		if($isturn!='')$where.=' and `isturn`='.$isturn.'';
	//echo $where;
		
		if($key!='')$where.=" and (udeptname like '%$key%' or uname like '%$key%' or ranking like '%$key%' )";
		if($dt!='')$where.=" and `month`='$dt'";
		return array(
			'where' => $where,
			'fields'=> 'id,status,udeptname,uname,ranking,optdt,month,optname,base,money,isturn,ispay',
			'order' => '`optdt` desc'
		);
	}
	
	public function flowrsreplace($rs){
		$s = '<font color=red>未发放</font>';
		if($rs['ispay']==1)$s = '<font color=green>已发放</font>';
		$rs['ispay'] = $s;
		
		$s = '<font color=red>未核算</font>';
		if($rs['isturn']==1)$s = '<font color=green>已核算</font>';
		$rs['isturn'] = $s;
		return $rs;
	}
	
	private function fafang()
	{
		$this->addlog(array(
			'name' => '发放'
		));
		$this->update('ispay=1', $this->id);
		m('reim')->asynurl('asynrun','salaryff', array(
			'id' => $this->id
		));//异步通知给人员
	}
	
	public function todouser()
	{
		$this->push($this->rs['xuid'],'','您['.$this->rs['month'].']月份薪资已发放，请注意查看对账。','薪资发放通知');
	}
	
	public function gongzifafang($sid)
	{
		$sarrid = explode(',',$sid);
		foreach($sarrid as $id){
			$this->loaddata($id, false);
			if($this->rs['status']==1 && $this->rs['ispay']==0)$this->fafang();
		}
	}
	
	//一键生成薪资
	public function createdata($month)
	{
		$lmonth = c('date')->adddate($month.'-01','m',-1, 'Y-m');
		$swer 	= m('admin')->monthuwhere($month);
		$where 	= "`month`='$lmonth' and `optid`='$this->adminid' and xuid in(select id from `[Q]admin` where 1=1 $swer) and `xuid` not in(select xuid from `[Q]".$this->mtable."` where `month`='$month')";
		$addfes = 'base,postjt,skilljt,travelbt,telbt';
		$adees	= explode(',', $addfes);
		$rows 	= $this->getall($where, 'xuid,uname,udeptname,ranking,'.$addfes.',socials');
		$count  = $this->db->count;
		foreach($rows as $k=>$rs){
			$money 	= 0;
			$xuid	= $rs['xuid'];
			$arr 	= array(
				'xuid' 		=> $xuid,
				'uname' 	=> $rs['uname'],
				'ranking' 	=> $rs['ranking'],
				'udeptname' => $rs['udeptname'],
				'uid' 		=> $this->adminid,
				'optid' 	=> $this->adminid,
				'optname' 	=> $this->adminname,
				'optdt' 	=> $this->rock->now,
				'applydt' 	=> $this->rock->date,
				'status' 	=> 0,
				'isturn' 	=> 0,
				'month' 	=> $month,
			);
			foreach($adees as $fid){
				$arr[$fid] = $rs[$fid];
				$money += floatval($rs[$fid]);
			}
			$money -= floatval($rs['socials']);
			$arr['money']	= $money;
			$this->insert($arr);
		}
		return '成功成功'.$count.'条';
	}
}