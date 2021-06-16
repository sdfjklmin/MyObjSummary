#### 门面 [facade] : 提供了一个静态调用接口
* 文件路径 : sinkmin\lib\facade

* 实现逻辑

      Facade : 中提供了 getFacadeClass()方法，让继承类进行覆写并反真正处理类的命名空间地址。
      Facade : 通过 __callStatic 和 getFacadeClass() 进行命名空间的方法调用。
      __callStatic : 静态方法不存在时触发，对应的方法前缀不能使用public，public能够找到。
      Facade : 使用call_user_func_array([类对象, 调用方法], 调用参数)进行真正调用。
      注意点 : 
            self::getFacadeClass();     self指向当前类
            static::getFacadeClass();   static指向的是实际调用时的对象所属的类
      继承类注释 : 
            /** xx门面类 (方法更具自身进行开放)
            * @method mixed name($params,...) static 备注
            */           
 ```php
/**
 * Class AppFacade
 * @method run() static
 * @method getApp() static
 * @package sinkmin\lib\facade
 */
class App extends Facade
{
    protected static function getFacadeClass()
    {
        //实际指向的类
        return 'sinkmin\\lib\\App';
    }
}

class Facade
{
    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
     protected static function getFacadeClass()
     {

     }


    /** 静态方法不存在时触发，对应的方法前缀不能使用public，public能够找到
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        //self::getFacadeClass();当前的getFacadeClass
        //static::getFacadeClass();子类的getFacadeClass()
        $class = static::getFacadeClass();
        if(!class_exists($class)) {
            exit('类不存在：'.$class);
        }
        //更具自身设计进行处理
        if(method_exists($class,'init')) {
            return call_user_func_array([$class::init(), $name], $arguments);
        }else{
            return call_user_func_array([new $class(),$name],$arguments);
        }
    }

}
```
 
 
            
#### 视图
* 通过继承基础控制器进行模板渲染
* 基础控制器 -> 视图类 -> 视图处理类 -> 模板解析类 (会有很多复杂的处理)      

#### 实例化对象
通过反射类进行操作
```php
$class  = 'app\index\Test';
$action = 'tt';
try {
    $reflect = new \ReflectionClass($class);
    /*if ($reflect->hasMethod('__make')) {
        $method = new \ReflectionMethod($class, '__make');

        if ($method->isPublic() && $method->isStatic()) {
            $args = $this->bindParams($method, $vars);
            return $method->invokeArgs(null, $args);
        }
    }*/

    $constructor = $reflect->getConstructor();

    //$args = $constructor ? $this->bindParams($constructor, $vars) : [];
    $args = $vars;

    $classModel = $reflect->newInstanceArgs($args);

} catch (\ReflectionException $e) {
    throw new \Exception('Class Not Exists : ' . $this->_controller);
}
$data = $classModel->{$action};
```      