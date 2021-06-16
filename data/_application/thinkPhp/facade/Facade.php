<?php


namespace app\thinkPhp\facade;

/**
 * Class Facade
 * @author sjm
 * @remark
 *  Facade 提供 getFacadeClass,让 子类 进行复写,实现真正的调用
 *  __callStatic : 当静态方法不存在时, 会触发此魔术方法
 *  call_user_func_array([类对象, 调用方法], 调用参数)
 */
class Facade
{
    /**
     * @return string class of namespace
     */
    protected static function getFacadeClass():string
    {
        return '';
    }

    /**
     * @param $name string staticAction
     * @param $arguments array params
     * @tip Method Class::__callStatic() must take exactly 2 arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $class = static::getFacadeClass();
        try{
            if(!class_exists($class)) {
                throw new \Exception('Class Not Find : '.$class);
            }
            //single class
            /*if(method_exists($class,'init')) {
                return call_user_func_array([$class::init(), $name], $arguments);
            }*/
            //call_user_func_array([类对象, 调用方法], 调用参数)
            return call_user_func_array([new $class(),$name],$arguments);
        }catch (\Exception $exception){
            echo $exception->getMessage(),PHP_EOL;exit();
        }

    }
}