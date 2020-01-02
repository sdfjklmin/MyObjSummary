## 基础知识
* 不能通过全局变量获取属性参数
* 通过容器获取的类都是单例,会存在覆盖
* 数据都通过协程上下文去处理
* 不能存在阻塞代码(协程)
* 不能通过全局变量储存状态

## IDE插件
* 安装 annotation 插件，可自动生成对应注解所需要的命名空间

## 控制器

#### 通过`配置文件`定义路由 config/routes.php
```
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

#### 通过`@AutoController`注解定义路由
    使用 @AutoController 注解时需 use Hyperf\HttpServer\Annotation\AutoController; 命名空间；

#### 注入`request 和 response`或者其它服务类

构造注入
```
protected $request;

//可注入多个,一般只需要 request 和 response
//对应的Interface使用 Hyperf\HttpServer\Contract\ 下的接口类
public function __construct(RequestInterface $request)
{
    $this->request = $request;
}
```

方法注入
```
public function index(RequestInterface $request)
{
    return $request->input('name');
}
```

注解 annotation
```
use Hyperf\HttpServer\Contract\RequestInterface;
/**
 * @var RequestInterface
 * @Inject()
 */
protected $request;

#错误的注释格式会导致注解无法解析
/**@Inject()
 * @var RequestInterface
 */
```

## 配置 config
```
<?php


namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Class ConfigUseController
 * @author sjm
 * @package App\Controller
 * @AutoController()
 */
class ConfigUseController extends BaseController
{
    //++++++++++++++++++++++++++++++++++++++++++++++++
    // config 位于框架的config文件夹中，具体意义请查看文档
    //+++++++++++++++++++++++++++++++++++++++++++++++

    /**
     * @Inject()
     * @var \Hyperf\Contract\ConfigInterface
     */
    protected $config;

    public function getConfigByConfig()
    {
        $config = $this->config->get('app_name');
        return $config;
    }

    public function getConfigHasValue()
    {
        $config = $this->config->has('ttt');
        return [$config];
    }

    public function getConfigByEnv()
    {
        $config = env('APP_NAME');
        return [$config];
    }
}

```
    
    
    
    
    
## 协程使用
```php
<?php


namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Coroutine\Concurrent;

/** 协程使用的例子
 * Class CoroutineUseController
 * @author sjm
 * @package App\Controller
 * @AutoController()
 */
class CoroutineUseController
{
    //+++++++++++++++++++++++++++++++++++++
    // 以下协程的使用方法只能在协程中使用
    //+++++++++++++++++++++++++++++++++++++

    //协程 Coroutine(协程)
    public function base()
    {
        //判断当前是否在协程中
        $check = Coroutine::inCoroutine();
        return [
            'is_coroutine' => $check,
            'coroutine_id' => Coroutine::id()
        ];
    }

    //协程间通信使用 channel(通道)
    public function co()
    {
        //协程1
        co(function () use (&$data) {
            //数据通道
            $channel = new \Swoole\Coroutine\Channel();
            //可根据$channel->isEmpty()来进行业务处理

            //协程2
            co(function () use ($channel) {
                //只能单次push,data可以设置为json，query，array等
                $channel->push([
                    'name'  => 'co_two',
                    'value' => 'push co two'
                ]);
                var_dump($channel->isFull());
                var_dump($channel->isEmpty());
            });
            $data = $channel->pop();
            var_dump($data);
        });

        // defer 当我们希望在协程结束时运行一些代码时
        // 将一段函数以 栈(stack) 的形式储存起来，栈(stack) 内的函数会在当前协程结束时以 先进后出 的流程逐个执行
        defer(function (){
            echo 'defer';//3
        });
        defer(function (){
            echo 'defer2'.'time:';//2
        });
        return '11'; //1
    }

    //协程 WaitGroup(等待组)
    public function coWaitGroup()
    {
        //当前主协程一直阻塞等待(不会阻塞当前进程)所有协程完成后再继续运行
        $wg = new \Hyperf\Utils\WaitGroup();
        var_dump(1);
        // 计数器加二
        $wg->add(2);
        // 创建协程 A
        co(function () use ($wg) {
            // some code
            sleep(2);
            var_dump(2);
            // 计数器减一
            $wg->done();
        });
        // 创建协程 B
        co(function () use ($wg) {
            // some code
            var_dump(3);
            // 计数器减一
            $wg->done();
        });
        // 等待协程 A 和协程 B 运行完成
        $wg->wait();
        var_dump(4);
        //输出为 1 [2,3|3,2](具体时间根据代码运行时间) 4
    }

    //协程 parallel(平行) 抽象的 WaitGroup 更好用
    public function coParallel()
    {
        $parallel = new \Hyperf\Utils\Parallel();
        $parallel->add(function () {
            return 1;
        });
        $parallel->add(function () {
            return 2;
        });
        // $result 结果为 [1, 2]
        $result = $parallel->wait();
        var_dump($result);
    }

    //parallel更简单的使用
    public function coParallelSimple()
    {
        // 传递的数组参数您也可以带上 key 便于区分子协程，返回的结果也会根据 key 返回对应的结果
        $result = parallel([
            function () {
                return 1;
            },
            function () {
                return 2;
            }
        ]);
        return $result;
    }

    //concurrent(同时)
    //用来控制一个代码块内同时运行的最大协程数量的特性。
    public function coConcurrent()
    {
        //以下样例，当同时执行 10 个子协程时，会在循环中阻塞，
        //但只会阻塞当前协程，直到释放出一个位置后，循环继续执行下一个子协程
        $concurrent = new Concurrent(10);
        for ($i = 0; $i < 15; ++$i) {
            $concurrent->create(function () use ($i) {
                // Do something...
                var_dump($i);
            });
        }
    }

    //协程上下文 context(前后关系，语境)
    public function coContext()
    {
        //通过调用 set(string $id, $value) 方法储存一个值到当前协程的上下文中
        $setRet = \Hyperf\Utils\Context::set('context','coroutine context');
        $getRet = \Hyperf\Utils\Context::get('context');
        $hasRet = \Hyperf\Utils\Context::has('test');
        //override，覆盖 test 的值，如果有就进行覆盖，没有则 set
        $oveRet = \Hyperf\Utils\Context::override('test',function (){
            return 'override';
        });
        return [$setRet,$getRet,$hasRet,$oveRet];
    }

    //协程 Hook(钩子)
    //行为:在应用执行过程中的一个动作(抽象地理解)
    //有些行为的作用位置都是在应用执行前，有些行为都是在模板输出之后，
    //我们把这些行为发生作用的位置称之为钩子。
    //当应用程序运行到这个钩子的时候，就会被拦截下来，统一执行相关的行为，
    //类似于AOP编程中的“切面”的概念，给某一个钩子绑定相关行为就成了一种类AOP编程的思想
    public function coHook()
    {

    }
}
```

## [视图](https://doc.hyperf.io/#/zh/view)
~~~
相对简单，直接参照官网。
注意点： 资源存放在 根目录下 public 中，访问方式为 http://127.0.0.1:9501/css/style.css
        对应的资源路径会根据 访问路由进行动态变化 ， 可设置 访问变量 $HoGoAssets = 'http://127.0.0.1:9501'；
        <link href="{{$HoGoAssets}}/css/dark-sidebar.css" rel="stylesheet">
~~~