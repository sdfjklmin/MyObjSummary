<?php
namespace app\thinkPhp;

require 'base.php';

/**
 * Class App
 * @author sjm
 * @package app\thinkPhp
 */
class App
{
    /**
     * @var array
     */
    private $server;

    /**
     * @var Route
     */
    private $route;


    private $suffix = [];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $namespace = 'app\controller';

    /**
     * @var string
     */
    private $fixed = 'Tp';

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->initServer();
        $this->route  = new Route($this->server);
        $this->request = Container::get('request');
    }

    /**
     * init $_server
     */
    private function initServer()
    {
        $this->server = $_SERVER;
        $this->server['_frame'] = 'thinkPhp Model';
        $this->server['_frame_version'] = '1.0.1 (dev)';
    }

    public function run()
    {
        $path    = $this->route->path();
        $isClass = $this->route->parsing($path);
        if($isClass) {
            //获取规则
            $pathArr = explode('/',trim(str_replace(['_'],[''],$path),'/'));
            $class = $this->namespace.'\\'.$this->fixed.ucfirst($pathArr[0]);
            $classModel = (new Container())->invoke($class);
            $method = $pathArr[1];
            if(!method_exists($classModel,$method)) {
                echo $class.' method not exists : '.$method;exit();
            }
            try {
                $method = new \ReflectionMethod($classModel, $method);
            } catch (\ReflectionException $e) {
                echo $e->getMessage();exit();
            }
            if($method->getParameters()) {
                //多个参数一一对应
                $method->invokeArgs($classModel,[]);
            }else{
                $method->invoke($classModel);
            }
        }else{
            $suffix = $this->request->get('suffix','md');
            $filePath = APP_INIT_ROOT.$path.'.'.$suffix;
            classContent($filePath);
        }

    }
}