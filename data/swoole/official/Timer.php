<?php

/** 定时器测试
 * Class Test
 */
class Test
{
    private $str = "Say Hello";
    public function onAfter()
    {
        echo $this->str."\n"; // 输出”Say Hello“
    }
}

$test = new Test();

swoole_timer_after(1000, array($test, "onAfter") ); // 成员变量

swoole_timer_after(2000, function() use($test){ // 闭包
    $test->onAfter(); // 输出”Say Hello“
});