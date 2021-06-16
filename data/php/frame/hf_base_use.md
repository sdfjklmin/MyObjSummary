# 前言

一般来说，框架提供 自动 和 手动 两种方式使用。

自动: 通过 `注解 需要注解类的命名空间`， `注入 指定依赖`

手动: 修改配置文件。

注释规范: 严格按照标准注解，不支持自定义的名称。如类注释有 @remark 等，否则会报错。

# 路由
配置:
    config/routes.php 
    
```angular2
use Hyperf\HttpServer\Router\Router;

//多相应路由
Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');
//Router::addRoute(['GET', 'POST','PUT','DELETE'], $uri, $callback);

//闭包路由
Router::get('/hello-hyperf', function () {
    return 'Hello Hyperf.';
});

//单一路由,下面三种方式的任意一种都可以达到同样的效果
//Router::get('/hello-hyperf', 'App\Controller\IndexController::hello');
Router::get('/test', 'App\Controller\IndexController@test');
//Router::get('/hello-hyperf', [App\Controller\IndexController::class, 'hello']);
//其它响应
/*Router::get($uri, $callback);
Router::post($uri, $callback);
Router::put($uri, $callback);
Router::patch($uri, $callback);
Router::delete($uri, $callback);
Router::head($uri, $callback);*/

//路由组
Router::addGroup('/user/',function (){
    // url ->  /user/index
    Router::get('index','App\Controller\UserController@index');
});

//路由参数，必填
Router::get('/tt/{id}', 'App\Controller\IndexController@tt');

//路由参数，可选
Router::get('/tt2/[{id}]', 'App\Controller\IndexController@tt2');
//访问地址 http://127.0.0.1:9501/tt2/

//匹配id为数字,支持正则
Router::get('/user/{id:\d+}','App\Controller\IndexController@tt');


//一般来说 Router::method($a,$b,$c,array $option = [])
// $option 可以传入你想要的操作，如 中间件
Router::get('/tt/{id}', 'App\Controller\IndexController@tt', ['middleware' => [FooMiddleware::class]]);

```

注解:

@AutoController 注解
~~~
@AutoController 为绝大多数简单的访问场景提供路由绑定支持，
使用 @AutoController 时则 Hyperf 会自动解析所在类的所有 public 方法并提供 GET 和 POST 两种请求方式。

使用 @AutoController 注解时需 use Hyperf\HttpServer\Annotation\AutoController; 命名空间；
~~~

@Controller 注解 
~~~
详情请查看文档
~~~

# 中间件
原理
~~~
Requst -> middleware -> Response
        
middleware为洋葱模型，一层一层的进行，中间的为核心。

Requst -> m1L -> m2L -> m3 -> m2R -> m1R -> Response

具体使用请查看官方文档，相对简单。
~~~


# 微服务
服务中心
~~~
Server Center : 每个服务的地址，IP，接口信息，内容监测，配置等信息。
Server A 调用 Server B
A  -S(获取B的地址，认证，可用接口等)-> B(验证信息，返回参数) 
~~~

服务熔断和降级

服务限流

配置中心

# 协程
协程基础概念(可以很好的解决异步非阻塞系统的开发问题)
~~~
进程 ： 系统进行资源分配和调度的基本单位 （pcntl_fork）（可通过 缓存、数据库、共享内存进行数据处理）
       一个执行中的程序 ， 一个进程中至少有一个执行的流程（主线程），也可以开启新的执行流程（线程）
线程 ： 操作系统能够进行运算调度的最小单位
       多个执行流程 ， 一个线程可以执行多个协程
       普通线程是抢占式的，哪个线程能得到资源由操作系统决定
协程 ： 用户态完成程序的调度，像系统调度进程和线程一样
       协程是协作式的，执行权由用户态自行分配
       比线程更加轻量级 ，完全被程序代码所调度和掌控， 不用操作系统介入
       swoole解释 : 协程是一种轻量级的线程，由用户代码来调度和管理，而不是由操作系统内核来进行调度，也就是在用户态进行。
                   可以直接的理解为就是一个非标准的线程实现，但什么时候切换由用户自己来实现，而不是由操作系统分配 CPU 时间决定。
       使用setjmp和longjmp跳转来实现上下文的恢复来实现协程、使用ucontext库函数来实现协程、腾讯开源的libco协程库
~~~
协程使用
* 不能存在阻塞代码
* 不能通过全局变量储存状态
    