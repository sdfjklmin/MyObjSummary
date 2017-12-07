<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:57
 */

namespace app\index\behavior;


class Test
{
    public function run($params)
    {
        echo 'run';
    }

    public function appInit($params)
    {
        echo 'init';
    }
    public function appEnd($params)
    {
        echo 'end' ;
    }
}