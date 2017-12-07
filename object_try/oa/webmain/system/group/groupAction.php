<?php
class groupClassAction extends Action
{
	public function groupusershow($table)
	{
		$s 		= 'and 1=2';
		$gid 	= $this->post('gid','0');
		if($gid>0){
			$s = " and ( id in( select `sid` from `[Q]sjoin` where `type`='gu' and `mid`='$gid') or id in( select `mid` from `[Q]sjoin` where `type`='ug' and `sid`='$gid') )";
		}
		return array(
			'where' => $s,
			'fields'=> 'id,user,name,deptname'
		);
	}
	
	public function groupafter($table, $rows)
	{
		
		foreach($rows as $k=>$rs){
			$gid = $rs['id'];
			$s 	 = "( id in( select `sid` from `[Q]sjoin` where `type`='gu' and `mid`='$gid') or id in( select `mid` from `[Q]sjoin` where `type`='ug' and `sid`='$gid') )";
			$rows[$k]['utotal'] = $this->db->rows('[Q]admin', $s);
		}
		return array('rows'=>$rows);
	}
	
	public function saveuserAjax()
	{
		$gid 	= $this->post('gid','0');
		$sid 	= $this->post('sid','0');
		$dbs 	= m('sjoin');
		$dbs->delete("`mid`='$gid' and `type`='gu' and `sid` in($sid)");
		$this->db->insert('[Q]sjoin','`type`,`mid`,`sid`', "select 'gu','$gid',`id` from `[Q]admin` where `id` in($sid)", true);
		echo 'success';
	}
	
	public function deluserAjax()
	{
		$gid 	= $this->post('gid','0');
		$sid 	= $this->post('sid','0');
		$dbs 	= m('sjoin');
		$dbs->delete("`mid`='$gid' and `type`='gu' and `sid`='$sid'");
		$dbs->delete("`sid`='$gid' and `type`='ug' and `mid`='$sid'");
		echo 'success';
	}
}