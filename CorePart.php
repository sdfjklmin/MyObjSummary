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
    //这里没有明确的路由规则
    //只是简单的配置了解释器
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
    public function run()
    {
        $path = trim($_SERVER['REQUEST_URI'],'/') ;
        if(empty($path)) {
            return ;
        }
        $pathArr = explode('/',$path) ;
        if( count($pathArr) != self::PATH_NUM ) {
            exit(' this rule is forbid ');
        }
        $this->interpreter($path);
        exit() ;
    }
}
(new CorePart())->run() ;
