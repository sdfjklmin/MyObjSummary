<?php 
# 单例模式
# 保证一个类仅有一个实例,并提供一个访问它的全局访问点
# 通过 new 出两个对象在进行判断
# 通过静态访问获取相同的对象
class Singleton 
{
	

	static private $_instance = null; 

	public function __construct() 
	{ 

	} 

	static public function getInstance() 
	{ 
		if(is_null(self::$_instance)) { 
		self::$_instance = new Singleton(); 
		} 
		return self::$_instance; 
	} 



	public function display($value='')
	{
		echo "this is sigleton display";
	}
}

# 测试

$t1 = new Singleton();
$t2 = new Singleton();
var_dump($t1 === $t2) ;

$t3 = Singleton::getInstance();
$t4 = Singleton::getInstance();
var_dump($t3 === $t4) ;


