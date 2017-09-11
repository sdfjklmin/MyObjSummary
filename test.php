<?php
$t = '0' ;
var_dump(empty($t)) ;exit();
$a= [1=>'a',2=>'b',3=>'c']  ;
foreach ($a as $key => $value) {
	# code...
	unset($a[1]) ;
}
var_dump($a) ;exit();
$a = '投注了';
var_dump(json_decode($a,true)) ;
exit(var_dump(200844%10));
header('Location: http://www.baidu.com') ;
