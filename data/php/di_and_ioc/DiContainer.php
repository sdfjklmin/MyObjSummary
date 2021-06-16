<?php

define('DI_EOL',"\n");

class DiContainerA
{

    public function run()
    {
        echo 'A run ...',DI_EOL;
    }
}

class DiContainerB
{
    /**
     * @var DiContainerA
     */
    private $diContainer;

    /** 只限制于Interface
     * DiContainerB constructor.
     * @param DiContainerA $diContainer
     */
    public function __construct(DiContainerA $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function run()
    {
        $this->diContainer->run();
        echo 'B run ...',DI_EOL;
    }
}

class DiContainerC
{
    /**
     * @var DiContainerB
     */
    private $diContainer;

    /**限制于DiContainerB
     * DiContainerC constructor.
     * @param DiContainerB $diContainer
     */
    public function __construct(DiContainerB $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function run()
    {
        $this->diContainer->run();
        echo 'C run ...',DI_EOL;
    }
}

class DiContainer
{
    private $attr = [];

    private static $_self = null;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __set($name, $value)
    {
       $this->attr[$name] = $value;
    }

    public static function init()
    {
        if(!self::$_self) {
            self::$_self = new self();
        }
        return self::$_self;
    }

    /** 自动绑定（Autowiring）
     *  自动解析（Automatic Resolution）
     * @param $name
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function __get($name)
    {
       return $this->build($this->attr[$name]);
    }


    /**
     * @param $class
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function build($class)
    {
        //若为匿名函数，则执行结果
        if($class instanceof \Closure) {
            return $class($this);
        }

        /** @var \ReflectionClass $reflector  反射类*/
        $reflector = new \ReflectionClass($class);

        //检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if(!$reflector->isInstantiable()) {
            throw new \Exception("Can't instantiate this.");
        }

        /** @var \ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        //若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $class;
        }

        //取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();

        //递归解析构造函数的参数
        $dependencies = $this->getDependencies($parameters);

        //创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];

        /** @var \ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            /** @var \ReflectionClass $dependency */
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
     * @param \ReflectionParameter $parameter
     * @return mixed
     * @throws \Exception
     */
    public function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \Exception('I have no idea what to do here.');
    }
}

//Test One ...
$container = DiContainer::init();

//build DiContainerA - C
$container->A = 'DiContainerA';
$container->B =  new DiContainerB($container->A);
$container->C = function ($container)
{
    return new DiContainerC($container->B);
};

//get DiContainerC
$c = $container->C;
$c->run();
//A run ...
//B run ...
//C run ...

$a = $container->A;
$a->run();
//A run ...

$container->B->run();

class OtherB
{
    public function run()
    {
        echo 'OtherB run ...',PHP_EOL;
    }
}
$container->B = 'OtherB';
$container->B->run();

