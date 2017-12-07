<?php

class mode_goodsClassAction extends inputAction{
	
	public function getgoodstype()
	{
		$rows = m('goods')->getgoodstype();
		return $rows;
	}
}	
			