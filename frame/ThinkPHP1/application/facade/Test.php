<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:24
 */
namespace app\facade ;
use think\Facade;

class Test extends Facade
{
    /*获取当前Facade对应类名
     *   等同如下
     *   (调用)动态绑定类
     *   Facade::bind('app\facade\Test', 'app\common\Test');
     *   echo Test::hello('thinkphp');
     */
    protected static function getFacadeClass()
    {
       return 'app\common\Test' ;
    }
}