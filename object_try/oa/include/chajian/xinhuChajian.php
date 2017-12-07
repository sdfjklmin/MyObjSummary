<?php 
class xinhuChajian extends Chajian{
	
	private $updatekey 	= 'aHR0cDovL3hoODI5LmNvbS8:';
	private $updatekeys = 'aHR0cDovLzEyNy4wLjAuMS9hcHAvcm9ja3hpbmh1d2ViLw::';
	
	protected function initChajian()
	{
		if(getconfig('systype')=='dev')$this->updatekey = $this->updatekeys;
		$this->updatekey = $this->rock->jm->base64decode($this->updatekey);
	}
	
	public function getwebsite()
	{
		return $this->updatekey;
	}
	
	public function geturlstr($act, $can=array())
	{
		$url = $this->updatekey;
		$url.= 'api.php?a='.$act.'';
		$url.= '&host='.$this->rock->jm->base64encode(HOST).'&version='.VERSION.'&time='.time().'';
		foreach($can as $k=>$v)$url.='&'.$k.'='.$v.'';
		return $url;
	}
	
	public function getdata($act, $can=array())
	{
		$url 	= $this->geturlstr($act, $can);
		$cont 	= c('curl')->getfilecont($url);
		$data  	= array('code'=>199,'msg'=>'访问出错');
		if($cont!=''){
			$data  	= json_decode($cont, true);
		}
		return $data;
	}
}