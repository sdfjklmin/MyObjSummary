<?php

class flow_goodsClassModel extends flowModel
{
	
	protected function flowchangedata(){
		$this->rs['typeid']	 = $this->db->getpval('[Q]option','pid','name', $this->rs['typeid'],'/','id',2);;
	}

	
}