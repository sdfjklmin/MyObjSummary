<?php 
/**
*	api的接口请求访问：http://我的域名/api.php/index/test
*/
include_once('config/config.php');
$_paths = '';
$d		= 'task';$m	= 'index';$a = 'index';
if(isset($_GET['m'])){
	$m  = $rock->get('m');
	$a  = $rock->get('a', $a);
}else{
	if(isset($_SERVER['PHP_SELF']))$_paths=$_SERVER['PHP_SELF'];
	if($_paths==''&&isset($_SERVER['ORIG_PATH_INFO']))$_paths=$_SERVER['ORIG_PATH_INFO'];
	$_patha = explode('api.php', $_paths);
	$_paths = '/index/index';
	if(isset($_patha[1])){
		$_paths = $_patha[1];
	}else{
		if(isset($_SERVER['PATH_INFO']))$_paths=$_SERVER['PATH_INFO'];
	}
}
unset($_GET['d']);
unset($_GET['m']);
unset($_GET['a']);
if($_paths){
	$_pa = explode('/', $_paths);
	if(isset($_pa[1])&&$_pa[1])$m=$_pa[1];
	if(isset($_pa[2])&&$_pa[2])$a=$_pa[2];
}
if(substr($m,0,4)=='open'){
	$m 	= ''.$m.'|openapi';
}else{
	$m 	= ''.$m.'|api';
}
include_once('include/View.php');