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
    /** 是否刷新缓存文件
     * @var bool
     */
    static $refresh = false ;

    /**程序控制
     * @var bool
     */
    protected $isExit = true;

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
            case 'up':
                self::$refresh = true ; $link=''; $this->isExit = false;
                break;
            default : exit('no match this rule');break ;
        }
        defined('RULE_MATCH') or define('RULE_MATCH',$path) ;
        if(!empty($link))
            require APP_DIR.$link;
    }
}

/**
 * Class FileCache
 * @package MyObjSummary
 */
class FileCache {
    private function __construct()
    {
    }
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /** 首页json缓存数据
     * @param string $jsonFileName
     * @return array|bool|string
     */
    public static function indexJson($jsonFileName = 'file.json')
    {
        //目录数据缓存
        $fileJson = file_exists($jsonFileName);
        if($fileJson && !empty(file_get_contents($jsonFileName)) && (CorePart::$refresh ==false)) {
            $link = file_get_contents($jsonFileName);
        }else{
            //获取目录结构
            $link = getDirTree(APP_ROOT);
            $link = json_encode($link);
            file_put_contents($jsonFileName,$link);
        }
        return $link;
    }

}
/** 解析路由 特定的路由访问
 * Class CorePart
 * @package MyObjSummary
 */
class CorePart extends CoreInterpreter
{
    #默认一级路由
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
        if($this->isExit)
            exit() ;
    }
}

