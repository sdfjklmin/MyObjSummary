## [Swoole 新版文档 ☺](https://wiki.swoole.com/#/)

#### 快速入门
* 和之前没多大差别, 请移步[official](../official)

#### [TCP/UDP Server (Swoole\Server)](TcpOrUdpServer.php)
* 方法 `$server->set(),$server->addlistener(),$server->bind() ...`
* 属性 `$server->master_pid,$serverr->worker_id ...`
* 配置 `$server->set(array())`
* 事件 `$server->on('connect',function (\Swoole\Server $servers, $fd) {}); ...`
    * 事件执行顺序
    * 所有事件回调均在 $server->start 后发生
    * 服务器关闭程序终止时最后一次事件是 onShutdown
    * 服务器启动成功后，onStart/onManagerStart/onWorkerStart 会在不同的进程内并发执行
    * onReceive/onConnect/onClose 在 Worker 进程中触发
    * Worker/Task 进程启动 / 结束时会分别调用一次 onWorkerStart/onWorkerStop
    * onTask 事件仅在 task 进程中发生
    * onFinish 事件仅在 worker 进程中发生
    * onStart/onManagerStart/onWorkerStart 3 个事件的执行顺序是不确定的
    
#### [Http Server](HttpServer.php)    