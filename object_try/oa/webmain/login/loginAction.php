<?php 
class loginClassAction extends ActionNot{
	
	public function defaultAction()
	{
		$this->tpltype	= 'html';
		$this->smartydata['ca_adminuser']	= $this->getcookie('ca_adminuser');
		$this->smartydata['ca_rempass']		= $this->getcookie('ca_rempass');
		$this->smartydata['ca_adminpass']	= $this->getcookie('ca_adminpass');
	}
	
	public function checkAjax()
	{
		$user 	= $this->jm->base64decode($this->post('adminuser'));
		$user	= str_replace(' ','',$user);
		$pass	= $this->jm->base64decode($this->post('adminpass'));
		$rempass= $this->post('rempass');
		$jmpass	= $this->post('jmpass');
		if($jmpass == 'true')$pass=$this->jm->uncrypt($pass);
		$arr 	= m('login')->start($user, $pass, 'pc');
		$barr 	= array();
		if(is_array($arr)){
			$uid 	= $arr['uid'];
			$name 	= $arr['name'];
			$user 	= $arr['user'];
			$token 	= $arr['token'];
			$face 	= $arr['face'];
			m('login')->setsession($uid, $name, $token, $user);
			$this->rock->savecookie('ca_adminuser', $user);
			$this->rock->savecookie('ca_rempass', $rempass);
			$ca_adminpass	= $this->jm->encrypt($pass);
			if($rempass=='0')$ca_adminpass='';
			$this->rock->savecookie('ca_adminpass', $ca_adminpass);
			$barr['success'] = true;
			$barr['face']  	 = $face;
		}else{
			$barr['success'] = false;
			$barr['msg'] 	 = $arr;
		}
		$this->returnjson($barr);
	}
	
	public function exitAction()
	{
		m('login')->exitlogin('pc',$this->admintoken);
		$this->rock->location('?m=login');
	}
}