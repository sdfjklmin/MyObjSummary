<?php 
/**
* YAR FOR RPC SERVCE
*/

class YarIndex
{
	/**
    * Yar 服务端单一入口
    * $params action params
    * $eg: YarIndex()->index('Test/index',$params)
    */
    public function index()
    {
    	if(2 != count(func_get_args())) {
            throw new Exception("the request must tow params");
        }
        $data    = func_get_args() ;
        $actions = explode('/',ucfirst($data[0]))   ;
        $params  = $data[1] ;
       /* if(!file_exists('./'.$actions[0].'.php')) {
            throw new Exception("not find ".'./'.$actions[0].'.php')." file");
        }*/ 
        require_once('./'.$actions[0].'.php');
        if(!class_exists($actions[0])) {
            throw new Exception($actions[0]." not find ");
        }
        if(!in_array($actions[1],get_class_methods($actions[0]))) {
            throw new Exception($actions[1]." not find ");
        }
       
        $classData = new $actions[0];
        $action = $actions[1] ;
        return $classData->$action($params);
    }

    /**
    * Yar 测试
    * $params action params
    */
    public static function test()
    {
        echo "test";
    }

    /**
    * Yar 测试二
    * $params action params
    */
    public function testTwo()
    {
        echo "testTwo";
    }
}

//判断扩展是否存在
if (!extension_loaded('yar')) {
    throw new Exception('not support yar');
}
$service = new Yar_Server(new YarIndex());
$service->handle();
