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
    # -d 数字类型
    $n = 2;
    $str = "Shanghai";
    $txt = sprintf("test %u %s %s",$n,$str,$str);

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
    //骚操作
    $arr = [
        ['tt','tt1','tt2'],
        ['ss','ss1','ss2'],
        ['bb','bb1','bb2'],
    ];
    //只取二维数组第一个
    foreach ($arr as list($item)) {
        //list($item) = $item
        //扩展 list($one,$two,$three) = $item
        var_dump($item);// tt ss bb
    }
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
	#get_parent_class() 获取父类名称

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

#24: final class className;最终类(不能用于继承)
#    abstract class className; 抽象类(只能用于继承,和实现里面的方法)
#    interface className ;接口类(只用用于继承实现)
#    static function functionName;静态方法(无需new class)
final class EndClass
{

}
abstract class AbClass
{

}
interface InterfaceClass
{

}

#25: call_user_func_array(callable $callback , array $param_arr ) 调用回调函数，并把一个数组参数作为回调函数的参数
	#call_user_func ( callable $callback [, mixed $parameter [, mixed $... ]] ) : mixed
	#第一个参数 callback 是被调用的回调函数，其余参数是回调函数的参数。

	#单一函数调用
	function ttCallUser($one, $two)
	{
		echo 'call_user_func_array';
	}
    call_user_func_array('ttCallUser',['one','two']);
	call_user_func('ttCallUser','1','2');

    #类调用
	class CallUser
	{
		public function tt($one, $two)
		{
			echo 'call_user_func_array';
		}
	}
	$ttCallUser = new CallUser();
	call_user_func_array([$ttCallUser,'tt'],['one','two']);
	call_user_func([$ttCallUser,'tt'],'one','two');

	#匿名函数调用
	class CallUser2
	{
		public function tt($one, $two)
		{
			$this->intoCallback(function ()use ($one, $two){
				echo "do something";
			});
		}

		public function intoCallback(callable $callable)
		{
			if(is_callable($callable)) {
				call_user_func_array($callable,[$this]);
			}
		}
	}

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

#30 array_combine($key,$data) 组合key和data
    $key = [2,3] ;
    $data = ['2data','3data'] ;
    array_combine($key,$data);
    /*array:2 [
        2 => "2data"
        3 => "3data"
    ]*/

	#给 receive  的key 批量加前缀
	$receive = [
		'id' => 1,
		'name' => 'receive name'
	];
	$myNewArray = array_combine(
		array_map( function($key) {
			return 'receive_member_'.$key;
		}, array_keys($receive)),
		$receive
	);
	var_dump($myNewArray);

#31 官方建议PHP文件以 ? > 结尾 ，但是语法标准和文件中并未这样做，为什么 ？
# 避免文件引入的时候有多余的空格或者其它字符引起报错

#32 在设置 header() 前不能有任何输出，否则PHP文件会报错 。

#33 setcookie() 定义了 Cookie，会和剩下的 HTTP 头一起发送给客户端。
# 和其他 HTTP 头一样，必须在脚本产生任意输出之前发送 Cookie（由于协议的限制）。
# 请在产生任何输出之前（包括 <html> 和 <head> 或者空格）调用本函数。
#一旦设置 Cookie 后，下次打开页面时可以使用 $_COOKIE 读取。 Cookie 值同样也存在于 $_REQUEST。

#33 浏览的Cookie操作都是通过HTTP Header(俗称“Http头”) 来实现。
#所有的服务器与客户端之间Cookie数据传输都是通过Http请求头来操作。
#PHP中setCookie(函数的实现)，就是对HTTP头进行封装，由此看来 使用 header 与 使用setCookie是一样的。
#由于header头信息属于HTTP协议内容，必须先把头信息发送到服务器，再进行数据下载等其他操作，
#所以在setCookie 与 header 之前不能有任何内容输出（例如：echo/printf等）


#34
# https://blog.csdn.net/baixiaoshi/article/details/71848383
# https://blog.csdn.net/zhangbijun1230/article/details/80474988
#进程 ： 系统进行资源分配和调度的基本单位 （pcntl_fork）（可通过 缓存、数据库、共享内存进行数据处理）
#       一个执行中的程序 ， 一个进程中至少有一个执行的流程（主线程），也可以开启新的执行流程（线程）
#线程 ： 操作系统能够进行运算调度的最小单位
#       多个执行流程 ， 一个线程可以执行多个协程
#协程 ： 用户态完成程序的调度，像系统调度进程和线程一样
#       比线程更加轻量级 ，完全被程序代码所调度和掌控， 不用操作系统介入
#       swoole解释 : 协程是一种轻量级的线程，由用户代码来调度和管理，而不是由操作系统内核来进行调度，也就是在用户态进行。
#                   可以直接的理解为就是一个非标准的线程实现，但什么时候切换由用户自己来实现，而不是由操作系统分配 CPU 时间决定。
#       使用setjmp和longjmp跳转来实现上下文的恢复来实现协程、使用ucontext库函数来实现协程、腾讯开源的libco协程库
#
#进程(资源分配和调度) 给 线程(运算调度) 由操作系统控制.
#协程(用户态完成程序的调度，像系统调度进程和线程一样)
#
#CPU : 进行运算和处理的地方    工人
#内存 : 交换数据				车间
#磁盘 : 数据存储的地方			原料仓库
#CPU ---获取--->内存<---输入/输出--->磁盘(有I/O瓶颈)
#---------------------------------------------------------------------------
#| CPU	: 工厂	进行运算和处理的地方
#| CPU核: 电源	提供运算的资源
#| 进程	: 车间	提供内存,数据,堆栈
#| 线程	: 工人	执行操作
#| 内存	: 车间原料,供工人使用,数量一定,没有I/O
#| 磁盘	: 原料仓库,当车间原料不足时,从仓库获取,有一定的I/O
#| 协程	: 用户提供的帮工,用户态完成程序的调度
#| CPU工厂,我有两个电源提供运算cpu-member,cm1,cm2,每个电源只能处理一个进程,
#| 其它进程需要等待,进程可以类比车间,线程可类比多个工人,它们共同完成一个任务(用户发起的)
#| 车间的空间是工人共享的,比如车间有很多房间,工人都可以共同使用,但每个房间的大写不同,
#| 比如厕所,只能有一个人使用,所有其它线程需要等待,为了防止别人进入,需要加个锁,先到的人进入
#| 上锁,其他人依次排队(互斥锁),防止多个线程同时读写某一块内存区域,还有些房间,
#| 可以同时容纳n个人,比如厨房.如果人数大于n,多出来的人只能在外面等着.
#| 这好比某些内存区域,只能供给固定数目的线程使用,这时的解决方法,就是在门口挂n把钥匙.
#| 进去的人就取一把钥匙,出来时再把钥匙挂回原处.后到的人发现钥匙架空了,就知道必须在门口排队等着了。
#| 这种做法叫做”信号量”（Semaphore），用来保证多个线程不会互相冲突
#|
#|		[ 我是进程,这里是我的内部空间,其它进程不可见,cm1来了后我会进行工作,
#|		  其它进程处于等待状态(单核情况),cm1可以切到其它进程中|
#|		  这里是进程中共享的内存空间(代码,数据,堆),线程们来用啊
#|	 	  {哈哈,我就是线程x1} {老妹,我也是线程x2} {谁还不是个线程x3} {都™是线程啊x5}
#|        {我们都是线程,都可以相互切换,比进程之间切换开销要小的多哦x6}
#|        <无限制空间,线程都可以使用> <一个空间,只能有一个线程使用,形成互斥锁>
#|		  <有限制空间n,只能接纳n个线程,信号量-一开始有n个钥匙,进一个消耗一个,出去了在加个>
#|		]
#|  	 	[进程1]
#|								[进程2]
#|  					[进程3]
#|									[进程4]
#|		[进程5]
#|
#---------------------------------------------------------------------------


#35 jsonp 解决跨域问题 Json of Padding (调用js文件是不受跨域影响)
/*$.ajax({
             type: "get",
             async: false,
             url: "http://flightQuery.com/jsonp/flightResult.aspx?code=CA1998",
             dataType: "jsonp",
             jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
             jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
             success: function(json){
                alert('您查询到航班信息：票价： ' + json.price + ' 元，余票： ' + json.tickets + ' 张。');
            },
             error: function(){
                alert('fail');
            }
         });
     })*/

#36 框架模型
#laravel分为三大数据库操作(DB facade[原始查找]，查询构造器[Query Builder]，Eloquent ORM（ActiveRecord）)：

#37 composer  是 PHP 的一个依赖管理工具 。
# classmap , files , namespaces , psr4 , static


#38  nginx+phpfpm工作原理
/*www.example.com
|
        |
      Nginx
|
        |
路由到www.example.com/index.php
|
        |
加载nginx的fast-cgi模块
|
        |
fast-cgi监听127.0.0.1:9000地址
|
        |
www.example.com/index.php请求到达127.0.0.1:9000
|
        |
php-fpm 监听127.0.0.1:9000
|
        |
php-fpm 接收到请求，启用worker进程处理请求
|
        |
php-fpm 处理完请求，返回给nginx
|
        |
nginx将结果通过http返回给浏览器*/

#39 负载均衡
# 通过某种负载分担技术，将外部发送来的请求均匀分配到对称结构中的某一台服务器上，而接收到请求的服务器独立地回应客户的请求。
#均衡负载能够平均分配客户请求到服务器列阵，借此提供快速获取重要数据，解决大量并发访问服务问题。

#40 1 6
# 运算符优先级
# @link https://www.php.net/manual/zh/language.operators.precedence.php
$a = 3 ;
$b = 5;
if($a = 5 || $b = 7) {
	//t.  ||,一边条件成立即可,优先级大于 = 的优先级
	//a.  以下是猜的
	//e.  $a = 5 || $b = 7
	//0.  先执行 ||,一边成立即可
	//1.  5 || $b; 5 为 true,这时候$b不会被赋值,因为5为true,获取 true
	//2.  $a = true; 获得 true;
	//3.  执行 if 内的代码

	// $a = 0 || $b = 7;  0 为false,执行 $b=7; $a = ( 0 || true) = true;
	$a++;
	$b++;
}
echo $a.' '.$b,"\n" ; // 1  6
var_dump($a,$b); // true  6


$a = 3 ;
$b = 5 ;
$c = 7 ;
if( $a = 1 && $b = 2  && $c = 5 ) {
	//t.  &&,两边条件都成立,优先级大于 = 的优先级
	//a.  以下是猜的
	//0.  先执行第一个 && 而 $b 的值依赖于 后面的内容,则先执行依赖,即最后一个 &&
	//1.  $c = 5; 获得 5;
	//2.  $b = (2 && 5); 获得 true;
	//3.  $a = (1 && true); 获得 true;
	//r.  遇到多个 && 是,代码先从左执行,但是它的值依赖于最后一个 && ,所以最后一个 && 先运算
	//t.  ( $a = ( 1 && ($b = (2 && ($c = 5) ) ) ) ) 完整的过程
	//echo 'into',"\n";
	$a++;
	$b++;
	$c++;
}

echo $a.' '.$b.' '.$c,'---',"\n";
var_dump($a,$b,$c);exit();

#41
#分布式(不同业务模块分布到对应的服务器通过API相互访问)[模块]
#集群(同一个系统分布到不同的服务器中)[系统]

#42
#strip_tags — 从字符串中去除 HTML 和 PHP 标记

#43 命名空间
//namespace app\common;  申明命名空间
//use app\common\model\System\SystemConfig; 使用某个文件
//use function function\dd; 使用某个方法,示例如下
/*`
    namespace function;
    function dd()
    {
        echo 11;
    }
`*/

#44 parse_ini_file  解析一个配置文件
#parse_ini_file() 载入一个由 filename 指定的 ini 文件，并将其中的设置作为一个联合数组返回。


#45 Closure类 用于代表 匿名函数 的类.
$a = function(){
    echo 11;
};
$a(); //11
var_dump($a instanceof \Closure); //true

# 预定义接口
# https://www.php.net/manual/zh/reserved.interfaces.php
#46 ArrayAccess（数组式访问）接口 可以以数组形式访问类

#47 IteratorAggregate（聚合式迭代器）接口
//无论你用的是什么结构（链表也好、数组也好、数也好、图也好、hash表也好），
//总之， 你可以不关心任何细节遍历细节，（下面看好了） 从一个起点(begin)触发到达，到达终点(end)，
//并且保证每个节点都能走到且只走一次。
class myData implements IteratorAggregate {
    public $property1 = "Public property one";
    public $property2 = "Public property two";
    public $property3 = "Public property three";

    public function __construct() {
        $this->property4 = "last property";
    }

    public function getIterator() {
        //这里使用的数组迭代器
        return new ArrayIterator($this);
    }
}

$obj = new myData;

foreach($obj as $key => $value) {
    var_dump($key, $value);
    echo "\n";
}
/*string(9) "property1"
string(19) "Public property one"

string(9) "property2"
string(19) "Public property two"

string(9) "property3"
string(21) "Public property three"

string(9) "property4"
string(13) "last property"*/

#48 类实现 Countable 可被用于 count() 函数.
//Countable::count — 统计一个对象的元素个数
class myData2 implements Countable {

    public function count()
    {
        //return '18446744073709551616' ;
        return '1' ;
    }
}
$obj = new myData2;
echo "<pre    >";
var_dump(count($obj),$obj->count());
//1,1
//count($obj) 中的值有最大值，当数据超过最大值时，只会返回默认最大值

#48 预定义接口 : https://www.php.net/manual/zh/reserved.interfaces.php
#49 PHP标准库 (SPL) : https://www.php.net/manual/zh/book.spl.php

#50 ucwords(str_replace('_',' ',$table));
#upper change first
ucfirst('im'); #Im
#lower change first
lcfirst('Im'); #im
ucwords('im boy'); #Im Boy
strtolower('AAAA'); #aaa
strtoupper('aaa'); #AAA
$str = "mary had a Little lamb and she loved it so";
$str = mb_convert_case($str, MB_CASE_UPPER, "UTF-8");
echo $str; // 输出 MARY HAD A LITTLE LAMB AND SHE LOVED IT SO
$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
echo $str; // 输出 Mary Had A Little Lamb And She Loved It So

#51 htmlentities
//$class = 类的文本内容
//将字符转换为 HTML 转义字符
$class = htmlentities($class);
//将空格替换成html标签
$class = str_replace(' ','&nbsp;',$class);
//在字符串所有新行之前插入 HTML 换行标记
print_r(nl2br($class));


#52 基于结果判断
$user = new ArrayObject();
if($user) {
    if($user->name == 'test') {
        echo 'test';
        //more if
    }else{
        echo 'false';
    }
}else{
    echo 'false';
}
//卫语句(条件反转):将else部分条件取反进行结构优化，最终输出结果，避免内层条件越来越多
if(!$user) {
    echo 'false';
}
if($user->name != 'test') {
    echo 'false';
}
echo 'test';

#53 创建空对象
$tt = new \StdClass();

#54 获取header
function getHeader()
{
    $header = [];
    if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
        //apache function
        $header = $result;
    } elseif (function_exists('getallheaders') && $result = getallheaders()) {
        //apache function based on apache_request_headers
        $header = $result;
    } else {
        $server = $_SERVER;
        foreach ($server as $key => $val) {
            if (0 === strpos($key, 'HTTP_')) {
                $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                $header[$key] = $val;
            }
        }
        if (isset($server['CONTENT_TYPE'])) {
            $header['content-type'] = $server['CONTENT_TYPE'];
        }
        if (isset($server['CONTENT_LENGTH'])) {
            $header['content-length'] = $server['CONTENT_LENGTH'];
        }
    }
    //array_change_key_case 将数组的 key 全部变为大写或小写
    $header = array_change_key_case($header);
    return $header;
}

#55 验证字符串时间格式
$dataTime = '2019-12-27 13:35:04';
$ret1 = date_parse($dataTime);
$ret2 = date_parse_from_format('Y-m-d H:i:s',$dataTime);
//当 ret1,ret2 中的 warning_count,error_count 都为0的时候通过

#56 echo,exit 为语法结构,var_dump(),print_r为函数
echo true;#1
echo false;#没有输出
var_dump(true);//true
var_dump(false);//false

#57 如何防止重复提交?(幂等性)
//前端业务逻辑(disabled) -> 后端入口(速率验证) -> 记录单次请求缓存 -> 数据库约束,唯一索引

#58 gethostbyname(); 根据host获取id
# 系列函数: gethost{name | byname | byaddr | bynamel}
var_dump(gethostbyname('www.baidu.com'));

#59
# ip2long ,将 IPV4 的字符串互联网协议转换成长整型数字
# long2ip ,将长整型转化为字符串形式带点的互联网标准格式地址（IPV4）

#60 为一个类创造一个别名
#class_alias ( string $original , string $alias [, bool $autoload = TRUE ] ) : bool
class TestAlias
{
	public function __construct()
	{
		echo "init test alias";
	}
}
class_alias(TestAlias::class,TA::class);
$model = new TA();
var_dump($model);

#61 cli_set_process_title()
# 设置当前 PHP 脚本名称
cli_set_process_title('');

#61 bc,mb_
#精度计算: bc库,统一int型
#更精细的Str: substr(); mb_substr();
#更精细的时间: time(); microtime();

#62 pow(2,3)
# 2 的 3 次方

#63 array_slice 取数组前几个, slice(片)
$arr = [
	'a','b'
];
var_dump(array_slice($arr,0,3));

#64 fputcsv — 将行格式化为 CSV 并写入文件指针
$list = array (
	array('aaa', 'bbb', 'ccc', 'dddd'),
	array('123', '456', '789'),
	array('"aaa"', '"bbb"')
);
$fp = fopen('file.csv', 'w');
foreach ($list as $fields) {
	fputcsv($fp, $fields);
}
fclose($fp);

#65 array_chunk ( array $array , int $size [, bool $preserve_keys = false ] ) : array 分割数组
#   array_slice ( array $array , int $offset [, int $length = NULL [, bool $preserve_keys = false ]] ) : array 分割数组(取数组一部分)
$arr = [1,2,3,4];
$arr2 = array_chunk($arr,1);
# [
#    [1],
#    [2],
#    [3],
#    [4],
#]

#66. flock 文件锁定
#@link https://www.php.net/manual/zh/function.flock.php
	$fp = fopen("./lock.txt", "r+");

	//LOCK_SH 取得共享锁定（读取的程序）。
	//LOCK_EX 取得独占锁定（写入的程序）。
	//LOCK_UN 释放锁定（无论共享或独占）。
	//LOCK_NB 锁定时无阻塞操作。
	/* Activate the LOCK_NB option on an LOCK_EX operation */
	if(!flock($fp, LOCK_EX | LOCK_NB)) {
		echo 'Unable to obtain lock';
		exit(-1);
	}

	flock($fp, LOCK_UN);    // 释放锁定
	/* ... */

	fclose($fp);

#67. strtr 转换指定字符
#@link https://www.php.net/manual/zh/function.strtr.php
	//strtr ( string $str , string $from , string $to ) : string
	//strtr ( string $str , array $replace_pairs ) : string
	$trans = array("hello" => "hi", "hi" => "hello");
	echo strtr("hi all, I said hello", $trans);
	//hello all, I said hi

#68. trait 实现代码复用 (PHP为但继承，不能同时继承多个类，使用 trait 进行复用)，单基础复用机制。
#优先级: 优先顺序是来自当前类的成员覆盖了 trait 的方法，而 trait 则覆盖了被继承的方法。
trait TestTrait
{
	public function t1()
	{

	}

	public function t2()
	{

	}
}

class TestTraitClass
{
	use TestTrait;

	public function test()
	{
		$this->t1();
		$this->t2();
	}
}