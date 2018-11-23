<?php
/**
 *  设计模式
 * @see  https://www.ctolib.com/docs-php-design-patterns-c-index.html
 */
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
echo "<pre />";
# 模式简码对应的文件和名称
$mode = [
	'sin'=>['Singleton','单例模式',true],
	'fac'=>['Factory','工厂模式',true],
	'obs'=>['Observerable','观察者模式',true],
	'pro'=>['Proxy','代理模式',false],
	'dec'=>['Decorator','装饰模式',true],
] ;

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

}

//预定义页面输出
$options = '' ;
foreach ($mode as $m => $v) {
    list($enName,$zhName) = $v ;
    $options .= " <option value='".$m."'>".$enName.' | '.$zhName."</option>" ;
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
    exit("<div><a href='try.php' style='color: red;font-size: 18px'>Backspace</a></div>");
}
