<?php
//--------------------------------
// PHP 将所有以 __（两个下划线）开头的类方法保留为魔术方法。
// 所以在定义类方法时，除了上述魔术方法，建议不要以 __ 为前缀。
//--------------------------------
/**
 * Class magicFunc
 * @author sjm
 * @remark
 */
class MagicFunc
{
	/** 每次创建新对象时先调用此方法
	 * MagicFunc constructor.
	 */
	public function __construct()
	{
		echo 'init';
	}

	/** 等同于 __construct
	 * 如果 PHP 5 在类中找不到 __construct() 函数并且也没有从父类继承一个的话，它就会尝试寻找旧式的构造函数，也就是和类同名的函数.
	 * 优先执行 __construct
	 * MagicFunc constructor.
	 */
	/*public function MagicFunc()
	{
		echo '11';
	}*/

	/**
	 * 析构函数会在到某个对象的所有引用都被删除或者当对象被显式销毁时执行
	 * 和构造函数一样,调用父类需要 parent::__destruct
	 * 析构函数即使在使用 exit() 终止脚本运行时也会被调用。
	 * 在析构函数中调用 exit() 将会中止其余关闭操作的运行。
	 */
	/*function __destruct()
	{
		print "Destroying " . '32' . "\n";
	}*/

	/**
	 * @inheritDoc 不能在 __toString() 方法中抛出异常,这么做会导致致命错误
	 * @uses echo (new MagicFunc());
	 * @return string
	 *
	 */
	public function __toString()
	{
		return 'this is __toString';
	}

	/**
	 * @inheritDoc 当尝试以调用函数的方式调用一个对象时，__invoke() 方法会被自动调用。
	 * @uses $model = new MagicFunc(); $model();
	 */
	public function __invoke()
	{
		echo 'function use';
	}

	//------------------------
	// 重载(overloading)
	//------------------------
	/** 在对象中调用一个不可访问方法时，__call() 会被调用。
	 *  $name 参数是要调用的方法名称。$arguments 参数是一个枚举数组，包含着要传递给方法 $name 的参数。
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call ( string $name , array $arguments )
	{
		var_dump($name,$arguments);
	}

	/** 在静态上下文中调用一个不可访问方法时，__callStatic() 会被调用。
	 *  $name 参数是要调用的方法名称。$arguments 参数是一个枚举数组，包含着要传递给方法 $name 的参数。
	 * @param string $name
	 * @param array $arguments
	 */
	public static function __callStatic ( string $name , array $arguments )
	{
		var_dump($name,$arguments);
	}

	/** 设置 当前类不存在的属性(public)时
	 * @param $name
	 * @param $value
	 */
	public function __set ($name,$value )
	{
		var_dump($name,$value);
	}

	/** 获取 不存在的属性
	 * @param $name
	 */
	public function __get($name)
	{
		var_dump($name);
	}

	/** 不存在时,调用 isset($$model->name)时,触发
	 * @param $name
	 */
	public function __isset($name)
	{
		var_dump($name,'isset');
	}

	//同 __isset()
    public function __unset ( string $name )
	{

	}

	/**
	 *  克隆
	 */
	public function __clone()
	{
	  echo 'clone';
	}
}
//__toString
/*echo (new MagicFunc());*/

//__invoke(调用)
/*$model = new MagicFunc();
$model();*/

$model = new MagicFunc();
//$a = isset($model->a);
//MagicFunc::abc(['a']);
$t = clone  $model;