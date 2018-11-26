<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
echo "<pre />";

/** 文件解释器
 * Class FileInterpreter
 */
class ZendFileInterpreter
{
    public function file($name='')
    {
        $mode = require './config.php';
        if(!isset($mode[$name])) return $mode ;
        return $mode[$name] ;
    }
}

/** 构建HTML代码
 * Class ZendBuildHtml
 */
class ZendBuildHtml
{
   public function html()
   {
       //初始化数据
       $interpreter = new ZendFileInterpreter();
       $mode = $interpreter->file() ;

       //预定义页面输出
       $options = '' ;
       foreach ($mode as $m => $v) {
           list($enName,$zhName) = $v ;
           $options .= " <option  value='".$m."'>".$enName.' | '.$zhName." </option> " ;
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
   }
}

/** 业务处理
 * Class Zend
 */
class Zend
{
    public function into($file)
    {
        //初始化数据
        $interpreter = new ZendFileInterpreter();
        $mode = $interpreter->file($file) ;

        # 判断对应简码文件是否存在
        list($name,$aliasName,$achieve) = $mode;
        if(!$achieve) {
            $this->exitMsg('该模式正在验证中 。。。');
        }
        if (!file_exists($name.'.php')) {
            exit('no file match');
        }

        # 引入文件
        require './'.$name .'.php';
        $this->exitMsg();
    }

     //输出提醒
    public function exitMsg($msg = '')
    {
        if($msg) echo $msg ;
        exit("<div><a href='Zend.php' style='color: red;font-size: 18px'>Backspace</a></div>");
    }

}

if(isset($_POST['mode']) && !empty($_POST['mode'])) {
   $zend = new Zend();
   $zend->into($_POST['mode']) ;
}

$html = new ZendBuildHtml();
$html->html();

