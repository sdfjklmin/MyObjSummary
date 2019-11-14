<?php

namespace php\phpAsy;

use Generator;
use SplQueue;

//--------------------------------------------------
//                  多任务协作
//协程和任务调度之间的关系：
// yield指令提供了任务中断自身的一种方法, 然后把控制交回给任务调度器.
// 因此协程可以运行多个其他任务.
// 更进一步来说, yield还可以用来在任务和调度器之间进行通信
//--------------------------------------------------

/** 一个用轻量级的包装的协程函数
 * Class Task
 */
class Task {

    /**
     * @var
     */
    protected $taskId;

    /**
     * @var Generator
     */
    protected $coroutine;

    /**
     * @var null
     */
    protected $sendValue = null;

    /**
     * @var bool
     */
    protected $beforeFirstYield = true;

    public function __construct($taskId, Generator $coroutine)
    {
        $this->taskId    = $taskId;
        $this->coroutine = $coroutine;
    }

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function setSendValue($sendValue)
    {
        $this->sendValue = $sendValue;
    }

    public function run()
    {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } else {
            $retVal = $this->coroutine->send($this->sendValue);
            $this->sendValue = null;
            return $retVal;
        }
    }

    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}

/**调度器
 * Class Scheduler
 */
class Scheduler {
    protected $maxTaskId = 0;
    protected $taskMap = []; // taskId => task
    protected $taskQueue;

    public function __construct() {
        //设置一个双向链表来提供队列
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
        //进入队列
        $this->taskQueue->enqueue($task);
    }

    public function run() {
        //当前队列不为空时
        while (!$this->taskQueue->isEmpty()) {
            //取出
            $task = $this->taskQueue->dequeue();
            //执行task
            $task->run();
            //当前task是否执行完成
            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                //重新进入队列
                $this->schedule($task);
            }
        }
    }

    public function getMaxTaskId()
    {
        return $this->maxTaskId;
    }

    public function getTaskMap()
    {
        return $this->taskMap;
    }
}

function task1() {
    for ($i = 1; $i <= 10; ++$i) {
        echo "This is task 1 iteration $i.\n";
        yield;
    }
}

function task2() {
    for ($i = 1; $i <= 5; ++$i) {
        echo "This is task 2 iteration $i.\n";
        yield;
    }
}

/*$scheduler = new Scheduler;

$scheduler->newTask(task1());
$scheduler->newTask(task2());
//对前五个迭代来说,两个任务是交替运行的,
//而在第二个任务结束后, 只有第一个任务继续运行
$scheduler->run();*/

/** 系统调用，执行某些系统内核操作
 * Class SystemCall
 * @author sjm
 * @package php\phpAsy
 */
class SystemCall {

    protected $callback;

    public function __construct(callable $callback) {
        $this->callback = $callback;
        //echo "4\n";
    }

    public function __invoke(Task $task, SchedulerSystem $scheduler) {
        $callback = $this->callback;
        return $callback($task, $scheduler);
    }
}
class SchedulerSystem {
    protected $maxTaskId = 0;
    protected $taskMap = []; // taskId => task
    protected $taskQueue;

    public function __construct() {
        //echo "1\n";
        //设置一个双向链表来提供队列
        $this->taskQueue = new SplQueue();
    }

    public function newTask(Generator $coroutine) {
        //echo "2\n";
        $tid = ++$this->maxTaskId;
        $task = new Task($tid, $coroutine);
        $this->taskMap[$tid] = $task;
        $this->schedule($task);
        return $tid;
    }


    public function killTask($tid) {
        if (!isset($this->taskMap[$tid])) {
            return false;
        }

        unset($this->taskMap[$tid]);

        // This is a bit ugly and could be optimized so it does not have to walk the queue,
        // but assuming that killing tasks is rather rare I won't bother with it now
        foreach ($this->taskQueue as $i => $task) {
            if ($task->getTaskId() === $tid) {
                unset($this->taskQueue[$i]);
                break;
            }
        }
        return true;
    }

    public function schedule(Task $task) {
        //进入队列
        $this->taskQueue->enqueue($task);
    }

    public function run() {
        while (!$this->taskQueue->isEmpty()) {
            //echo "8\n";
            $task = $this->taskQueue->dequeue();
            /** @var SystemCall $retval */
            $retval = $task->run();

            if ($retval instanceof SystemCall) {
                //将任务和调度器交个系统操作
                $retval($task, $this);
                continue;
            }

            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }

    public function getMaxTaskId()
    {
        return $this->maxTaskId;
    }

    public function getTaskMap()
    {
        return $this->taskMap;
    }
}

function getTaskId2() {
    return new SystemCall(function(Task $task, SchedulerSystem $scheduler) {
        //echo "5\n";
        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}


/** 创建生成器并不会执行里面的代码
 * @param $max
 * @return Generator
 */
function task($max) {
    //echo "3\n";
    $tid = (yield getTaskId2()); // <-- here's the syscall!
    //echo "6\n";
    for ($i = 1; $i <= $max; ++$i) {
        //echo "7\n";
        echo "This is task $tid iteration $i.\n";
        yield;
    }
}

/*$scheduler = new SchedulerSystem;
//执行顺序 1 2 8 3 4 5 8 6 7 ...
$scheduler->newTask(task(10));
$scheduler->newTask(task(5));
$scheduler->run();*/


function newTask(Generator $coroutine) {
    return new SystemCall(
        function(Task $task, SchedulerSystem $scheduler) use ($coroutine) {
            $task->setSendValue($scheduler->newTask($coroutine));
            $scheduler->schedule($task);
        }
    );
}

function killTask($tid) {
    return new SystemCall(
        function(Task $task, SchedulerSystem $scheduler) use ($tid) {
            $task->setSendValue($scheduler->killTask($tid));
            $scheduler->schedule($task);
        }
    );
}

function childTask() {
    $tid = (yield getTaskId2());
    while (true) {
        echo "Child task $tid still alive!\n";
        yield;
    }
}

function task3() {
    $tid = (yield getTaskId2());
    $childTid = (yield newTask(childTask()));

    for ($i = 1; $i <= 6; ++$i) {
        echo "Parent task $tid iteration $i.\n";
        yield;

        if ($i == 3) yield killTask($childTid);
    }
}

$scheduler = new SchedulerSystem();
$scheduler->newTask(task3());
$scheduler->run();

//现在你可以实现许多进程管理调用.
// 例如 wait（它一直等待到任务结束运行时),
// exec（它替代当前任务)和fork（它创建一个当前任务的克隆). fork非常酷,
// 而且你可以使用PHP的协程真正地实现它, 因为它们都支持克隆
// 太他妈难了
