<?php
include_once('../../config/config.php');
if($rock->adminid==0)exit('not sign');
$upfile = c('upfile');
$upfile->initupfile('jpg|png|gif|jpeg','../../upload|'.date('Y-m').'', 5);
$upses	= $upfile->up('imgFile');
if(is_array($upses)){
	$url = $upses['allfilename'];
	$url = str_replace('../' , '', $url);
	$arr = array('error' => 0, 'url' => $url);
}else{
	$arr = array('error' => 1, 'message' => $upses);
}
echo json_encode($arr);