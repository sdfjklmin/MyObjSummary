<?php

namespace php\diIoc;

class ContainerFoo
{

    /**
     * @var ContainerBim
     */
    protected $bim ;

    /**
     * ContainerFoo constructor.
     * @param ContainerBim $bim
     */
    public function __construct(ContainerBim $bim)
    {
        $this->bim = $bim;
    }

    public function doSomething()
    {
        $this->bim->doSomething();
        echo 'foo do something',"\n";
    }
}

class ContainerBim
{
    public function doSomething()
    {
        echo 'bim do something',"\n";
    }
}

/**
 * Class Container
 * @author sjm
 * @package php\diIoc
 */
class Container
{
    //+++++++++++++++++++++++++++++
    // 这段代码使用了魔术方法，
    // 在给不可访问属性赋值时，__set() 会被调用。
    // 读取不可访问属性的值时，__get() 会被调用。
    //+++++++++++++++++++++++++++++

    private $attr = [] ;


    /**
     * @param $attribute
     * @param $object
     */
    function __set($attribute, $object)
    {
        $this->attr[$attribute] = $object ;
    }

    /**
     * @param $k
     * @return mixed
     */
    function __get($k)
    {
        # code...
        return $this->attr[$k]($this);
    }
}

//容器
$c = new Container();
$Closure = function(){
    return new ContainerBim();
};
// object(Closure)  闭合对象
// Closure 用于代表 匿名函数 的类.主要限制类的实例化，对象和类的作用域。

$c->bim = function(){
    return new ContainerBim();
};
// object(Closure)       闭合对象 => 对应 __get 使用 $this->attr[$k]($this)
// object(ContainerBim)  实体对象 => 对应 __get 使用 $this->attr[$k]

//foo 依赖如 bim
$c->foo = function ($c) {
    return new ContainerFoo($c->bim);
};
// 从容器中取得Foo
$foo = $c->foo;

$foo->doSomething();


/** 容器静态绑定
 * Class ContainerIoc
 * @author sjm
 * @package php\diIoc
 */
class ContainerIoc
{
    protected static $register = [];

    public static function bind($name,$value)
    {
        self::$register[$name] = $value;

    }

    public static function make($name)
    {
        if(isset(self::$register[$name])) {
            //匿名对象
            $reg = self::$register[$name];
            //$reg(),转换为之前的实例对象
            return $reg();
        }
    }
}
ContainerIoc::bind('bim',function () {
    return new ContainerBim();
});

ContainerIoc::bind('foo',function () {
   return new ContainerFoo(ContainerIoc::make('bim'));
});

//从容器中取得Foo
$foo = ContainerIoc::make('foo');
$foo->doSomething();
