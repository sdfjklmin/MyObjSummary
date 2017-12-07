<?php
class modeClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_set');
	}
	public function getmodearr()
	{
		$arr = $this->getall('status=1','`id`,`num`,`name`,`table`,`type`,`isflow`','sort');
		foreach($arr as $k=>$rs){
			$arr[$k]['name'] = ''.$rs['id'].'.'.$rs['name'].'('.$rs['num'].')';
		}
		return $arr;
	}
	
	public function getmoderows($uid, $sww='')
	{
		$where	= m('admin')->getjoinstr('receid', $uid);
		$arr 	= $this->getall("`status`=1 and `type`<>'系统' $sww $where",'`id`,`num`,`name`,`table`,`type`,`isflow`','`sort`');
		return $arr;
	}
	
	public function getmodemyarr($uid=0)
	{
		$where = '';
		if($uid>0)$where = m('admin')->getjoinstr('receid', $uid);
		$arr = $this->getall('status=1 and isflow=1 '.$where.'','`id`,`name`','sort');
		return $arr;
	}
}