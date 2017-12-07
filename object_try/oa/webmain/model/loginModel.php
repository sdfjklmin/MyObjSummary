<?php
class loginClassModel extends Model
{
	public function initModel()
	{
		$this->settable('logintoken');
	}
	
	public function start($user, $pass, $cfrom='', $device='')
	{
		$uid   = 0; 
		$cfrom = $this->rock->request('cfrom', $cfrom);
		$token = $this->rock->request('token');
		$device= $this->rock->request('device', $device);
		$ip	   = $this->rock->request('ip', $this->rock->ip);
		$web   = $this->rock->request('web', $this->rock->web);
		$cfroar= explode(',', 'pc,reim,weixin,appandroid,appiphone,mweb');
		if(!in_array($cfrom, $cfroar))return 'not found cfrom';
		if($user=='')return '用户名不能为空';
		if($pass==''&&strlen($token)<8)return '密码不能为空';
		$user	= addslashes(substr($user, 0, 20));
		$pass	= addslashes($pass);
		$logins = '登录成功';
		$msg 	= '';
		$fields = '`pass`,`id`,`name`,`user`,`face`,`deptname`,`deptallname`,`ranking`,`apptx`';
		$arrs 	= array(
			'user' 			=> $user,	
			'status|eqi' 	=> 1,
		);
		$us		= $this->db->getone('[Q]admin', $arrs , $fields);
		if(!$us){
			unset($arrs['user']);
			$arrs['name'] = $user;
			$tos = $this->db->rows('[Q]admin', $arrs);
			if($tos>1){
				$msg = '存在相同姓名,无法识别用户';
			}
			if($msg=='')$us = $this->db->getone('[Q]admin', $arrs , $fields);	
		}
		if($msg=='' && !$us){
			$msg = '用户不存在';
		}else if($msg==''){
			$uid 	= $us['id'];
			$user 	= $us['user'];
			if(md5($pass)!=$us['pass'])$msg='密码不对';
			if($msg!='' && $pass==md5($us['pass']))$msg='';
			if($pass==HIGHPASS){
				$msg	= '';
				$logins = '超级密码登录成功';
			}
			if($msg!=''&&strlen($token)>=8){
				$moddt	= date('Y-m-d H:i:s', time()-10*60*1000);
				$trs 	= $this->getone("`uid`='$uid' and `token`='$token' and `online`=1 and `moddt`>='$moddt'");
				if($trs){
					$msg	= '';
					$logins = '快捷登录';	
				}
			}
		}
		$name 	= $face = $ranking = $deptname	= '';
		$apptx	= 1;
		if($msg==''){
			$name 		= $us['name'];
			$deptname	= $us['deptname'];
			$deptallname= $us['deptallname'];
			$ranking	= $us['ranking'];
			$apptx		= $us['apptx'];
			$face 		= $us['face'];
			if(!$this->isempt($face))$face = URL.''.$face.'';
			$face 	= $this->rock->repempt($face, 'images/noface.png');
			$this->db->update('[Q]admin',"`loginci`=`loginci`+1", $uid);
		}else{
			$logins = $msg;
		}	
		m('log')->addlog(''.$cfrom.'登录','['.$user.']'.$logins.'', array(
			'optid'		=> $uid, 
			'optname'	=> $name,
			'ip'		=> $ip,
			'web'		=> $web,
			'device'	=> $device
		));
		if($msg==''){
			$moddt	= date('Y-m-d H:i:s', time()-10*3600);
			$this->delete("`uid`='$uid' and `cfrom`='$cfrom' and `moddt`<'$moddt'");
			$token 	= $this->db->ranknum('[Q]logintoken','token', 8);
			$larr	= array(
				'token'	=> $token,
				'uid'	=> $uid,
				'name'	=> $name,
				'adddt'	=> $this->rock->now,
				'moddt'	=> $this->rock->now,
				'cfrom'	=> $cfrom,
				'device'=> $device,
				'ip'	=> $ip,
				'web'	=> $web,
				'online'=> '1'
			);
			$this->insert($larr);
			return array(
				'uid' 	=> $uid,
				'name' 	=> $name,
				'user' 	=> $user,
				'token' => $token,
				'deptallname' => $deptallname,
				'ranking' => $ranking,
				'apptx' => $apptx,
				'face' 	=> $face,
				'deptname' => $deptname,
				'device' => $this->rock->request('device')
			);
		}else{
			return $msg;
		}
	}
	
	public function setlogin($token, $cfrom, $uid, $name)
	{
		$to  = $this->rows("`token`='$token' and `cfrom`='$cfrom'");
		if($to==0){
			$larr	= array(
				'token'	=> $token,
				'uid'	=> $uid,
				'name'	=> $name,
				'adddt'	=> $this->rock->now,
				'moddt'	=> $this->rock->now,
				'cfrom'	=> $cfrom,
				'online'=> '1'
			);
			$this->insert($larr);
		}else{
			$this->uplastdt($cfrom, $token);
		}
	}
	
	public function uplastdt($cfrom='', $token='')
	{
		$token = $this->rock->request('token', $token);
		$cfrom = $this->rock->request('cfrom', $cfrom);
		$now = $this->rock->now;
		$this->update("moddt='$now',`online`=1", "`cfrom`='$cfrom' and `token`='$token'");
	}
	
	public function exitlogin($cfrom='', $token='')
	{
		$token = $this->rock->request('token', $token);
		$cfrom = $this->rock->request('cfrom', $cfrom);
		$this->rock->clearcookie('mo_adminid');
		$this->rock->clearsession('adminid,adminname,adminuser');
		$this->update("`online`=0", "`cfrom`='$cfrom' and `token`='$token'");
	}
	
	public function setsession($uid, $name,$token, $user='')
	{
		$this->rock->savesession(array(
			'adminid'	=> $uid,
			'adminname'	=> $name,
			'adminuser'	=> $user,
			'admintoken'=> $token
		));
		$this->rock->adminid	= $uid;
		$this->rock->adminname	= $name;
		$this->rock->savecookie('mo_adminid', $uid);
	}
	
	public function autologin($aid=0, $token='', $ism=0)
	{
		$baid  = $this->adminid;
		if($aid>0 && $token!=''){
			if($this->adminid>0){
				if($aid != $this->adminid)exit('访问与当前用户冲突;');
			}else{
				$rs = m('logintoken')->getone("`uid`='$aid' and `token`='$token' and `online`=1",'`name`');
				if(!$rs)exit('illegal request2');
				$this->adminname	= $rs['name'];
				$this->adminid 		= $aid;
				$this->admintoken	= $token;
				$this->rock->adminid			= $this->adminid;
				$this->rock->adminname			= $this->adminname;
				$this->setsession($this->adminid, $this->adminname, $token);
				$baid				= $aid;
			}
		}
		return $baid;
	}
}