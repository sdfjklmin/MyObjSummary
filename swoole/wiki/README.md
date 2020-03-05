## [Swoole 新版文档 ☺](https://wiki.swoole.com/#/)

### 快速入门
* 和之前没多大差别, 请移步[official](../official)

### [服务端](https://wiki.swoole.com/#/server/init)

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


#### 示例代码缺少必要事件
* 文档链接: https://wiki.swoole.com/#/server/port?id=%e5%a4%9a%e7%ab%af%e5%8f%a3%e4%b8%8b%e7%9a%84%e8%bf%9e%e6%8e%a5%e9%81%8d%e5%8e%86
* 实际内容: 代码中使用了 `Swoole\WebSocket\Server()`,但未设置必要事件 `onMessage`
* 期望内容: 完善代码