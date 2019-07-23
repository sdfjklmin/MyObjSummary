<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/11/22
 * Time: 11:41
 */
//重定向页面 非index入口重定向到index
//header('location:/index.php');
//以index为入口,根据入口访问路由做路由分配
//设置跨域
//header('Access-Control-Allow-Origin','*') ;
//file_put_contents('t.txt',json_encode($_REQUEST,true).PHP_EOL, FILE_APPEND) ;

//目录入口
defined('APP_DIR') or define('APP_DIR',__DIR__.'/');
defined('APP_ROOT') or define('APP_ROOT','./');
defined('APP_STATIC') or define('APP_STATIC',APP_ROOT.'static/');

//不需要生成的 后缀 可提成配置文件
//文件夹
defined('NOT_LINK') or define('NOT_LINK', ['.','frame','html','object','static','tool','vendor','min','config']) ;
//文件后缀(避免无法解析时下载)
defined('NOT_SUFFIX') or define('NOT_SUFFIX', ['png','md','jpg','zip']) ;

//简单的路由配置,非入口常量定义
//统一以 t-xx 为准
require './vendor/autoload.php';
require APP_ROOT.'CorePart.php';

$config = array_merge(
    require APP_ROOT.'config/dir_name.php'
);

//运行
(new \MyObjSummary\CorePart())->run($config) ;