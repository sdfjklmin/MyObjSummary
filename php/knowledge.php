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
