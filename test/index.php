<!-- 类测试 -->
<?php 
/*tarit*/
trait WayTrait {
	public function baseWay($value='')
	{
		# code...
		//parent::baseWay();
		echo "this is trait base way",'<br />';
	}
}

/**
* PHP方法类
*/
class PhpWay 
{
	use WayTrait ; //单继承
	/*静态*/
	public static function way($value='')
	{
		# code...
		echo "this is phpway way",'<br />';
	}

	/*非静态*/
	public function way2($value='')
	{
		# code...
		echo "this is phpway way2",'<br />';
	}

	/*多个参数*/
	public function way3($v1,$v2)
	{
		# code...
		echo $v1.$v2,'<br />';
	}
}


?>
<!-- 单一测试 -->
<?php
/*
 * 运行
 * Cli-web-server
 * php-fpm
 *
 * 标准
 * PHP-FIG => [PSR-0|1|2|4]
 *
 * 检查
 * php_codeSniffer | phpcs
 *
 * 待续 ...
 */

/*方法打印*/
function dd($v)
{
	echo "<pre>";
	var_dump($v) ;
	echo "<br />";
}

/*queue方法*/
function sendQueue($target,$queue)
{
	# 处理队列数据
	$queue->rewind(); //指向开始流
	//vaild(),有队列节点时才为真
	while($queue->valid()){
	    echo $queue->current(),"\n";
	    $queue->next(); //指向下一个
	}
	//isEmpty()判断为空
	/*while (!$queue->isEmpty()) {
		$sendData = $queue->shift(); //shift() is the same as dequeue() 
		echo $queue->current(),"\n";
    	$queue->next();
	}*/
}
/************面向对象编程****************************/
/*类于对象*/
$objClass = new PhpWay() ;
PhpWay::way();
/*继承*/
/*克隆*/

/*trait 单继承复用机制*/
$objClass->baseWay() ;

/***********************函数式编程******************/
/*匿名函数*/
$arrayNm = [1,2,3,4,5,6] ;
$funcNm = function($value='') {
	return ($value%2) ==0;
} ;
$outNm = array_filter($arrayNm,$funcNm) ;
dd($funcNm(2)); // 调用

/*闭包继承*/
$bb = 'bb' ;
$fucnBb = function() use ($bb){
	var_dump($bb,'use') ;
} ;
$fucnBb() ; // 调用


/*方法,函数调用*/
$dy = call_user_func('dd','2') ;
//静态方法调用(非静态也可以使用即不创建对象)
$dyClass1 = call_user_func(array('PhpWay','way')) ; 
$dyClass2 = call_user_func(array(new PhpWay(),'way2')) ; 
$dyClass3 = call_user_func_array((array(new PhpWay(),'way3')),array('1',2)) ; 


/*false*/
$check = [1,2,34,56];
dd($check[false]); //1
dd($check[true]); //2

/*************元编程(反射API和魔术方法)************************/
	//重载（overloading）是指动态地"创建"类属性和方法。
	//通过魔术方法（magic methods）来实现的。
	
	//暂时跳过
	// CLI模式
	// php --rf strlen
	// php --rc finfo
	// php --re json
	// php --ri dom
//创造函数引号中遵循php语法
$newFunc = create_function('$a,$b', 'return $a+$b;') ; 
dd($newFunc(2,3));

/*******PHP标准库(SPL)************/
//包含了常用的数据结构类 (堆栈，队列，堆等等)，
//以及遍历这些数据结构的迭代器，或者你可以自己实现 SPL 接口。
//数据结构 [双链表,堆,数组,映射,自行了解]
//	双链表 (DLL) 是一个链接到两个方向的节点列表。
				//当底层结构是 DLL 时, 迭代器的操作、对两端的访问、节点的添加或删除都具有 O (1) 的开销。
				//因此, 它为栈和队列提供了一个合适的实现。
				// SplDoublyLinkedList (父类)
					// SplStack(子类)
					// SplQueue(子类)
$queue= new SplQueue();
$queue->push(1);
$queue->push(2);
$queue->push(3);
$queue->pop() ;
dd($queue); // 当使用push和pop时,继承了父类,此时更像是堆栈,而不是队列
//子类方法
$queue2 = new SplQueue();
$queue2->enqueue(1) ;
$queue2->enqueue(2) ;
$queue2->enqueue(3) ;
$queue2->enqueue(8) ;
//$queue2->dequeue(); ///移除第一个
// sendQueue('127.0.0.1',$queue2) ;

//socket[框架使用]
// $address = gethostbyname('localhost:8001');
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "OK.\n";
}
$socketBind = socket_bind($socket,'127.0.0.1','8000');
$socketListen = socket_listen($socket, 5) ;
dd($socketListen);


/**********命令行接口*********************/
//以下测试命令在CLI模式下运行 具体参照官网说明
//注意指向流的开启
//运行项目          php -S localhost:8000
//启动时指定根目录   php -S localhost:8000 -t foo/
// php -a  shell编程 (linux)
// php -i  = phpinfo() 配置
// php -m 模块
// php -w 源码
// php -z 加载Zend扩展
// php -c 知道ini位置
// php test.php
// ./test.php  可以输出会有错误提示
// php -f test.php
// php -r 'echo"test";' ; 引号中遵循PHP语法
//
//
//

/**********Xdebug*********************/
header( "X-Test", "Testing" );
setcookie( "TestCookie", "test-value" );
/*for($i=1000000;$i>=0;$i--) {
    $m = $i+$i*$i ;
}*/
//具体使用请参照Xdebug官网
var_dump( xdebug_get_headers() ); //返回所以header信息,cookie,session等
var_dump( xdebug_is_enabled() ); //是否启用堆栈
var_dump( xdebug_memory_usage() ); //内存使用情况
var_dump( xdebug_peak_memory_usage() ); //峰值内存使用情况


/**********依赖管理***************/
//具体使用参看composer.txt
//Composer 与 Packagist
//PEAR


/**********设计模式***************/
//具体请参看designMode目录
//这里省略了PHP的语言用法,参照官网


/**********框架编码**************/
//自行了解
?>
<?php
/*********依赖注入***************/
//依赖注入是一种允许我们从硬编码的依赖中解耦出来，
//从而在运行时或者编译时能够修改的软件设计模式。
//粗暴解释:
//      创建对象的时候把一个或者多个对象或者资源作为参数传入对象体中
//      可以参照laravel和TP5
class Pend
{
    private $pend  ;
    public function __construct(Pend2 $intoPend)
    {
        $this->pend = $intoPend ;
        dd($this->pend);
    }
}
class Pend2
{

}
$pend = new Pend(new Pend2());

//控制反转|依赖反转准则是
//面向对象设计准则 S.O.L.I.D 中的 “D” ,倡导 “依赖于抽象而不是具体”。
//粗暴解释:依赖应该是接口/约定或者抽象类，而不是具体的实现。
class PendBack
{
    private $pend ;
    public function __construct(PendFace $intoPend)
    {
        $this->pend = $intoPend ;
        $this->pend->testFace();
    }
}
interface PendFace{
    public function testFace();
};
class PendBack2 implements PendFace{
    public function testFace()
    {
        echo 'PendBack2','<br >';
    }
};
$pendBase = new PendBack(new PendBack2());


/**********容器*****************/
//依赖注入容器和依赖注入不是相同的概念。
//容器是帮助我们更方便地实现依赖注入的工具，
//但是他们通常被误用来实现反模式设计：服务定位（Service Location）。
//把一个依赖注入容器作为服务定位器（ Service Locator）注入进类中隐式地建立了对于容器的依赖，
//而不是真正需要替换的依赖，而且还会让你的代码更不透明，最终变得更难测试。
//粗暴解释:不解释 -_-|| 框架应用比较多,请参照框架进行学习


/**********数据库(PDO)***************/
//跳过

/**********使用模板******************/
//原生模板
//Twig | Smarty

/**********错误与异常****************/
error_reporting();//设置错误等级
echo @$noData ;
//基本上你可以利用 ErrorException 类抛出「错误」来当做「异常」，这个类是继承自 Exception 类。

/********安全************************/
//web应用安全 以下测试请参数对应的测试文件
//网站参照:owasp,phpsecurity
//输入验证
//注入攻击
//跨站点脚本（XSS）
//传输层安全性不足（HTTPS，TLS和SSL）
//对于随机值而言熵不足
//PHP安全：默认的漏洞，安全漏洞和框架程序员？

//密码哈希 https://en.wikipedia.org/wiki/Cryptographic_hash_function
//加盐处理 https://en.wikipedia.org/wiki/Salt_(cryptography)

//数据过滤[过滤函数]
filter_var('bob@example.com', FILTER_VALIDATE_EMAIL) ;
dd(filter_input(INPUT_GET,'test')); //过滤Get请求参数
$request = fopen('php://input', 'r') ; //获取请求体,资源类型

/*********测试**************/
//测试驱动开发(TDD)
//行为驱动开发(DBB)

/********服务器于部署*********/
