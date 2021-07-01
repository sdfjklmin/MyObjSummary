<?php

namespace Ioc;

use ReflectionException;

//------------------------------------
// 1. 依赖注入、控制反转、容器注入、服务提供
//------------------------------------
interface Log
{
    public function write();
}

class FileLog implements Log
{

    public function write()
    {
        echo "FileLog write ...","\n";
    }
}

class DatabaseLog implements Log
{

    public function write()
    {
        echo "DatabaseLog write ...","\n";
    }
}


//-----------------------------------------

interface User
{
    public function login();
}


class TestUser implements User
{
    /**
     * @var FileLog
     */
    protected $log;

    public function __construct(FileLog $log)
    {
        //依赖于外部资源的注入来实现某些业务
        //外部资源可以是具体的调用、也可以是容器的绑定
        $this->log = $log;
    }

    public function login()
    {
        echo "login success","\n";

        $this->log->write();
    }
}

class TestUserIoc implements User
{
    /**
     * @var Log
     */
    protected $log;

    public function __construct(Log $log)
    {
        //依赖的是抽象，而不是具体的实体，让具体的调用关系反转到调用链的起点。
        //外部资源可以是具体的调用、也可以是容器的绑定
        $this->log = $log;
    }

    public function login()
    {
        echo "ioc login success","\n";

        $this->log->write();
    }
}

//--------------------------------------------

/** 服务容器
 * Class Container
 * @package Ioc
 */
class Container
{
    /** 绑定的抽象与实体
     * @var array
     */
    protected $binding;

    public function bind($abstract, $concrete)
    {
        //这里有三种实现方式，选取 3 。

        //1.这里绑定的是抽象与实体的对应关系
        //$this->binding[$abstract]['concrete'] = $concrete;

        //2.这里绑定的是抽象与实体类的对应关系
        //$this->binding[$abstract]['concrete'] = new $concrete();

        //3.这里绑定的是抽象与实体类的匿名函数，因为绑定还不需要创建，真正的构建通过 make 进行，这里只绑定关系。
        $this->binding[$abstract]['concrete'] = function ($ioc) use ($concrete) {
            return $ioc->build($concrete);
        };

        //4.如果不需要依赖关系去获取外部资源时可以直接创建
        //$this->binding[$abstract]['concrete'] = new Test($a, $b, $c);
    }

    /** 获取抽象对应的实体
     * @param $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        $concrete = $this->binding[$abstract]['concrete'];
        return $concrete($this);
    }


    /** 通过反射创建对象
     * @param $concrete
     * @return mixed
     * @throws ReflectionException
     */
    public function build($concrete) {
        $reflector = new \ReflectionClass($concrete);
        $constructor = $reflector->getConstructor();
        if(is_null($constructor)) {
            return $reflector->newInstance();
        }else {
            $dependencies = $constructor->getParameters();
            $instances = $this->getDependencies($dependencies);
            return $reflector->newInstanceArgs($instances);
        }
    }

    /** 获取参数的依赖
     * @param $params
     * @return array
     */
    protected function getDependencies($params): array
    {
        $dependencies = [];
        foreach ($params as $param) {
            $dependencies[] = $this->make($param->getClass()->name);
        }
        return $dependencies;
    }
}

/*全局运行体*/
$container = new Container();

/*这里的 bind 需要根据依赖关系来进行绑定*/

/**
 * 示例一
 * TestUser 基于 文件日志
 */
$container->bind(FileLog::class, FileLog::class);
$container->bind(User::class, TestUser::class);
$user = $container->make(User::class);
$user->login();

echo '-------------',"\n";

/**
 * 示例二
 * TestUserIoc 基于 日志接口(文件日志、数据库日志)
 */
$container->bind(Log::class, DatabaseLog::class);
$container->bind(User::class, TestUserIoc::class);
$user = $container->make(User::class);
$user->login();

//----------------------------------------------
//  2. Contracts 契约编程: 契约就是所谓的面向接口编程
//----------------------------------------------
// 这里的接口指的是 interface Log 、interface User
// 基于接口编程，从而更好的解耦程序依赖，方便扩展。
// 对比 示例一、示例二 中 日志的绑定。


//--------------------------------------------
// 3. Facades 的实现原理
//--------------------------------------------
// 通过魔术方法

/**
 * Class UserFacade
 * @package Ioc
 * @method static login()
 */
class UserFacade
{
    /**
     * @var Container
     */
    protected static $ioc;

    /**
     * @param $ioc
     */
    public static function setFacadeIoc($ioc)
    {
        static::$ioc = $ioc;
    }
    
    /**
     * @return string
     */
    protected static function getFacadeClass(): string
    {
        return User::class;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        //这里的实现方式有很多，是一个入口，具体怎么实现看项目。

        //基于容器
        $instance = static::$ioc->make(static::getFacadeClass());
        return $instance->$name(...$arguments);

        //基于单类
        //call_user_func_array([new $class(),$name],$arguments);
    }
}
UserFacade::setFacadeIoc($container);

UserFacade::login();


//--------------------------------------------
// 4. Laravel 之中间件
//--------------------------------------------
// 主要提供一系列的验证规则
// 官方实现 middleware 主要通过 call_user_func 和 array_reduce
// 通过 array_reduce 将所有中间件变成匿名体，再通过 call_user_func 进行调用

interface Middleware
{
    public static function handle(\Closure $next);
}

class VerifyToken implements Middleware
{
    public static function handle(\Closure $next)
    {
        //先执行逻辑，再执行匿名函数
        echo "验证Token","\n";
        $next();
    }
}

class VerifyUser implements Middleware
{

    public static function handle(\Closure $next)
    {
        //先执行逻辑，再执行匿名函数
        echo "验证用户","\n";
        $next();
    }
}

class VerifyLogin implements Middleware
{

    public static function handle(\Closure $next)
    {
        //先执行匿名函数，再执行逻辑
        $next();
        echo "验证登录","\n";
    }
}

//输出: 验证用户 - 验证Token - 我就看看你怎么运行 - 验证登录

/*VerifyLogin::handle(function () {
   VerifyUser::handle(function () {
       VerifyToken::handle(function () {
           echo "我就看看你怎么运行","\n";
       });
   });
});*/

//通过 array_reduce 和 call_user_func 进行优化

$pipe_arr = [
    VerifyToken::class,
    VerifyUser::class,
    VerifyLogin::class,
];
$handle = function () {
    echo "我再试试呢","\n";
};

function testUse($carry, $item): \Closure
{
    return function() use($carry,$item){
        return $item::handle($carry);
    };
}

/* array_reduce 说明
array
    输入的 array。

callback
    callback(mixed $carry, mixed $item): mixed

    carry
    携带上次迭代的返回值； 如果本次迭代是第一次，那么这个值是 initial。

    item
    携带了本次迭代的值。

    initial
    如果指定了可选参数 initial，该参数将用作处理开始时的初始值，如果数组为空，则会作为最终结果返回。
 */
$callback = array_reduce($pipe_arr, 'Ioc\testUse',$handle);

call_user_func($callback);
//单独调用
//var_dump(call_user_func_array(array(VerifyLogin::class, 'handle'),array($deal)));


//-----------------------------------
// 5. laravel 生命周期
//-----------------------------------
// 详情请查看入口解析


echo "6. Laravel事件之观察者模式","\n";
//----------------------------------
// 6. Laravel事件之观察者模式
//----------------------------------
//观察者模式 (Observer), 当一个对象的状态发生改变时，依赖他的对象会全部收到通知，并自动更新。
// 场景：一个事件发生后，要执行一连串更新操作。
//      传统的编程方式，就是在事件的代码之后直接加入处理逻辑，当更新的逻辑增多之后，代码会变得难以维护。
//      这种方式是耦合的，侵入式的，增加新的逻辑需要改变事件主题的代码。
// 观察者模式实现了低耦合，非侵入式的通知与更新机制

function updateLogic()
{
    //改变1
    //...
    //改变2
    //...
    //改变3
    //...
    //改变4
}

interface Observer
{
    public function update();
}

class Observer1 implements Observer
{

    public function update()
    {
        echo "我是 观察者1 ，执行完毕","\n";
    }
}

class Observer2 implements Observer
{

    public function update()
    {
        echo "我是 观察者2 ，执行完毕","\n";
    }
}

/**
 * Class ObserverEvent
 * @package Ioc
 * @remark 我是为观测者提供的事件处理
 *  我不关心具体的业务，基于我自身职责我会提供:
 *  新增事件、事件通知、事件执行、移除事件、清空事件
 */
class ObserverEvent
{
    /**
     * @var array
     */
    protected $observer = [];

    public function add(Observer $observer)
    {
        $this->observer[] = $observer;
    }

    public function notify()
    {
        foreach ($this->observer as $observer) {
            /** @var Observer $observer */
            $observer->update();
        }
    }

    public function trigger()
    {
        $this->notify();
    }

}

/**
 * Class ObserverServiceEvent
 * @package Ioc
 * @remark 事件调用入口和业务逻辑之后的处理相绑定
 */
class ObserverServiceEvent
{
    public function __construct()
    {
        //触发业务事件操作，这里的 add 可以通过配置进行
        //可以参考框架定义的事件
        $event = new ObserverEvent();
        $event->add(new Observer1());
        $event->add(new Observer2());
        $event->trigger();
    }
}

class ObserverService
{
    public function demo()
    {
        //处理当前的业务逻辑
        //...
        echo "我是观察者服务中的逻辑处理","\n";
        //触发业务逻辑之后的事件
        new ObserverServiceEvent();
    }
}
//测试
$service = new ObserverService();
$service->demo();

// 接口 - 定义观测者需要执行的入口
// 观察者 - 实现接口定义、自身逻辑
// 观察者事件 - 为观察者提供事件的基础操作(新增、触发、通知)
// 观察者服务事件 - 事件调用入口(事件逻辑、新增事件、触发事件)
// 服务 - 提供业务处理、调用服务事件


echo "7. Eloquent ORM 中的 find","\n";
//---------------------------------------------
// 7. Eloquent ORM 中的 find
//---------------------------------------------
// Article::find (1); 发现没有 find 方法就会调用 Model 的__callStatic
// callStatic 方法又回去调用 call 方法，这时发现有 find 方法
// find 方法会调用 where 拼装要查询的参数，然后调用 first ()
// 因为 first () 只需要取 1 条，所以设置 $limit 1
// 最后组装 sql
// 交给 mysql 执行 返回结果。
// laravel 中封装的比这个要复杂的多，这个只是让大家明白 ORM 简单的一个 find () 是怎么编写的
