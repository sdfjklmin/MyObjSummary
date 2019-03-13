<?php

//title:依赖注入容器

echo "<pre>" ;
// 依赖注入

// 传统的思路是应用程序用到一个Foo类 就会创建Foo类并调用Foo类的方法
// 假如这个方法内需要一个Bar类 就会创建Bar类并调用Bar类的方法
// 而这个方法内需要一个Bim类 就会创建Bim类 接着做些其它工作。

/**
*  Foo
*/
class Foo 
{

	public function doSomething($value='')
	{
		# code...
		$Bar = new Bar();
		$Bar->doSomething();
		echo 'Foo doSomething','<br />' ;
	}
}

/**
*  Bar
*/
class Bar 
{
	

	public function doSomething($value='')
	{
		# code...
		$Bim = new Bim();
		$Bim->doSomething();
		echo 'Bar doSomething','<br />' ;
	}
}

/**
* Bim
*/
class Bim
{
	

	public function doSomething($value='')
	{
		# code...
		echo 'Bim doSomething','<br />'  ;
	}
}

$doSomething = new Foo();
$doSomething->doSomething();

echo "-------------------------------------------------","<br />";

// 使用依赖注入的思路是应用程序用到Foo类，Foo类需要Bar类，
// Bar类需要Bim类，那么先创建Bim类，再创建Bar类并把Bim注入，再创建Foo类，并把Bar类注入，
// 再调用Foo方法，Foo调用Bar方法，接着做些其它工作。

/**
* Bim2
*/
class Bim2 
{
	
	public function doSomething($value='')
	{
		# code...
		echo 'Bim2 doSomething','<br />' ;
	}

}

/**
*  Bar2
*/
class Bar2
{

	private $bim2 ;

	public function __construct(Bim2 $bim2)
	{
		# code...
		$this->bim2 = $bim2 ;
	}

	public function doSomething($value='')
	{
		# code...
		$this->bim2->doSomething();
		echo "Bar2 doSomething",'<br />';
	}
}

/**
* Foo
*/
class Foo2
{
	
	private $bar2 ;
	public function __construct(Bar2 $bar2)
	{
		# code...
		$this->bar2 = $bar2 ;
	}

	public function doSomething($value='')
	{
		# code...
		$this->bar2->doSomething();
		echo "Foo2 doSomething",'<br />';
	}
}

$doSomething2 = new Foo2(new Bar2(new Bim2()));
$doSomething2->doSomething();


// 这就是控制反转模式。依赖关系的控制反转到调用链的起点。这样你可以完全控制依赖关系，通过调整不同的注入对象，来控制程序的行为。例如Foo类用到了memcache，可以在不修改Foo类代码的情况下，改用redis。

// 使用依赖注入容器后的思路是应用程序需要到Foo类，就从容器内取得Foo类，容器创建Bim类，再创建Bar类并把Bim注入，再创建Foo类，并把Bar注入，应用程序调用Foo方法，Foo调用Bar方法，接着做些其它工作.

// 总之容器负责实例化，注入依赖，处理依赖关系等工作。
echo "-------------------------------------------------","<br />";

/**
*  Test
*/
class Test 
{
	public function __construct($value='')
	{
		# code...
	}
	public function test($value='')
	{
		# code...
		echo "Test doSomething","<br />" ;
	}
}

/**
*  Use
*/
class Base 
{
	private $baseC ;
	private $baseA ;
	public  function __construct($baseC = '' ,$baseA = '')
	{
		if(!$baseA) {

			echo 'NoA' ;
			exit();
		}
		if(!$baseC) {
			echo 'NoC' ;
			exit();
		}
		# code...
		if ($baseC && $baseA) {
			# code...
			$this->baseC = $baseC;
			$this->baseA = $baseA;
		}else{
			echo "参数不足" ;
			return false ;
		}
		
	}

	public function doSomething($value='')
	{
		# code...
		$a = $this->baseA;
		$this->baseC->$a();
		echo "Use doSomething" ;
	}
}

// $use = new Base(new Test(),'test');
// $use->doSomething();
echo "<br />","-------------------------------------------------","<br />";



// 容器类
/**
*   这段代码使用了魔术方法，
*   在给不可访问属性赋值时，__set() 会被调用。
*   读取不可访问属性的值时，__get() 会被调用。
*/
class Container
{
	private $a = [] ;
	public $test = 1 ;
	function __set($k,$v)
	{
		# code...
		// var_dump($k);
		// var_dump($v);exit();
		$this->a[$k] = $v ;
	}

	function __get($k)
	{
		# code...
		return $this->a[$k]($this);
	}
}
$c = new Container();
$c->bim = function(){
	return new Bim2();
};
$c->bar = function ($c) {
    return new Bar2($c->bim);
};
$c->foo = function ($c) {
    return new Foo2($c->bar);
};
// 从容器中取得Foo
$foo = $c->foo;
$foo->doSomething();

echo "<br />","-------------------------------------------------","<br />";

// 后期静态绑定
class IoC
{
    protected static $registry = [];

    public static function bind($name, Callable $resolver)
    {
        static::$registry[$name] = $resolver;
    }

    public static function make($name)
    {
        if (isset(static::$registry[$name])) {
            $resolver = static::$registry[$name];
            return $resolver();
        }
        throw new Exception('Alias does not exist in the IoC registry.');
    }
}
    
    IoC::bind('bim2', function () {
        return new Bim2();
    });
    IoC::bind('bar2', function () {
        return new Bar2(IoC::make('bim2'));
    });
    IoC::bind('foo2', function () {
        return new Foo2(IoC::make('bar2'));
    });
    
    
    // 从容器中取得Foo
    $foo = IoC::make('foo2');
    $foo->doSomething(); 

echo "<br />","-------------------------------------------------","<br />";

 // 依赖注入容器
 // 真实的dependency injection container会提供更多的特性，如
// 自动绑定（Autowiring）或 自动解析（Automatic Resolution）
// 注释解析器（Annotations）
// 延迟注入（Lazy injection）


    class Bim
    {
        public function doSomething()
        {
            echo __METHOD__, '|';
        }
    }
    
    class Bar
    {
        private $bim;
    
        public function __construct(Bim $bim)
        {
            $this->bim = $bim;
        }
    
        public function doSomething()
        {
            $this->bim->doSomething();
            echo __METHOD__, '|';
        }
    }
    
    class Foo
    {
        private $bar;
    
        public function __construct(Bar $bar)
        {
            $this->bar = $bar;
        }
    
        public function doSomething()
        {
            $this->bar->doSomething();
            echo __METHOD__;
        }
    }
    
    class Container
    {
        private $s = array();
    
        public function __set($k, $c)
        {
            $this->s[$k] = $c;
        }
    
        public function __get($k)
        {
            // return $this->s[$k]($this);
            return $this->build($this->s[$k]);
        }
    
        /**
         * 自动绑定（Autowiring）自动解析（Automatic Resolution）
         *
         * @param string $className
         * @return object
         * @throws Exception
         */
        public function build($className)
        {
            // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
            if ($className instanceof Closure) {
                // 执行闭包函数，并将结果
                return $className($this);
            }
    
            /** @var ReflectionClass $reflector */
            $reflector = new ReflectionClass($className); // 反射类
    
            // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
            if (!$reflector->isInstantiable()) {
                throw new Exception("Can't instantiate this.");
            }
    
            /** @var ReflectionMethod $constructor 获取类的构造函数 */
            $constructor = $reflector->getConstructor();
    
            // 若无构造函数，直接实例化并返回
            if (is_null($constructor)) {
                return new $className;
            }
    
            // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
            $parameters = $constructor->getParameters();
    
            // 递归解析构造函数的参数
            $dependencies = $this->getDependencies($parameters);
    
            // 创建一个类的新实例，给出的参数将传递到类的构造函数。
            return $reflector->newInstanceArgs($dependencies);
        }
    
        /**
         * @param array $parameters
         * @return array
         * @throws Exception
         */
        public function getDependencies($parameters)
        {
            $dependencies = [];
    
            /** @var ReflectionParameter $parameter */
            foreach ($parameters as $parameter) {
                /** @var ReflectionClass $dependency */
                $dependency = $parameter->getClass();
    
                if (is_null($dependency)) {
                    // 是变量,有默认值则设置默认值
                    $dependencies[] = $this->resolveNonClass($parameter);
                } else {
                    // 是一个类，递归解析
                    $dependencies[] = $this->build($dependency->name);
                }
            }
    
            return $dependencies;
        }
    
        /**
         * @param ReflectionParameter $parameter
         * @return mixed
         * @throws Exception
         */
        public function resolveNonClass($parameter)
        {
            // 有默认值则返回默认值
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
    
            throw new Exception('I have no idea what to do here.');
        }
    }
    
    // ----
    $c = new Container();
    $c->bar = 'Bar';
    $c->foo = function ($c) {
        return new Foo($c->bar);
    };
    // 从容器中取得Foo
    $foo = $c->foo;
    $foo->doSomething(); // Bim::doSomething|Bar::doSomething|Foo::doSomething
    
    // ----
    $di = new Container();
    
    $di->foo = 'Foo';
    
    /** @var Foo $foo */
    $foo = $di->foo;
    
    var_dump($foo);
    /*
    Foo#10 (1) {
      private $bar =>
      class Bar#14 (1) {
        private $bim =>
        class Bim#16 (0) {
        }
      }
    }
    */
    
    $foo->doSomething(); 
