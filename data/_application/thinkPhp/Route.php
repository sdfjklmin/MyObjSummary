<?php

namespace app\thinkPhp;

/** 简单的路由 根据 $_SERVER['PATH_INFO'] 进行处理
 * Class Route
 * @author sjm
 * @package app\thinkPhp
 */
class Route
{
    /**
     * @var array
     */
    public $server;

    public function __construct($server = [])
    {
        $this->server = $server;
    }

    /** 根据自己的规则,解析路径,通过反射类进行调用实例
     * @param bool $isArray
     * @return mixed|string
     */
    public function path()
    {
        if(isset($this->server['PATH_INFO'])) {
            $path = $this->server['PATH_INFO'];
        }else{
            $path = '/_index/index';
        }
        return $path;
    }

    /**
     * @param $path
     * @return bool
     */
    public function parsing($path)
    {
        $pathArr = explode('/',trim($path,'/'));
        $fixedString = substr($pathArr[0],0,1);
        //使用路由规则
        if($fixedString === '_') {
            return true;
        }else{
            //使用文件规则
            return false;
        }
    }
}