<?php
/**
 *  设计模式
 * @see  https://www.ctolib.com/docs-php-design-patterns-c-index.html
 */
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
echo "<pre />";

$mode = require './config.php';

//参数处理
if(isset($_POST['mode']) && !empty($_POST['mode'])) {
    # 对应文件前三个字母
    $arg = $_POST['mode'] ;

    # 判断模式简码是否存在
    if (empty($mode[$arg]) && !isset($mode[$arg])) {
        exit('not find design mode');
    }

    # 判断对应简码文件是否存在
    list($name,$aliasName,$achieve) = $mode[$arg];
    if(!$achieve) {
        exitMsg('该模式正在验证中 。。。');
    }
    if (!file_exists($name.'.php')) {
        exit('no file match');
    }

    # 引入文件
    require_once './'.$name .'.php';
    exitMsg();
    # 自动加载 php 自带加载类(设计模式中有些是接口实现)
    /* function __autoload($class_name) {
         require_once $class_name . '.php';
     }*/
    /*spl_autoload_register(function ($class_name) {
       require_once './'.$class_name . '.php';
    });*/

}

$sin = $_GET['sin'] ?? '' ;
//预定义页面输出
$options = '' ;
foreach ($mode as $m => $v) {
    list($enName,$zhName) = $v ;
    if($m == $sin) {
        $options .= " <option selected='selected' value='".$m."'>".$enName.' | '.$zhName." </option> " ;
    }else{
        $options .= " <option  value='".$m."'>".$enName.' | '.$zhName." </option> " ;
    }
}
$predefined =<<<PRE
<form method="post">
    模式选择:<select name="mode">
            " $options "
        </select> <br />
    <input type="submit" style="color: red;font-size: 18px" value="Go">
</form>
PRE;
echo $predefined ;

//输出提醒
function exitMsg($msg = '')
{
    if($msg) echo $msg ;
    $mode = $_POST['mode'] ?? '' ;
    exit("<div><a href='try.php?sin=".$mode."' style='color: red;font-size: 18px'>Backspace</a></div>");
}
