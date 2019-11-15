<?php

namespace php\phpAsy\two;

use Generator;
use SplQueue;

/** 任务脚本
 * Class Job
 * @author sjm
 */
class Job
{
    public static function task1()
    {
        for ($i = 1; $i <= 10; $i++) {
            echo "This is Job task1 {$i} ...\n";
            yield;
        }
    }

    public static function task2()
    {
        for ($i = 1; $i <= 5; $i++) {
            echo "This is Job task2 {$i} ...\n";
            yield;
        }
    }

    public static function task3()
    {
        echo "[ INFO ] start task3 \n";
        $tt = yield;
        $tt->tt3 = 'task3';
    }

    public static function task4()
    {
        echo "[ INFO ] start task4 \n";
        $tt = yield;
        $tt->tt4 = 'task4';
    }

    public static function task5()
    {
        echo "[ INFO ] start task5 \n";
        $tt = yield;
        $tt->tt5 = 'task5';
    }
}


//---------------------------------
// 当前任务脚本通过 生成器来实现并行处理
//---------------------------------
/** 任务脚本
 * Class Task
 * @author sjm
 */
class Task
{
    /** 执行任务的ID
     * @var
     */
    protected $taskId;

    /** 生成器
     * @var Generator
     */
    protected $coroutine;

    /** 生成器 send() 的参数
     * @var
     */
    protected $sendValue;

    /** 是否为第一调用 生成器
     * @var bool
     */
    protected $firstYield = true;

    /**
     * Task constructor.
     * @param $taskId
     * @param Generator $coroutine
     */
    public function __construct($taskId, Generator $coroutine)
    {
        $this->taskId       =   $taskId;
        $this->coroutine    =   $coroutine;
    }

    /**
     * @return mixed
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /** 设置 send() 参数
     * @param $value
     */
    public function setValue($value)
    {
        $this->sendValue = $value;
    }

    /** 执行入口
     * @return mixed
     */
    public function run()
    {
        //由于 生成器的特性 需要判断是否是第一次调用
        if($this->firstYield) {
            $this->firstYield = false;
            //调用当前
            return $this->coroutine->current();
        }else{
            $ret = $this->coroutine->send($this->sendValue);
            $this->sendValue = null;
            return $ret;
        }
    }

    /** 检测当前生成器是否可用
     * @return bool
     */
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}
//当前任务job只会执行一次，只有一个run。怎么办呢 ？ (使用调度器进行调度，通过重复添加队列进行处理)
/*$job1 = Job::task1();
$task = new Task(1,$job1);
$task->run();*/


/** 调度器
 * Class Scheduler
 * @author sjm
 */
class Scheduler
{
    /** 队列
     * @var SplQueue
     */
    protected $taskQueue;

    /** 设置最大的执行Id
     * @var
     */
    protected $maxTaskId = 0;

    /** 任务map
     * @var
     */
    protected $taskMap;

    public function __construct()
    {
        //设置任务队列
        $this->taskQueue = new SplQueue();
    }

    /** 生成任务，将任务放入队列中
     * @param Generator $coroutine
     * @param $sendValue
     * @return int
     */
    public function newTask(Generator $coroutine, $sendValue = '')
    {
        $tid  = ++$this->maxTaskId;
        //初始化 任务脚本
        $task = new Task($tid,$coroutine);
        if(!empty($sendValue)) {
            $task->setValue($sendValue);
        }
        $this->taskMap[$tid]    =   $task;
        //将任务放入队列中
        $this->intoQueue($task);
        return $tid;
    }

    public function copyTask(Task $task)
    {
        //fork
        $task2 = clone $task;
        $this->intoQueue($task2);
    }

    /** 将任务放入队列
     * @param Task $task
     */
    public function intoQueue(Task $task)
    {
        $this->taskQueue->enqueue($task);
    }

    /**
     * 执行入口
     */
    public function run()
    {
        //当前队列不为空，说明有任务脚本
        while (!$this->taskQueue->isEmpty()) {
            //获取队列中的数据
            /** @var Task $task */
            $task = $this->taskQueue->dequeue();
            //执行
            $task->run();
            if($task->isFinished()) {
                //去除已经执行完成的任务
                unset($this->taskMap[$task->getTaskId()]);
            }else{
                //没有执行完成，重新进入队列
                $this->intoQueue($task);
            }
        }
    }

    /**
     * @return int
     */
    public function getMaxTaskId()
    {
        return $this->maxTaskId;
    }

    /**
     * @return mixed
     */
    public function getTaskMap()
    {
        return $this->taskMap;
    }
}
class TT
{
    public $tt3 ;
    public $tt4 ;
    public $tt5 ;
}
//通过调度器调度
//流程:
//创建调度器 -> 调用 newTask 生成任务[传入 Job(迭代器)] ->
// 初始化 Task -> 加入队列中 -> run(执行) ->  从队列中获取 Task -> 运行 Task run() -> 判断是否完成 -> 重新加入队列
$scheduler  = new Scheduler;
$tt = new TT();
$scheduler->newTask(Job::task3(),$tt);
$scheduler->newTask(Job::task4(),$tt);
$scheduler->newTask(Job::task5(),$tt);
$scheduler->run();
var_dump($tt);

