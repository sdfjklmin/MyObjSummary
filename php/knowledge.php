<?php

 // _Tip : 
 //    _1.
 //        能做30个题.
 //        一小时能做30个题.
 //        对整个系统进行优化和建议,编写很多底层公共
 //        独立创建出一个新东西
 //    _2.
 //        黄金思维圈    
 //          |     why     目的,理念
 //          |     how     方法,措施
 //        \   /   what    现象,成功

/*
声明:
    以下所有的只是个人测试,一切请以PHP官网手册为准
*/

	echo "<pre/>";
#1：
    $arr = [1,2,3,4];
    foreach($arr as &$v) {
        //nothing todo.
        // unset($v);
        var_dump($v);
    }
    echo "<hr />";
    var_dump($arr);
    echo "<hr />";
    foreach($arr as $v) {
        //nothing todo.
        var_dump($v);
    }
    var_export($arr); # 输出变量的字符串表示
    //output:array(0=>1,1=>2,2=>2)，你的答案对了吗？为什么
    $arr = var_export($arr,true); // 为true时,变量表示,将结果赋值给一个变量string
    //$arr "array(0=>1,1=>2,2=>2)" ;

#2:字符串替换 str_replace(search, replace, subject)
    var_dump(str_replace('a', 'b', 'a1a2a3')) ; #b1b2b3
    var_dump(str_replace(array('.','#'), array('/','.'), 'a.a.a#a#')); #a/a/a.a.

#3:方法中的静态变量会保存每次调用的值
    #eg:可用于优化 require_once
    function statictest($val)
    {
        static $_staticArr = [] ;
        if (!isset($_staticArr[$val])) {
            # code...
            $_staticArr[$val] = $val ;
        }
        var_dump($_staticArr) ;
    }    
    statictest('a') ;statictest('b');statictest('c');

#4: basename 返回路径中的文件名
    var_dump(basename('/home/index/index.php')) ;           # index.php  
    var_dump(basename('/home/index/index.php'),'.php') ;    # index

#5: dirname 返回路径中的目录部分  
    var_dump(dirname('/home/index/index.php'))     ;    # /home/index

#6: 'AbcdeFG'[0]   A  
    # ord()  返回对应的ASCII码值 可以对字符串做相应处理 如 ABcdE转换成 a_bcd_e
    ord('a') ;

#7: chr(number)   返回指定的字符串 number为ASCII码值
    # 大写转小写 +32
    # 小写转大写 -32
    chr(65) ; # A
    chr(90) ; # Z
    chr(97) ; # a
    chr(122) ; # z

#8: sprintf 字符串替换     
    # -s 字符类型
    # -u 数字类型
    $n = 2;
    $str = "Shanghai";
    $txt = sprintf("test %u %s %s",$number,$str,$str);

#9: extract  从数组中将变量导入到当前的符号表(用于前端解析)
    $t = 'test' ;
    $arr = [
        'abc'=>'testAbc',
        'def'=>'testDef',
        'ghi'=>'testGhi',
    ] ;
    extract($arr,EXTR_PREFIX_SAME,$t) ;
    echo "$abc,$def,$ghi,$t";
    // testAbc,testDef,testGhi,test
#10:DIRECTORY_SEPARATOR php系统常量即文件分隔符    

#11:ClI模式启动php
    //进入到项目目录=> php -S localhost:8000     
    //启动时指定根目录=> php -S localhost:8000 -t foo/

#12:获取数组第一个的值
    current(['firstErr'=>'666',2=>'2332']) ;
    //666

#13:list将数组的值解析到对应的变量中
    $initArr = ['this is name','this is other'] ;
    list($name,$other) = $initArr ;

#14:后台运行脚本程序 加 &
    #eg: /usr/local/php/bin/php /var/www/passport/yii queue/listen &

#15:去掉小数点后的00 : floatval(1.00) //1
#   ceil(4.3) //5 进一取整 ceil(-3.14);  // -3
#   floor(4.3);   // 4 舍去取整

#16: compact 组建一个数组
   $a = 'A' ;
   $b = 'B' ;
   $c = 'C' ;
   compact('a','b','c') ;
   // ['a'=>'A','b'=>'B','c'=>'C']

#17:parse_str 进一串字符串数据解析成数组
   $dataStr = "BillNo=1545792840227&FundChannel=CFT&ChannelNo=4200000224201812265299127294&MerNo=46548&Amount=0.01&BankNo=0230324421&OrderNo=0230324421&Succeed=88&Result=SUCCESS";
   parse_str($dataStr,$data);
   var_dump($data);

#18:file_get_contents("php://input"); 获取请求参数
   //数据格式根据请求参数解析,情况如下
   //A=1&B=2&C=3  row 参数拼接(可能是POST|GET|PUT|其它)
   //{'name':'abc'}  json
   //<MerNo>46548</MerNo> xml
   //more
   $data = file_get_contents("php://input");

#19: 标准xml解析(xml数据格式不对可能会出错)
    $str = 'xml-data';
    $a = simplexml_load_string($str);
    $a = json_encode($a);
    var_dump(json_decode($a, true));

#20:#bool property_exists ( mixed $class , string $property ) 检查对象或类是否具有该属性
    #ethod_exists ( mixed $object , string $method_name ) 检查类的方法是否存在
    #class_exists('MyClass') 检查类是否纯在
    #interface_exists 检查接口是否已被定义
    #get_class 返回对象的类名

#21:php.ini => expose_php = Off|on 关闭PHP显示信息

#22:$_SERVER['REQUEST_METHOD'] 获取请求方式: GET,POST,PUT,DELETE,...
    $_PUT = array();
    if ('PUT' == $_SERVER['REQUEST_METHOD']) {
        parse_str(file_get_contents('php://input'), $_PUT);
    }

#23:限定方法的参数类型
    //输入为数组
    function typeArg(array $arr)
    {
        return $arr;
    }
    //输入为null|array
    function typeArg2(?array $arr)
    {
        return $arr ;
    }

#24: final class ;最终类(不能用于继承)
#    abstract class ; 抽象类(只能用于继承,和实现里面的方法)
#    interface class ;接口类(只用用于继承实现)
#    static function ;静态方法(无需new class)

#25: call_user_func_array(callable $callback , array $param_arr ) 调用回调函数，并把一个数组参数作为回调函数的参数
     #调用 $foo->bar($arg1,$arg2)
     call_user_func_array(array($foo, "bar"), array("three", "four"));
     // bar($arg1,$arg2)
     call_user_func_array('bar', array("three", "four"));

#26:可变函数
    function foo() {
        echo "In foo()<br />\n";
    }
    function bar($arg = '') {
        echo "In bar(); argument was '$arg'.<br />\n";
    }
    // 使用 echo 的包装函数
    function echoit($string)
    {
        echo $string;
    }
    $func = 'foo';
    $func();        // This calls foo()
    $func = 'bar';
    $func('test');  // This calls bar()
    $func = 'echoit';
    $func('test');  // This calls echoit()

#27:可变参数 ...标识可变
# $args为数组形式,无论传多少参数都会以数组的形式保存在$args中
# $args一般为一维数组或者多维,主要看传参
    function tt(...$args) { return 'change'; }

#28: 内置web server服务
#   php -S localhost:20002 -t ./
#    -S  启动内置服务
#    -t  指定运行目录 文件夹形式 ,默认文件为 index.php
#    省略-t  php -S localhost:20002 index.php  以index.php作为路由文件


#29 cURL error 60: SSL certificate problem: unable to get local issuer certificate
#访问https://curl.haxx.se/docs/caextract.html，下载cacert.pem，并在php.ini文件添加
#curl.cainfo="extras/ssl/cacert.pem"
#openssl.cafile="extras/ssl/cacert.pem"
