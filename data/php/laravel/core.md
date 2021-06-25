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
    
    Illuminate\Contracts\Http\Kernel 会获取之前初始化的 $app 和已绑定的 router : Illuminate\Routing\Router
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
~~~