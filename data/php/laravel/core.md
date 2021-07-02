#### Illuminate\Foundation\Application  extends Illuminate\Container\Container
~~~
1.  $app = new Illuminate\Foundation\Application，实例化。

1.0 设置运行的 path，绑定框架各个目录的 path 到 Container 的 instances 中。

1.2 registerBaseBindings 将 Application 、Container、PackageManifest 注册到 Container 的 instances 中。

1.3 registerBaseServiceProviders 
     通过 $this->register 将 EventServiceProvider、LogServiceProvider、RoutingServiceProvider 
        注册到 Container 的 serviceProviders、loadedProviders 中。
        
     通过 EventServiceProvider，将 events 注册到 Container 的 bindings 中，格式为
        ['events' => ['concrete' => Closure, 'shared' => true ]]
            
     通过 LogServiceProvider，将 log 注册到 Container 的 bindings 中，格式为
        ['log' => ['concrete' => Closure, 'shared' => true ]]
            
     通过 RoutingServiceProvider 将 router、url、redirect、
         Psr\Http\Message\ServerRequestInterface、Psr\Http\Message\ResponseInterface、
         Illuminate\Contracts\Routing\ResponseFactory、Illuminate\Routing\Contracts\ControllerDispatcher
         批量注册到 Container 的 bindings 中，格式为 
            ['router' => ['concrete' => Closure, 'shared' => true ]]
            ['url' => ['concrete' => Closure, 'shared' => true ]]  
            ...
            
1.4 registerCoreContainerAliases 注册核心类的别名到 Container 的 abstractAliases 中。
     具体有 app、auth、auth.driver、cache、blade.compiler、config、db、等等。
     
1.5 通过实例化 Application，给 Container 的属性赋值(instances、serviceProviders、loadedProviders、bindings)

t.t 调试
    dd($this->instances, $this->bindings, $this->abstractAliases);
    
    设置:
        $app->instance  => $app->instances[]
        $app->singleton -> $app->bind => $app->bindings[]
        $app->bind      => $app->bindings[]
        $app->register  => $app->serviceProviders[]、$app->loadedProviders[]
        $app->alias     => $app->aliases[]、$app->abstractAliases[]
        
    获取:    
        $app->make      => 获取 $this->bindings 中，绑定的 concrete

2.1 绑定 核心 处理类 Http/Kernel、Console/Kernel、Exceptions/Handler
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        App\Http\Kernel::class
    );
    
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
~~~

#### App\Http\Kernel extend Illuminate\Contracts\Http\Kernel
~~~
3.1 获取之前绑定的核心、处理、运行。
    /** @var App\Http\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    //Illuminate\Contracts\Http\Kernel 会获取之前初始化的 $app 和已绑定的 router : Illuminate\Routing\Router
    
    //加载项目、自身的中间件、路由、命令、服务等一些列东西
    //路由匹配规则参考: https://learnku.com/articles/38503
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
~~~

#### Illuminate\Http\Request::capture()
~~~
3.1.2 初始化请求
    Illuminate\Http\Request::capture()
    
    /**
     * Create a new Illuminate HTTP request from server variables.
     *
     * @return static
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }
    
    SymfonyRequest::createFromGlobals() 将 $_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER
    赋值给 request (Illuminate\Http\Request extends Symfony\Component\HttpFoundation\Request)
~~~

#### Illuminate\Foundation\Http\Kernel -> handle
~~~
4. 核心类处理
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
     /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request extends SymfonyRequest $request
     * @return \Illuminate\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);
    
        Facade::clearResolvedInstance('request');
    
        $this->bootstrap();
    
        return (new Pipeline($this->app))
                    ->send($request)
                    ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
                    ->then($this->dispatchToRouter());
    }
    
    这里的路由分发主要是 $this->dispatchToRouter();
    
    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);
            // \Illuminate\Routing\Router
            return $this->router->dispatch($request);
        };
    }
    
    经过一系列规则最终走向 toResponse($request, $response)
    Response prepars Request
~~~