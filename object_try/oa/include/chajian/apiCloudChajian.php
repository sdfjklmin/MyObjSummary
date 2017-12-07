<?php 
/**
	apiCloud推送
*/
class apiCloudChajian extends Chajian{
	
	private $AppID 		= 'A6903844516729';
    private $AppKey 	= '6B872B6E-ADDE-D7C0-396F-1F618E8D836A';
    private $AppPath 	= 'https://p.apicloud.com/api/push/message';
    private	$timeOut 	= 30;
        
	public function initChajian()
	{
		if($this->contain(HOST,'.com')){
			$this->AppID 	= 'A6917065771076';
			$this->AppKey 	= 'B7A5FD72-3F11-89CB-8FB3-BC06B112041D';
		}
		$this->headerInfo = array(
			'X-APICloud-AppId:'.$this->AppID,
			'X-APICloud-AppKey:'.$this->getSHAKey()
		);
	}
	
	//毫秒
	private function getMilliSecond()
	{
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}
	
	private function getSHAKey()
	{
		$time = $this->getMilliSecond();
		return sha1($this->AppID.'UZ'.$this->AppKey.'UZ'.$time).'.'.$time;
	}

	private function pushtesmp($data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_URL, $this->AppPath);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headerInfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;        
	}
	
	/**
		type – 消息类型，1:消息 2:通知
	*/
	public function send($touid, $title='', $conarr='', $type=2)
	{
		if($this->isempt($touid))return false;
		$touid	= $this->gettouid($touid);
		if($this->isempt($touid))return false;
		$s = '';
		if(is_array($conarr)){
			foreach($conarr as $k=>$v){
				$s .= ','.$k.':"'.$v.'"';
			}
			$s = substr($s, 1);
			$s = '{'.$s.'}';
		}else{
			$s = $conarr;
		}
		if($s=='')return 'not cont';
		if(strlen($s)>168)return 'over 168 zi';
		$da = array(
			'userIds' 	=> $touid,
			'title'		=> $title,
			'content'	=> $s,
			'type'		=> $type,
			'platform'	=> 2, //0:全部平台，1：ios, 2：android
		);
		$recet = $this->pushtesmp($da);
		$this->rock->debugs(''.$touid.':'.$recet.'','apiCloudpush');
		return json_decode($recet, true);
	}
	
	public function gettouid($touid)
	{
		$where = '';
		if($touid!='all')$where		= " and `uid` in($touid)";
		$rows 		= m('logintoken')->getrows("`cfrom`='appandroid' $where and `online`=1",'token');
		$uarr		= array();
		foreach($rows as $k=>$rs){
			$uarr[] = $rs['token'];
		}
		$sids	= join(',', $uarr);
		return $sids;
	}
}                                                                                                                                                          