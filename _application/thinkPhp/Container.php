<?php

namespace app\thinkPhp;
//++++++++++++++++++++++++++++++++++++++++++
// 容器单例
// static::instance() 提供当前容器的单例
// get()  容器静态方法,获取对应类的实例,内部调用make()
// make() 容器类方法,获取对应类的实例,如果有则返回,没有则新建
// 步骤 :
// static get() -获取对应的实例-> 获取容器单例 -> 调用容器类 make() 方法 -> 获取当前绑定的类(属性) -> 通过反射调用对应的类
//++++++++++++++++++++++++++++++++++++++++++

/**
 * Class Container
 * @author sjm
 * @package app\thinkPhp
 */
class Container
{
    /** 容器单例
     * @var Container
     */
    protected static $instance;

    /** 容器中的类
     * @var
     */
    protected $instances;

    /** 将基础类绑定到容器中
     * @var array
     */
    protected $bind = [
        'app' => App::class,
        'request' => Request::class,
    ];

    /** 获取当前容器的单例
     * @return Container
     */
    public static function instance()
    {
        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /** 获取 $name 的示例
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function get($name, $arguments = [])
    {
        return static::instance()->make($name, $arguments);
    }

    /** 创建实例,统一为单例
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function make($name, $arguments)
    {
        if(!isset($this->bind[$name])) {
            exit($name.'没有对应的指向');
        }
        if(isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        $class = $this->bind[$name];
        $this->instances[$name] = $this->invoke($class, $arguments);
        return $this->instances[$name];
    }

    /** 通过反射实例化类
     * @param $class
     * @param array $arguments
     * @return object
     */
    public function invoke($class, $arguments = [])
    {
        try {
            //Re f lec tion
            $reflect = new \ReflectionClass($class);
        }catch (\ReflectionException $exception) {
            echo "ReflectionClass Error ".$exception->getMessage();exit();
        }
        //不支持只定义魔术方法
        /*if ($reflect->hasMethod('__make')) {
            $method = new \ReflectionMethod($class, '__make');
            if ($method->isPublic() && $method->isStatic()) {
                $args = $this->bindParams($method, $vars);
                return $method->invokeArgs(null, $args);
            }
        }*/
        //获取 $class 是否有 __construct
        $constructor = $reflect->getConstructor();
        $args = $constructor ? $arguments : [];
        //获取 $class 实例
        return $reflect->newInstanceArgs($args);
    }


    /** 通过反射调用类方法
     * @deprecated 弃用,有点画蛇添足了
     * @param $class
     * @param $action
     * @param array $arguments
     * @param array $actionArg
     * @return mixed
     */
    public function invokeAction($class, $action, $arguments = [], $actionArg = [])
    {
        try {
            //Re f lec tion
            $reflect = new \ReflectionClass($class);
        }catch (\ReflectionException $exception) {
            echo "ReflectionClass Error ".$exception->getMessage();exit();
        }
        //不支持自定义魔术方法
        //获取 $class 是否有 __construct
        $constructor = $reflect->getConstructor();
        $args = $constructor ? $arguments : [];
        //获取 $class 实例
        $instanceClass = $reflect->newInstanceArgs($args);
        return call_user_func_array([$instanceClass,$action],$actionArg);
    }
}