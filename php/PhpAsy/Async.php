<?php
echo "<pre />";

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
 * yield数据传输
 * @return Generator
 */
function gen() {
    $ret = (yield 'yield1');
    var_dump($ret);
    $ret = (yield 'yield2');
    var_dump($ret);
}
/*$gen = gen();
var_dump($gen->current());
var_dump($gen->send('ret1'));
var_dump($gen->send('ret2'));*/


/**
 * Class Task
 */
class Task {

    protected $taskId;
    protected $coroutine;
    protected $sendValue = null;
    protected $beforeFirstYield = true;

    public function __construct($taskId, Generator $coroutine) {
        $this->taskId = $taskId;
        $this->coroutine = $coroutine;
    }

    public function getTaskId() {
        return $this->taskId;
    }

    public function setSendValue($sendValue) {
        $this->sendValue = $sendValue;
    }

    public function run() {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } else {
            $retval = $this->coroutine->send($this->sendValue);
            $this->sendValue = null;
            return $retval;
        }
    }

    public function isFinished() {
        return !$this->coroutine->valid();
    }
}

/**
 * Class Scheduler
 */
class Scheduler {
    protected $maxTaskId = 0;
    protected $taskMap = []; // taskId => task
    protected $taskQueue;

    public function __construct() {
        $this->taskQueue = new SplQueue();
    }

    public function newTask(Generator $coroutine) {
        $tid = ++$this->maxTaskId;
        $task = new Task($tid, $coroutine);
        $this->taskMap[$tid] = $task;
        $this->schedule($task);
        return $tid;
    }

    public function schedule(Task $task) {
        $this->taskQueue->enqueue($task);
    }

    public function run() {
        while (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            $task->run();

            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
}
