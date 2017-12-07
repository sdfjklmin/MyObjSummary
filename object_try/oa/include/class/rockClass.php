<?php 
/**
	*****************************************************************
	* 联系QQ： 290802026/1073744729									*
	* 版  本： V2.0													*
	* 开发者：雨中磐石工作室										*
	* 邮  箱： qqqq2900@126.com										*
	* 说  明: 基础操作类方法										*
	* 备  注: 未经允许不得商业出售，代码欢迎参考纠正				*
	*****************************************************************
*/ 
final class rockClass
{
	public $ip;
	public $host;
	public $url;
	public $win;
	public $web;
	public $unarr;
	public $now;
	public $date;
	public $jm;
	

	public $adminid;
	public $adminuser;
	public $adminname;

	public function __construct()
	{		
		$this->ip		= isset($_SERVER['REMOTE_ADDR'])	? $_SERVER['REMOTE_ADDR']	: '' ;
		$this->host		= isset($_SERVER['HTTP_HOST'])		? $_SERVER['HTTP_HOST']		: '' ;
		$this->url		= '';
		$this->win		=  php_uname();
		$this->web		= isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']	: '' ;
		$this->web		= $this->getbrowser($this->web);
		$this->unarr	= explode(',','1,2');
		$this->now		= $this->now();
		$this->date		= date('Y-m-d');
		$this->lvlaras  = explode(',','select ,alter table,delete ,drop ,update ,insert into,load_file,outfile');
		$this->lvlaraa  = explode(',','select,alter,delete,drop,update,insert,from,time_so_sec,convert,from_unixtime,unix_timestamp,curtime,time_format,union,concat,information_schema,group_concat,length,load_file,outfile,database,system_user,current_user,user(),found_rows,declare,master,exec');
		$this->lvlarab	= array();
		foreach($this->lvlaraa as $_i)$this->lvlarab[]='';
	}
	
	public function initRock()
	{
		$this->jm 		= c('jm', true);
		$this->adminid	= (int)$this->session('adminid',0);
		$this->adminname= $this->session('adminname');
		$this->adminuser= $this->session('adminuser');
	}
	
	public function iconvsql($str,$lx=0)
	{
		$str = strtolower($str);
		$str = str_replace($this->lvlaraa,$this->lvlarab,$str);
		$str = str_replace("\n",'', $str);
		if($lx==1)$str = str_replace(array(' ',' ','	'),array('','',''),$str);
		return $str;
	}
	
	private function unstr($str)
	{
		$ystr = '';
		for($i=0;$i<count($this->unarr);$i++){
			if($this->contain($str,$this->unarr[$i])){
				$ystr = $this->unarr[$i];
				break;
			}
		}
		return $ystr;
	}

	public function get($name,$dev='', $lx=0)
	{
		$val=$dev;
		if(isset($_GET[$name]))$val=$_GET[$name];
		if($this->isempt($val))$val=$dev;
		return $this->jmuncode($val, $lx, $name);
	}
	
	public function post($name,$dev='', $lx=0)
	{
		$val  = '';
		if(isset($_POST[$name])){$val=$_POST[$name];}else{if(isset($_GET[$name]))$val=$_GET[$name];}
		if($this->isempt($val))$val=$dev;
		return $this->jmuncode($val, $lx, $name);
	}
	
	public function request($name,$dev='', $lx=0)
	{
		$val  = '';
		if(isset($_REQUEST[$name]))$val=$_REQUEST[$name];		
		if($this->isempt($val))$val=$dev;
		return $this->jmuncode($val, $lx, $name);
	}
	
	public function jmuncode($s, $lx=0, $na)
	{
		$jmbo = false;
		if($lx==3)$jmbo = $this->isjm($s);
		if(substr($s, 0, 7)=='rockjm_' || $lx == 1 || $jmbo){
			$s = str_replace('rockjm_', '', $s);
			$s = $this->jm->uncrypt($s);
			if($lx==1){
				$jmbo = $this->isjm($s);
				if($jmbo)$s = $this->jm->uncrypt($s);
			}
		}
		$s=str_replace("'", '&#39', $s);
		if($lx==2)$s=str_replace(array('{','}'), array('[H1]','[H2]'), $s);
		$str = strtolower($s);
		foreach($this->lvlaras as $v1)if($this->contain($str, $v1)){
			$this->debug(''.$na.'《'.$s.'》error:包含非法字符《'.$v1.'》','params');
			$s = str_replace($v1,'', $str);
		}
		return $s;
	}
	
	public function debug($txt, $lx)
	{
		if(!DEBUG)return;
		$_dt 	= date('Y-m');
		$fdir	= ''.ROOT_PATH.'/upload/'.$_dt.'';
		if(!file_exists($fdir))mkdir($fdir);
$txt	= ''.$txt.'
URL：'.$_SERVER['QUERY_STRING'].'
';
		$this->createtxt('upload/'.$_dt.'/'.$lx.''.date('YmdHis').'_'.rand(1000,9000).'.log', $txt);
	}
	
	private function isjm($s)
	{
		$bo = false;
		if(!$s)return $bo;
		$bo = preg_match("/^([a-z]{2,3})0([a-z]{2,3})0([a-z]{2,3})0([a-z0])*([1-9]{1,2})$/", $s);
		return $bo;
		$a 	= explode('0', $s);
		$len= count($a);
		if($len>1){
			$ls=(int)$a[$len-1];
			if($ls>=1&&$ls<=14)$bo=true;
		}
		return $bo;
	}
	
	public function savesession($arr)
	{
		foreach($arr as $kv=>$vv)$this->setsession($kv,$vv);
	}
	
	public function setsession($kv,$vv)
	{
		$_SESSION[QOM.$kv]=$vv;
	}
	
	public function session($name,$dev='')
	{
		$val	= '';
		$name 	= QOM.$name;
		if(isset($_SESSION[$name]))$val=$_SESSION[$name];
		if($this->isempt($val))$val=$dev;
		return $val;
	}
	
	public function clearsession($name)
	{
		$arrn=explode(',',$name);
		for($i=0;$i<count($arrn);$i++){
			@$_SESSION[QOM.$arrn[$i]]='';
		}
	}
	
	public function clearallsession()
	{
		foreach($_SESSION as $key=>$value){
			$this->clearsession($key);
		}
	}	
	
	//保存cookie，默认是7天
	public function savecookie($namarr,$valarr,$expire=360,$path='/',$domain='')
	{
		$time 	= time()+$expire*3600*24;
		$arrn	= explode(',',$namarr);
		$valn	= $valarr;
		if(!is_array($valarr))$valn=explode(',',$valarr);
		for($i=0;$i<count($arrn);$i++){
			setcookie(QOM.$arrn[$i],$valn[$i], $time, $path,'');
		}
	}
	
	//获取cookie
	public function cookie($name,$dev='')
	{
		$val	= '';
		$name 	= QOM.$name;
		if(isset($_COOKIE[$name]))$val=$_COOKIE[$name];
		if($this->isempt($val))$val=$dev;
		return $val;
	}
	
	public function getcookie($namarr)
	{
		$arrn=explode(',',$namarr);
		for($i=0;$i<count($arrn);$i++){
			$val[$arrn[$i]]=$this->cookie($arrn[$i]);
		}
		return $val;
	}
	
	//删除cookie
	public function clearcookie($name,$path='/',$domain='')
	{
		//$domain=(empty($domain))?$this->host:$domain;
		$arr=explode(',',$name);
		for($i=0;$i<count($arr);$i++){
			setcookie(QOM.$arr[$i],'',time()-1,$path,$domain);
			@$_COOKIE[$arr[$i]]='';
		}
	}
	
	//删除所有cookie
	public function clearallcookie()
	{
		foreach($_COOKIE as $key=>$value){
			$this->clearcookie($key);
		}
	}	
	
	//跳转
	public function location($url)
	{
		header('location:'.$url.'');
		exit;
	}
	
	public function now($type='Y-m-d H:i:s',$kmti='')
	{
		if($kmti=='')$kmti=time();
		return date($type,$kmti);
	}
	
	public function cnweek($date)
	{
		$arr = array('日','一','二','三','四','五','六');
		return $arr[date('w', strtotime($date))];
	}	
		
	public function getbrowser()
	{
		$web	= $this->web;
		$val	= 'IE';
		$parr	= array(
			array('MSIE 5'),array('MSIE 6'),array('MSIE 7'),array('MSIE 8'),array('MSIE 9'),array('MSIE 10'),array('MSIE 11'),array('rv:11','MSIE 11'),array('MSIE 12'),array('MicroMessenger','wxbro'),
			array('MSIE 13'),array('Firefox'),array('OPR/','Opera'),array('Chrome'),array('Safari'),array('Android'),array('iPhone')
		);
		foreach($parr as $wp){
			if($this->contain($web, $wp[0])){
				$val	= $wp[0];
				if(isset($wp[1]))$val	= $wp[1];
				break;
			}
		}
		return $val;
	}
	
	public function script($daima)
	{
		echo '<script type="text/javascript">
		'.$daima.'
		</script>';
	}
	
	/**
		全角半角转换
	*/
	public function replace($str,$type='ban')
	{
		$search=array('0','1','2','3','4','5','6','7','8','9',',','.','?','\'','(',')',';','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$replace=array('０','１','２','３','４','５','６','７','８','９','，','。','？','’','（','）','；','ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ','Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｚ','Ｙ','Ｚ');
		if($type=='ban'){
			$str=str_replace($replace,$search,$str);
		}else{
			$str=str_replace($search,$replace,$str);
		}
		return $str;
	}
	
	/**
		过滤特殊符合
	*/
	public function repmark($str)
	{
		$search=array('select','delete','join','inner','outer');
		$str=strtolower($str);//转为小写
		$str=str_replace($search,'',$str);
		return $str;
	}
	
	/**
		html编码
	*/
	public function htmlescape($str)
	{
		$str = htmlspecialchars($str);
		return $str;
	}
	
	/**
		小数点位数
	*/
	public function number($num,$w=2)
	{
		if(!$num)$num='0';
		return number_format($num,$w,'.','');
	}
	
	/**
		是否包含返回bool
	*/
	public function contain($str,$a)
	{
		$bool=false;
		if(!$this->isempt($a) && !$this->isempt($str)){
			$ad=strpos($str,$a);
			if($ad>0||!is_bool($ad))$bool=true;
		}
		return $bool;
	}
	
	/**
		将&#39;转换'
	*/
	public function covexec($str)
	{
		$dt  = date('Y-m-d');
		$str = str_replace(
			array('&#39;', '&#39','[F]', '[X]', '[K]', '[A]', '[D]', '[adminid]', '[date]', '{adminid}', '{date}','[H1]','[H2]'), 
			array('\'', '\'', '\'', '\\', ' ', 'and', '=', $this->adminid, $dt, $this->adminid, $dt,'{','}'),
			$str
		);
		return $str;
	}
	
	//判断是否为空
	public function isempt($str)
	{
		$bool=false;
		if( ($str==''||$str==NULL||empty($str)) && (!is_numeric($str)) )$bool=true;
		return $bool;
	}
	
	/**
		地址
	*/
	public function rewrite($m,$a,$s)
	{
		$url	= '';
		if(REWRITE=='true'){
			$url	= ''.$m.'';
			if($a == '' && $s == ''){
				$url	= ''.$url.'.html';
			}elseif($a == ''){
				$url	= ''.$url.'_'.$s.'.html';
			}else{
				$url	= ''.$url.'_'.$a.'_'.$s.'_a.html';
			}
		}else{
			$url	= 'index.php?m='.$m.'';
			if($a != '')$url.='&a='.$a.'';
			if($s != '')$url.='&s='.$s.'';
		}
		return $url;	
	}	
	
	//设置所有的GET方法
	public function setallcan($rep=4)
	{
		foreach($_GET as $key=>$val)$GLOBALS['get_'.$key]=$this->get($key,'',0);
		foreach($_POST as $key=>$val)$GLOBALS['post_'.$key]=$this->post($key,'',0);
	}
	
	/**
		如果字符为空，使用默认的
	*/
	public function repempt($str,$dev='')
	{
		$s	= $str;
		if($this->isempt($s))$s=$dev;
		return $s;
	}
	
	//返回文件大小
	public function formatsize($size)
	{
		$arr = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
		if($size == 0)return '0';
		$e = floor(log($size)/log(1024));
		return number_format(($size/pow(1024,floor($e))),2,'.','').' '.$arr[$e];
	}
	
	/**
		采集字符串截取
	*/
	public function getcai($content,$start,$end)
	{
		$geju = strpos($content,$start);
		if($geju===false){
			$cont1='';
		}else{
			$stard	= $geju+strlen($start);
			$cont1	= substr($content,$stard);
			$endd	= strpos($cont1,$end);
			$cont1	= substr($cont1,0,$endd);
			$cont1	= trim($cont1);
		}
		return $cont1;
	}
	
	/**
		采集字符串截取
	*/
	public function createtxt($path, $txt)
	{
		$path	= ''.ROOT_PATH.'/'.$path.'';
		@$file	= fopen($path,'w');
		$bo 	= false;
		if($file){
			$bo = true;
			if($txt)$bo = fwrite($file,$txt);
			fclose($file);
		}
		return $bo;
	}
	
	public function stringformat($str, $arr=array())
	{
		$s	= $str;
		for($i=0; $i<count($arr); $i++){
			$s=str_replace('?'.$i.'', $arr[$i], $s);
		}
		return $s;
	}
	
	public function strformat($str)
	{
		$len = func_num_args();
		$arr = array();
		for($i=1; $i<$len; $i++)$arr[] = func_get_arg($i);
		$s	 = $this->stringformat($str, $arr);
		return $s;
	}
	
	public function T($n)
	{
		return PREFIX.''.$n;
	}
	
	public function reparr($str, $arr=array())
	{
		if($this->isempt($str))return '';
		preg_match_all('/\{(.*?)\}/', $str, $list);
		$s	= $str;
		foreach($list[1] as $k=>$nrs){
			$nts = '';
			if(isset($arr[$nrs]))$nts = $arr[$nrs];
			$s	= str_replace('{'.$nrs.'}', $nts, $s);
		}
		return $s;
	}
	
	/**
		字段中包含
	*/
	public function dbinstr($fiekd, $str, $spl1=',', $spl2=',')
	{
		return "instr(concat('$spl1', $fiekd, '$spl2'), '".$spl1.$str.$spl2."')>0";
	}
	
	public function debugs($str, $lxs='')
	{
		if(!DEBUG)return;
		$msg 	= '['.$this->now.']:'.$str.'';
		$mkdir 	= 'upload/'.date('Y-m').'';
		if(!is_dir($mkdir))mkdir($mkdir);
		$this->createtxt(''.$mkdir.'/'.$lxs.''.time().'_'.rand(100,999).'.log', $msg);
	}
	
	public function arrvalue($arr, $k, $dev='')
	{
		$val  = $dev;
		if(isset($arr[$k]))$val= $arr[$k];
		return $val;
	}
	
	public function nowurl()
	{
		$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		return $url;
	}
}