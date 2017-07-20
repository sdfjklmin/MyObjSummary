<?php 
echo "<pre>";
$t = 'test' ;
$arr = [
	'abc'=>'testAbc',
	'def'=>'testDef',
	'ghi'=>'testGhi',
] ;
extract($arr) ;
echo "$abc,$def,$ghi";
echo "测试git";