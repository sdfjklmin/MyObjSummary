<?php
/**
* redis静态链接
*/
class RedisConect
{
	
		private static $connections = array();  //定义一个对象池
        private static $servers = array(); 		//定义redis配置文件
        public static function addServer($conf) //定义添加redis配置方法
        {
            foreach ($conf as $alias => $data){
                self::$servers[$alias]=$data;
            }
        }

        public static function getRedis($alias,$select = 0)//两个参数要连接的服务器KEY,要选择的库
        { 
        	if(!array_key_exists($alias,self::$connections)){  //判断连接池中是否存在
                $redis = new Redis();
                $redis->connect(self::$servers[$alias][0],self::$servers[$alias][1]);
                self::$connections[$alias]=$redis;
                if(isset(self::$servers[$alias][2]) && self::$servers[$alias][2]!=""){ 
                    self::$connections[$alias]->auth(self::$servers[$alias][2]);
                }
            }
            self::$connections[$alias]->select($select);
            return self::$connections[$alias];
        }

}

$conf = array( 
    'def' => [
     	'ip'	=>	'127.0.0.1' ,
     	'port'  => '3306'
    ]
);
RedisConect::addServer($conf); //添加Redis配置
$redis = RedisConect::getRedis('def'); //连接RA，使用默认0库
$redis->set('user','private');
echo $redis ->get('user');