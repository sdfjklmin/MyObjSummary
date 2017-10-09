<?php
$test = [
	'app_id'=>'322323232sdsd',
	'app_key'=>'ssdssdsd23sdkjhkjhsdkj',
	'corp_id'=>'sssd324sd',
] ;

$str = json_encode($test) ;
$strMd = base64_encode($str) ;
$str2 = base64_decode($strMd) ;
$data = json_decode($str2,true) ;
$token = '23232' ;
var_dump($strMd,$str2,$data,compact('token'),uniqid());


