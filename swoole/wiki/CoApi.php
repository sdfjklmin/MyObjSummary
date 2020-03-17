<?php
//------------------
//  设置协程相关参数
//------------------
$options = [
	//设置全局最大协程数,超出后会无法创建
	'max_coroutine' => 0,
];
Swoole\Coroutine::set($options);

//-----------------------------------------------------------------------------------
//  创建协程
// Swoole\Coroutine::create(callable $function, ...$args) : int|false;
// go(callable $function, ...$args) : int|false; // 参考php.ini的use_shortname配置
// ...$args 对应 callable 的形参
//-----------------------------------------------------------------------------------
//多个协程 co::sleep 会产生协程调度,即将当前协程 yield,程序往下继续执行,yield 之后再 resume
$ab = 'ab';
Swoole\Coroutine::create(function ($a,$b)use ($ab){
	echo '1-2 start',"\n"; 			 //1
	go(function ($a,$b){
	  echo 'a-b start',"\n"; 		 //2
	  co::sleep(1); 		 // 2 - yield,在下面全部代码之后执行,包括 释放资源 示例
	  echo 'a-b end',"\n";   		 // 5
	},'a','b');
	go(function (){
		echo 'no params start',"\n"; // 3
		co::sleep(3); 		 // 3 - yield
		echo 'no params end ',"n"; 	 //6
	});
	echo '1-2 end',"\n"; 			 //4
},1,2);

//-----------------------------------------------
// 释放资源
// Swoole\Coroutine::defer(callable $function);
// defer(callable $function); // 短名API
//----------------------------------------------
go(function () {
	$db = new Swoole\Coroutine\Mysql();
	//先注册 defer 的后执行,就算抛出了异常,已注册的 defer 也会被执行
	defer(function () use ($db) {
		$db->close();
	});
});

//-----------------------------------------------------
// exists 判断协程释放存在
// Swoole\Coroutine::exists(int $cid = 0): bool
//----------------------------------------------------

//---------------------------------------------------------------------
// getCid 获取当前协程的唯一 ID, 它的别名为 getUid, 是一个进程内唯一的正整数。
// Swoole\Coroutine::getCid(): int
//---------------------------------------------------------------------


//----------------------------------------
// getPcid 获取当前协程的父 ID。
// Swoole\Coroutine::getPcid([$cid]): int
//----------------------------------------

//------------------------------------------------------------------
// getcontext 获取当前协程的上下文对象。
// Swoole\Coroutine::getContext([$cid]): Swoole\Coroutine\Context
//------------------------------------------------------------------

//----------------------------------------------------------------
// yield 手动让出当前协程的执行权,不是基于 IO 的协程调度,必须与 Coroutine::resume() 方法成对使用,
// 则将会造成协程泄漏，被挂起的协程永远不会执行
// Swoole\Coroutine::yield(); 别名 Coroutine::suspend()
//----------------------------------------------------------------
$cid = go(function () {
	echo "co 1 start\n"; //1
	co::yield();
	echo "co 1 end\n"; //3
});

go(function () use ($cid) {
	echo "co 2 start\n"; //2
	co::sleep(0.5);
	co::resume($cid);
	echo "co 2 end\n"; //4
});


//-----------------------------------------------------------
// list 遍历当前进程内的所有协程。返回的是迭代器需要遍历取值
// Swoole\Coroutine::list(): Coroutine\Iterator
// Swoole\Coroutine::listCoroutines(): Coroitine\Iterator
//------------------------------------------------------------


//--------------------------------------------
// stats 获取协程状态。
// Swoole\Coroutine::stats(): array
//-------------------------------------------


//--------------------------------------------
// getbacktrace 获取协程函数调用栈。
// Swoole\Coroutine::getBackTrace(int $cid=0, int $options=DEBUG_BACKTRACE_PROVIDE_OBJECT, int $limit=0): array;
//-------------------------------------------