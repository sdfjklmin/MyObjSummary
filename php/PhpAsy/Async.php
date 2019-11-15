<?php


interface Async
{
    public function begin(callable $callback);
}

class TestAsync implements Async
{
    public function begin(callable $callback)
    {
        var_dump($callback);
    }
}

//link:http://www.laruence.com/2015/05/28/3038.html

//生成器是一种可中断的函数, 在它里面的yield构成了中断点

/** 生成器 实现 range()
 *  也可以通过继承 Iterator 接口实现
 * @param $start
 * @param $end
 * @param int $step
 * @return Generator
 */
function xrange($start, $end, $step = 1) {
    for ($i = $start; $i <= $end; $i += $step) {
        //输出这些值的一个迭代器,而不是真正的数组形式
        //它可以让你在处理大数据集合的时候不用一次性的加载到内存中
        //甚至你可以处理无限大的数据流.
        yield $i;
    }
}

/*foreach (xrange(1, 1000000) as $num) {
    echo $num, "\n";
}*/

/*$xRange = xrange(1,100);

var_dump($xRange); // object(Generator)#1 (0) { }

var_dump($xRange instanceof Iterator); // bool(true)*/

//协程的支持是在迭代生成器的基础上, 增加了数据传输(调用者发送数据给被调用的生成器函数).
//这就把生成器到调用者的单向通信转变为两者之间的双向通信

/** yield 作为一个表达式来使用,即为一个值,而非语句使用
 *  此时调用 send('Foo') , yield 将被 Foo替代,写入 log 中
 * @param $fileName
 * @return Generator
 */
function logger($fileName) {
    $fileHandle = fopen($fileName, 'a');
    while (true) {
        fwrite($fileHandle, yield . "\n");
    }
}
/*$logger = logger(__DIR__ . '/log');
$logger->send('Foo');
$logger->send('Bar');*/


/**
 * @return Generator
 */
function gen2() {
    yield 'foo';
    yield 'bar';
}

$gen = gen2();
var_dump($gen->send('something'));

// 如之前提到的在send之前, 当$gen迭代器被创建的时候一个renwind()方法已经被隐式调用
// 所以实际上发生的应该类似:
//$gen->rewind();
//var_dump($gen->send('something'));

//这样renwind的执行将会导致第一个yield被执行, 并且忽略了他的返回值.
//真正当我们调用yield的时候, 我们得到的是第二个yield的值! 导致第一个yield的值被忽略.
//string(3) "bar"


/**
 * yield数据传输
 * 生成器是一种可中断的函数, 在它里面的yield构成了中断点
 * 程序为从右往左执行
 * 每次执行到yield的地方中断
 * @return Generator
 */
function gen() {
    $ret1 = (yield 'yield1'); //st1
    var_dump($ret1);          //st2
    $ret2 = (yield 'yield2'); //st3
    var_dump($ret2);          //st4
}
$gen = gen();//返回生成器
// yield1
var_dump($gen->current());           //当前中继点 执行到st1 -有yield返回-> yield1
// ret1 yield2
var_dump($gen->send('ret1')); //发送数据 执行 st2 -$ret1为当前send的值-> ret1 ,st3 -有yield返回-> yield2
// ret2 null
var_dump($gen->send('ret2')); //发送数据 执行 st4 -$ret2为当前send的值-> ret2 , 无yield数据返回 -输出->null

var_dump('---------------------');
$gen2 = gen();
//直接发送数据 st1 -忽略当前yield返回值-> $ret1 = tt, st2 -> tt , st3 -> yield2
var_dump($gen2->send('tt'));
// 当前 yield st3 -> yield2
var_dump($gen2->current());
// gen2-1 , null
var_dump($gen2->send('gen2-1'));

