<?php 
/**
	信鸽推送
*/
include_once(ROOT_PATH.'/include/XgPush/XingeApp.php');
class xgpushChajian extends Chajian{
	
	public $secretKey 	= 'a05b86788e95a32a917cecda27dd39cd';
	public $accessId 	= '2100197705';
	
	public function sendandroid($uid, $title='', $cont='', $type=0)
	{
		$account	= '';
		if($uid!='all'){
			$rows 		= m('logintoken')->getrows("`uid` in($uid) and `cfrom`='appandroid' and `online`=1",'token');
			$account 	= array();
			$oi			= 0;
			foreach($rows as $k=>$rs){
				$account[] = $rs['token'];
				$oi++;
			}
			if($oi==0)$account='';
			if($oi==1)$account=$rows[0]['token'];
		}else{
			$account = $uid;
			if(m('logintoken')->rows("`cfrom`='appandroid' and `online`=1")==0)$account='';
		}
		$this->rock->debugs($account.':'.$cont.'');
		$arr = array('err_msg'=>'account is null');
		if($account!='')$arr = XingeApp::PushAccountAndroid($this->accessId, $this->secretKey, $title, $cont, $account, $type);
		return $arr;
	}
	
	public function sendpush($uid, $title='', $cont='')
	{
		$this->sendandroid($uid, $title, $cont);
	}
}