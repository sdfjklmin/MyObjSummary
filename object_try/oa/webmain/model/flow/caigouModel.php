<?php

class flow_caigouClassModel extends flowModel
{
	
	protected function flowcheckfinsh($zt){
		m('goodss')->update('status='.$zt.'',"`mid`='$this->id'");
		$aid  = '0';
		$rows = m('goodss')->getall("`mid`='$this->id'",'aid');
		foreach($rows as $k=>$rs)$aid.=','.$rs['aid'].'';
		m('goods')->setstock($aid);
	}

	protected function flowchangedata(){
		
		$rows    = $this->db->getall('select b.name as aid,a.count,b.unit,a.price from `[Q]goodss` a left join `[Q]goods` b on a.aid=b.id where a.mid='.$this->id.' order by a.sort');
		foreach($rows as $k=>$rs){
			$rows[$k]['count'] 	= abs($rs['count']);
		}
		$this->rs['subdata0']	= $rows;
	}
}