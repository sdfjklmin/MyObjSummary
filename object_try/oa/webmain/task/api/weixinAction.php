<?php 

class weixinClassAction extends apiAction{

	public function getsignAction()
	{
		$url = $this->getvals('url');
		$arr = m('weixin:signjssdk')->getsignsdk($url);
		$this->showreturn($arr);
	}
	
	public function addlocationAction()
	{
		$arr['location_x']	= $this->post('location_x');
		$arr['location_y']	= $this->post('location_y');
		$arr['scale']		= (int)$this->post('scale');
		$arr['precision']	= (int)$this->post('precision');
		$arr['label']		= $this->getvals('label');
		$arr['optdt']		= $this->now;
		$arr['uid']			= $this->adminid;
		m('location')->insert($arr);
		$this->showreturn('');
	}
}