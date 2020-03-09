### [Swoole 新版文档 ☺](https://wiki.swoole.com/#/)

### 快速入门
* 和之前没多大差别, 请移步[official](../official)

### [服务端(异步风格:即所有事件都需要设置回调函数,Swoole\Server))](https://wiki.swoole.com/#/server/init)

#### [TCP/UDP Server (Swoole\Server)](TcpOrUdpServer.php) [link](https://wiki.swoole.com/#/server/tcp_init)
* 方法 `$server->set(),$server->addlistener(),$server->bind(),等`
* 属性 `$server->master_pid,$serverr->worker_id,等`
* 配置 `$server->set(array())`
* 事件 `$server->on('connect',function (\Swoole\Server $servers, $fd) {});等`
    * 事件执行顺序
    * 所有事件回调均在 $server->start 后发生
    * 服务器关闭程序终止时最后一次事件是 onShutdown
    * 服务器启动成功后，onStart/onManagerStart/onWorkerStart 会在不同的进程内并发执行
    * onReceive/onConnect/onClose 在 Worker 进程中触发
    * Worker/Task 进程启动 / 结束时会分别调用一次 onWorkerStart/onWorkerStop
    * onTask 事件仅在 task 进程中发生
    * onFinish 事件仅在 worker 进程中发生
    * onStart/onManagerStart/onWorkerStart 3 个事件的执行顺序是不确定的
    
#### [Http Server (Swoole\Http\Server 继承自 Swoole\Server)](HttpServer.php)  [link](https://wiki.swoole.com/#/http_server) 
* 基础操作 `方法,属性,配置,事件` 同 `Server`  
* 差异
    * Http\Server->on 不接受 onConnect/onReceive 回调设置
    * Http\Server->on 额外接受 1 种新的事件类型 onRequest
        * Request `请求常规操作,header,post,get,server 等`
        * Response `响应对象`
    * 相关配置 [`参考官网`](https://wiki.swoole.com/#/http_server?id=%e9%85%8d%e7%bd%ae%e9%80%89%e9%a1%b9)
        
#### [WebSocket Server  (Swoole\WebSocket\Server 继承自 Swoole\Http\Server)](WebSocketServer.php)   [link](https://wiki.swoole.com/#/websocket_server)
* 基础操作 `方法,属性,配置,事件` 同 `Http Server` 
* 事件同 `HttpServer, Server`
* handShake -> open -> massage -> close
* 额外事件
    * onMessage(Swoole\Websocket\Server  $server, Swoole\Websocket\Frame $frame) 回调函数为必选
    * onOpen(握手成功后执行) 和 onHandShake(自定义握手协议) 回调函数为可选 
* 方法(Swoole\WebSocket\Serve)
    * push, exist, pack, unpack, disconnect等  
    
#### [RedisServer (Swoole\Redis\Server 继承自 Server)](https://wiki.swoole.com/#/redis_server)
~~~
请参考官方示例
~~~    

### [服务端(协程风格:不需要设置回调函数,Swoole\Coroutine\Server)](https://wiki.swoole.com/#/server/co_init)
* 与 `异步风格` 的服务端不同之处在于，Swoole\Coroutine\Server 是完全协程化实现的服务器
* 不需要设置回调函数
* 不会自动创建多个进程
* 需要配合 Process\Pool 模块使用才能利用多核
* `Co` 协程短名,需要配置 php.ini `swoole.use_shortname=On/Off`
* 具体参考官方列举的 `方法`

#### [TCP(Swoole\Coroutine\Server)](TcpCo.php)
* `方法` 参考官网

#### [HTTP(Co\Http\Server extends Co\Server)](HttpCo.php)
* `方法` 参考官网

#### [WebSocket( extends Co\Http\Server)](WebSocketCo.php)
* `方法` 参考官网

#### [协程容器(Swoole\Coroutine\Scheduler)](https://wiki.swoole.com/#/coroutine/scheduler)
* `方法` 参考官网, 实际类 `Swoole\Coroutine\Scheduler`
* 所有的协程必须在协程容器里面创建，Swoole 程序启动的时候大部分情况会自动创建协程容器，用 Swoole 启动程序的方式一共有三种
    * 调用异步风格服务端程序的 start 方法，此种启动方式会在事件回调中创建协程容器，参考 enable_coroutine。
    * 调用 Swoole 提供的 2 个进程管理模块 Process 和 Process\Pool 的 start 方法，此种启动方式会在进程启动的时候创建协程容器，参考这两个模块构造函数的 enable_coroutine 参数。
    * 其他直接裸写协程的方式启动程序，需要先创建一个协程容器 (Co\run() 函数，可以理解为 java,c 的 main 函数)
* [demo](Scheduler.php)    

### [客户端](https://wiki.swoole.com/#/client?id=swooleclient)