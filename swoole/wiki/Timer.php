<?php
//参数为零的计时器
/*Swoole\Event::defer(function () {
	echo "hello\n";
});*/

// ------------------------------------------------
// 				方法
// tick()、after()、clear() 都拥有一个函数风格的别名
//
// 类静态方法	函数风格别名
// Swoole\Timer::tick()		swoole_timer_tick()
// Swoole\Timer::after()	swoole_timer_after()
// Swoole\Timer::clear()	swoole_timer_clear()
//--------------------------------------------------

//设置一个间隔时钟定时器
Swoole\Timer::tick(3000, function (int $timer_id, $paramA, $paramB) {
	echo "after 3000ms.\n";
	var_dump($timer_id,$paramA,$paramB);
	Swoole\Timer::tick(14000, function (int $timer_id) {
		echo "after 14000ms.\n";
		//关闭对应的计时器
		\Swoole\Timer::clear($timer_id);
	});
	\Swoole\Timer::clear($timer_id);
},$paramA,$paramB);

exit();


//在指定的时间后执行函数,是一个一次性定时器，执行完成后就会销毁
Swoole\Timer::after(1000, function() {
	echo "timeout 1000\n";
});

//关闭 clear
$timer = Swoole\Timer::after(1000, function () {
	echo "timeout\n";
});
var_dump(Swoole\Timer::clear($timer));
var_dump($timer);
// 输出：bool(true) int(1)
// 不输出：timeout


//---------------------------------------
// clearAll() 清除当前工作进程内的所有定时器。
// Swoole\Timer::clearAll(): bool;
//---------------------------------------


//-------------------------------------------------------
// 返回定时器迭代器，可使用 foreach 遍历全局所有 timer 的 id
// Swoole\Timer::list(): Swoole\Timer\Iterator;
//-------------------------------------------------------
foreach (Swoole\Timer::list() as $timer_id) {
	var_dump(Swoole\Timer::info($timer_id));
}

//------------------------------------------------
// 查看状态
// Swoole\Timer::stats(): array;
//------------------------------------------------