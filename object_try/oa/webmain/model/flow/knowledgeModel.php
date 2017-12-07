<?php
class flow_knowledgeClassModel extends flowModel
{
	protected function flowchangedata(){
		$this->rs['content'] = c('html')->replace($this->rs['content']);
	}
	
	
	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$typeid = $this->rock->post('typeid','0');
		$key 	= $this->rock->post('key');
		if($typeid!='0'){
			$where .= ' and `typeid`='.$typeid.'';
		}
		if($key != '')$where.=" and `title` like '%$key%'";
		return array(
			'where' => $where,
			'order' => 'optdt desc',
			'fields'=> 'id,title,adddt,optdt,optname'
		);
	}
}