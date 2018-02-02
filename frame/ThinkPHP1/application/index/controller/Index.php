<?php
namespace app\index\controller;
use think\facade\Hook;

class Index
{
    public function index()
    {
        //绑定测试
        bind('testBind',function ($nam){
            return 'hi '.$nam ;
        }) ;
        echo app('testBind',['a']),'<br />' ;

        //门面测试
        $test = new \app\common\Test();
        echo $test->hello('ss'),'<br />' ;
        echo \app\facade\Test::hello('sd'),'<br />';
        echo \app\facade\Test::what(),'<br />';

        //行为,钩子测试
        //$data = Hook::exec('app\\index\\behavior\\appInit');

        return 'what\'s fuck!';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function test()
    {
        return 'test' ;
    }
}
