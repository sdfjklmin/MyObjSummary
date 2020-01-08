<?php

/**
 * Class redisConcurrent
 */
class RedisConcurrent
{

    /** lock key
     * @var string
     */
    private $_lockKey = 'redis_lock';

    /** Redis Class
     * @var Redis
     */
    private $_redis ;

    /** ip
     * @var mixed|string
     */
    private $ip ='127.0.0.1' ;

    /** port
     * @var string
     */
    private $port = '6379' ;

    /** init redis connect
     * redisConcurrent constructor.
     * @param array $config
     */
    public function __construct( $config = [] )
    {
        if(!empty($config)) {
            if(isset($config['ip'])) {
                $this->ip = $config['ip'];
            }
            if(isset($config['port'])){
                $this->ip = $config['port'];
            }
        }
        /**
         * Redis连接信息可以用原生,也可以用其它的框架集成
         */
        $this->_redis = new Redis();
        $this->_redis->connect($this->ip,$this->port);

    }


    /** 锁定
     * @param int $intTimeout 默认过期时间(避免死锁)
     * @return bool
     */
    private function lock($intTimeout = 8) {
        #新版set,已经集成了大多数集成操作
        $strRet   = $this->_redis->set($this->_lockKey, time().rand(10000,99999).rand(1000,9999).rand(100,999), 'ex', $intTimeout, 'nx');
        if($strRet) {
            return true;
        }else{
            return false;
        }
    }


    /** 解锁
     * @throws \Exception
     */
    private function unlock()
    {
        $strRet   = $this->_redis->del($this->_lockKey);
        if($strRet) {
            return true;
        }else{
            if($this->_redis->get($this->_lockKey)) {
              return false ;
            }else{
              return false ;
            }
        }
    }

    /**
     * 业务相关的key,可以是库存,物品数等
     */
    const ORDER_KEY = 'order_num';

    /**
     * 用户相关的key
     */
    const USER_KEY = 'user_num';

    /** Redis下单
     * @param int $num 下单个数
     * @param string $userId 用户ID
     *
     * 场次是为了方便异常处理,方便数据查找
     * @param string $bout 商品场次 => order_num:1 , order_num:2
     * @return bool
     * @throws Exception
     */
    public function order( string $userId ,string $bout = '1' ,int $num = 1)
    {
        $orderKey = self::ORDER_KEY.':'.$bout ;
        $userKey  = self::USER_KEY.':'.$bout ;
        //此方法不具备原子性 并发处理是不能做条件判断
        //$len = $this->_redis->llen();
        #实际为n+1次触发完结,这里只做Redis自减
        $check = $this->_redis->lpop($orderKey);
        if(!$check){
            #当前order_num已经为0!
            //自动补货为 100 ,$bout有一定的处理规则,不能乱传
            self::autoBuild(100,$bout);
            return false ;
        }
        //特殊处理,避免n+1次的情况
        $len = $this->_redis->llen($orderKey) ;
        if($len == 0) {
            //自动补货为 100 ,$bout有一定的处理规则,不能乱传
            self::autoBuild(100,$bout);
            return false ;
        }
        //添加用户数据
        $result = $this->_redis->lpush($userKey,$userId);
        if($result){
            return true ;
        }else{
            return false ;
        }
    }


    /** 失败处理
     * #增加当前库存
     * #减少用户库存
     * @param string $num
     * @param string $userId
     * @param $bout
     * @return bool
     * @throws Exception
     */
    public function _out(string $num,string $userId,$bout)
    {
        #并发参与时,总库存有5个,一共10次请求,成功5次,退款1次,实际库存1次
        #失败处理时和_buildOrder加上同一把锁,避免更新下次库存时,上次库存累积
        #_out 和 _buildOrder 同时只能有一个在执行,不然锁会报错,也避免下不必要的死锁
        self::lock();
        //减用户库存
        $user = $this->_redis->lpop(self::USER_KEY.':'.$bout);
        if(!$user) {
           return false ;
        }
        //增加商品库存
        $all  = $this->_redis->lpush(self::ORDER_KEY.':'.$bout,$userId);
        if(!$all) {
           //TODO::这里需要做容错处理,即再商品库存增加失败时,做记录
           return false ;
        }
        self::unlock();
    }


    /** 自动构建
     * @param int $num
     * @param $bout
     * @throws Exception
     */
    private  function autoBuild( int $num ,$bout)
    {
        $a = $this->_redis->get(self::ORDER_KEY.':'.$bout);
        if(!$a) {
            //库存已完结
            $this->_buildOrder(self::ORDER_KEY.':'.$bout,$num);
        }
    }


    /** 物品库存规则
     * @param $orderKey
     * @param $num
     * @return string
     * @throws Exception
     */
    private function _buildOrder($orderKey,$num)
    {
        //锁定
        self::lock();
        $ckNum = '0' ;#Redis操作后返回为string类型
        #总数 与$ckNum要相同类型 不然可能会出现判断错误
        if($num < 0) {
            throw new \Exception('商品数量错误!');
        }
        $beforeNum = 0 ;
        //上一次库存判断 ()
        if($beforeNum > 0) {
            throw new \Exception('商品未售罄!');
        }
        //当前库存判断
        $length = $this->_redis->llen($orderKey);
        if($length > 0) {
            throw new \Exception('商品已经存在!');
        }
        //生成当前库存
        while ($ckNum < $num) {
            if($ckNum == $num) {
                break ;
            }else if($ckNum > $num){
                break ;
            }else{
                $ckNum = $this->_redis->lpush($orderKey,1) ;
                if($ckNum >=$num) {
                    break ;
                }
            }
        }
        //并发时 循环成功 redis不一定成功
        /*for ($i=1;$i<=$num ;$i++) {
            $ckNum = $this->_redis->lpush(self::$_allCoin.self::getNum().':'.$coin,1);
            if($ckNum >= $num) {
                break ;
            }
        }*/
        //解锁
        self::unlock();
        return $ckNum ;
    }
}
