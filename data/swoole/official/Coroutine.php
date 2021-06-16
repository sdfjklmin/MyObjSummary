<?php
//----------------------------------------------------------------
// Coroutine(协程) + Channel(管道)	  |							 |
//----------------------------------------------------------------
//传统实现协程一般都是用 yield,请参照 php/phpAsy 异步脚本控制			 |
//swoole中不要你去使用yield具体的含义,语境.使用同步编码实现底层异步协程	 |
//----------------------------------------------------------------

/**
 * Class WebServer
 * @author sjm
 * @link https://wiki.swoole.com/wiki/page/p-coroutine_realization.html
 * @desc
 *  1.调用onRequest事件回调函数时，底层会调用C函数coro_create创建一个协程（#1位置），
 * 		同时保存这个时间点的CPU寄存器状态和ZendVM stack信息。
 *	2.调用mysql->connect时发生IO操作，底层会调用C函数coro_save保存当前协程的状态，
 * 		包括Zend VM上下文以及协程描述信息，并调用coro_yield让出程序控制权，当前的请求会挂起（#2位置）
 *	3.协程让出程序控制权后，会继续进入EventLoop处理其他事件，这时Swoole会继续去处理其他客户端发来的Request
 *	4.IO事件完成后，MySQL连接成功或失败，底层调用C函数coro_resume恢复对应的协程，
 * 		恢复ZendVM上下文，继续向下执行PHP代码（#3位置）
 *	5.mysql->query的执行过程与mysql->connect一致，也会进行一次协程切换调度
 *	6.所有操作完成后，调用end方法返回结果，并销毁此协程
 */
class WebServer
{
	public function run()
	{
		$server = new Swoole\Http\Server('127.0.0.1', 9501, SWOOLE_BASE);

		#1
		$server->on('Request', function($request, $response) {
			$mysql = new Swoole\Coroutine\MySQL();
			#2
			$res = $mysql->connect([
				   'host'     => '127.0.0.1',
				   'user'     => 'root',
				   'password' => 'root123',
				   'database' => 'tt',
			   ]);
			#3
			if ($res == false) {
				$response->end("MySQL connect fail!");
				return;
			}
			$ret = $mysql->query('show tables', 2);
			$response->end("swoole response is ok, result=".var_export($ret, true));
		});

		$server->start();
	}
}