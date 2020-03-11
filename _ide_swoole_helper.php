<?php
namespace Swoole\Tip;

use Swoole\Server;


/** server配置提醒
 * Class ServerSet
 * @author sjm
 * @package Swoole\Tip
 * @link https://wiki.swoole.com/#/server/setting
 * @method static reactor_num($tip = '设置启动的 Reactor 线程数。【默认值：CPU 核数】') 反应堆
 * @method static worker_num($tip = '设置启动的 Worker 进程数。【默认值：CPU 核数】')
 * @method static max_request($tip = '设置 worker 进程的最大任务数。【默认值：0 即不会退出进程】')
 * @method static max_connection($tip = '服务器程序，最大允许的连接数。【默认值：ulimit -n】')
 * @method static task_worker_num($tip = '配置 Task 进程的数量。【默认值：未配置则不启动 task】')
 * @method static task_ipc_mode($tip = '设置 Task 进程与 Worker 进程之间通信的方式。【默认值：1】')
 * @method static task_max_request($tip = '设置 task 进程的最大任务数。【默认值：0】')
 * @method static task_tmpdir($tip = '设置 task 的数据临时目录。【默认值：Linux /tmp 目录】')
 * @method static task_enable_coroutine($tip = '开启 Task 协程支持。【默认值：false】，v4.2.12 起支持')
 */
class ServerSet{}

//--------------------------------------------------------
//  Swoole Server 事件回调提示函数,仅供参考无实际意义			  |
//  					Begin							  |
//--------------------------------------------------------

/** 启动后在主进程（master）的主线程回调此函数
 * @param Server $server
 * @inheritDoc
 * 	(不能调用 server 相关函数等操作，因为服务尚未就绪)
 *  可以在 onStart 回调中，将 $serv->master_pid 和 $serv->manager_pid 的值保存到一个文件中。
 *  这样可以编写脚本，向这两个 PID 发送信号来实现关闭和重启的操作。
 * @link https://wiki.swoole.com/#/server/events?id=onstart
 * @tips
 * 	 SWOOLE_BASE 模式下没有 master 进程，因此不存在 onStart 事件，请不要在 BASE 模式中使用使用 onStart 回调函数。
 */
function serverStart(\Swoole\Server $server){}

/** 此事件在 Server 正常结束时发生
 * @param Server $server
 * @link https://wiki.swoole.com/#/server/events?id=onshutdown
 */
function serverShutdown(\Swoole\Server $server){}

/** 此事件在 Worker 进程 /Task 进程启动时发生
 *  这里创建的对象可以在进程生命周期内使用。
 * @param Server $server
 * @param int $workerId
 * @link https://wiki.swoole.com/#/server/events?id=onworkerstart
 */
function serverWorkerStart(\Swoole\Server  $server, int $workerId){}

/** 此事件在 Worker 进程终止时发生。
 *  在此函数中可以回收 Worker 进程申请的各类资源。
 * @param Server $server
 * @param int $workerId
 * @link https://wiki.swoole.com/#/server/events?id=onworkerstop
 */
function serverWorkerStop(\Swoole\Server $server, int $workerId){}

/** 仅在开启 reload_async 特性后有效
 * @param Server $server
 * @param int $workerId
 * @link https://wiki.swoole.com/#/server/events?id=onworkerexit
 */
function serverWorkerExit(\Swoole\Server $server, int $workerId){}

/**
 * @param Server $server
 * @param int $fd 连接的文件描述符
 * @param int $reactorId 连接所在的 Reactor 线程 ID
 * @link  https://wiki.swoole.com/#/server/events?id=onconnect
 */
function serverConnect(\Swoole\Server $server, int $fd, int $reactorId){}

/**
 * @param Server $server
 * @param int $fd 连接的文件描述符
 * @param int $reactorId TCP 连接所在的 Reactor 线程 ID
 * @param string $data 收到的数据内容，可能是文本或者二进制内容
 * @link https://wiki.swoole.com/#/server/events?id=onreceive
 */
function serverReceive(\Swoole\Server $server, int $fd, int $reactorId, string $data){}

/** 接收到 UDP 数据包时回调此函数，发生在 worker 进程中。
 * @param Server $server
 * @param string $data
 * @param array $clientInfo
 * @link https://wiki.swoole.com/#/server/events?id=onpacket
 */
function serverPacket(\Swoole\Server $server, string $data, array $clientInfo){}

/** TCP 客户端连接关闭后，在 worker 进程中回调此函数。
 * @param Server $server
 * @param int $fd 连接的文件描述符
 * @param int $reactorId 来自那个 reactor 线程，主动 close 关闭时为负数
 * @link https://wiki.swoole.com/#/server/events?id=onclose
 */
function serverClose(\Swoole\Server $server, int $fd, int $reactorId){}

/** 在 task 进程内被调用
 * @param Server $server
 * @param int $task_id
 * @param int $src_worker_id
 * @param mixed $data 可以是任意类型
 * @link https://wiki.swoole.com/#/server/events?id=ontask
 */
function serverTask(\Swoole\Server $server, int $task_id, int $src_worker_id, $data){}

/** 此回调函数在 worker 进程被调用，当 worker 进程投递的任务在 task 进程中完成时
 *   task 进程会通过 Swoole\Server->finish() 方法将任务处理的结果发送给 worker 进程。
 * @param Server $server
 * @param int $task_id
 * @param string $data
 * @link https://wiki.swoole.com/#/server/events?id=onfinish
 */
function serverFinish(\Swoole\Server $server, int $task_id, string $data){}

/** 当工作进程收到由 $server->sendMessage() 发送的 unixSocket 消息时会触发 onPipeMessage 事件。
 *  worker/task 进程都可能会触发 onPipeMessage 事件
 * @param Server $server
 * @param int $src_worker_id
 * @param mixed $message 消息内容，可以是任意 PHP 类型
 * @link https://wiki.swoole.com/#/server/events?id=onpipemessage
 */
function serverPipeMessage(\Swoole\Server $server, int $src_worker_id, $message){}

/** 当 Worker/Task 进程发生异常后会在 Manager 进程内回调此函数。
 * @param Server $server
 * @param int $worker_id
 * @param int $worker_pid
 * @param int $exit_code
 * @param int $signal
 * @link https://wiki.swoole.com/#/server/events?id=onworkererror
 */
function serverWorkerError(\Swoole\Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal){}

/** 当管理进程启动时触发此事件
 * @param Server $server
 * @link https://wiki.swoole.com/#/server/events?id=onmanagerstart
 */
function serverManagerStart(\Swoole\Server $server){}

/** 当管理进程结束时触发
 * @param Server $server
 * @link https://wiki.swoole.com/#/server/events?id=onmanagerstop
 */
function serverManagerStop(\Swoole\Server $server){}

/** http服务类型
 * @param \Swoole\Http\Request $request ,别名类  swoole_http_request
 * @param \Swoole\Http\Response $response,别名类 swoole_http_response
 */
function serverHttpRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response){}

/** websocket
 *  WebSocket 建立连接后进行握手。WebSocket 服务器会自动进行 handshake 握手的过程，如果用户希望自己进行握手处理，可以设置 onHandShake 事件回调函数。
 * @param \Swoole\Http\Request $request
 * @param \Swoole\Http\Response $response
 */
function serverWsHandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response){}

/** websocket服务
 * 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数。
 * @param \Swoole\Websocket\Server $server
 * @param \Swoole\Http\Request $request
 */
function serverWsOpen(\Swoole\Websocket\Server $server, \Swoole\Http\Request $request){}

/** websocket服务
 * 当服务器收到来自客户端的数据帧时会回调此函数。
 * @param \Swoole\Websocket\Server $server
 * @param \Swoole\Websocket\Frame $frame
 */
function serverWsMessage(\Swoole\Websocket\Server $server, \Swoole\Websocket\Frame $frame){}

//--------------------------------------------------------
//  Swoole Server 事件回调提示函数,仅供参考无实际意义			  |
//  					End 							  |
//--------------------------------------------------------
