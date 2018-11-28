<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/11/28
 * Time: 10:43
 */

namespace MyObjSummary;

/** 路由解释器
 * Class CoreInterpreter
 * @package MyObjSummary
 */
abstract class CoreInterpreter
{
    /** 路由过滤规则
     * @return mixed
     */
    abstract protected function ruleFitter();

    /**  解释器路由匹配
     * @param $path
     */
    public function interpreter($path)
    {
        switch ($path)
        {
            case 't-mode' : $link = 'php/designMode/Zend.php';break ;
            case 't-php'  : $link = 'test/index.php';break ;
            case 't-html' : $link = 'test/index.html';break ;
            default : exit('no match this rule');break ;
        }
        defined('RULE_MATCH') or define('RULE_MATCH',$path) ;
        require APP_DIR.$link;
    }
}

/** 解析路由 特定的路由访问
 * Class CorePart
 * @package MyObjSummary
 */
class CorePart extends CoreInterpreter
{
    const PATH_NUM = 1 ;

    /** 规则实现
     * @return mixed|null|string
     */
    protected function ruleFitter()
    {
        $path = trim($_SERVER['REQUEST_URI'],'/') ;
        if(empty($path)) {
            return null;
        }
        $pathArr = explode('/',$path) ;
        if( count($pathArr) != self::PATH_NUM ) {
            exit(' this rule is forbid ');
        }
        return $path ;
    }

    /** 运行入口
     * @return null
     */
    public function run()
    {
        $path = $this->ruleFitter();
        if(!$path) {
            return null ;
        }
        $this->interpreter($path);
        exit() ;
    }
}
(new CorePart())->run() ;
