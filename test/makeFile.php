<!DOCTYPE html>
<html>
<head>
	<title>文件生成</title>
</head>
<body>
<form action="" method="post">
  文件名字:	<input type="text" name="fileName">
  			<input type="submit" value="submit">
</form>
</body>
</html>
<?php
#生成文件
if ($_POST['fileName']) {
	# code...
	$str = $_POST['fileName'];
	$arr = explode('.',$str) ;
	$arrCount = count($arr) ;


	if ($arrCount == 1) {
		$suffix = 'php' ;
		$str .= '.php' ;
	}else{
		$suffix = $arr[$arrCount-1] ;
	}

	$suffixConf = [
	'php','html','txt','js','md'
	] ;
	$suffinxWrite = [
		'php' => '<?php 
namespace MyObjSummary ;
require_once("./function.php") ;
		' ,
	];

	if (!in_array($suffix, $suffixConf)) {
		echo "文件格式只支持:".implode($suffixConf, ',');
		exit();
	}
	if (file_exists($str)) {
		# code...
		exit('文件已经存在!');
	}
	file_put_contents($str, $suffinxWrite[$suffix],FILE_APPEND) ;
}
