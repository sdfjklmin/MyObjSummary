<?php
namespace php\diIoc;
//+++++++++++++++++++++++++++++++++++++++++++++
// 控制反转模式。依赖关系的控制反转到调用链的起点。
// 这样你可以完全控制依赖关系，通过调整不同的注入对象，来控制程序的行为。
// 例如 IocFoo 类用到了memcache，可以在不修改 IocFoo 类代码的情况下，改用redis。
//+++++++++++++++++++++++++++++++++++++++++++++

/** 缓存类基础接口
 * Interface CacheInterface
 */
interface CacheInterface
{
    public function get($key,$value = '');

    public function set($key,$value);
}

/** redis缓存
 * Class RedisCache
 * @author sjm
 */
class RedisCache implements CacheInterface
{
    /**
     * @var Redis
     */
    protected $redis;

    public function __construct()
    {
        //设置redis
        $this->redis = new \stdClass();
    }

    public function get($key, $value = '')
    {
        echo 'redis cache get';
    }

    public function set($key, $value)
    {
        $this->redis->set($key,$value);
    }
}

/** MemCache 缓存
 * Class MemCache
 * @author sjm
 * @package php\diIoc
 */
class MemCache implements CacheInterface
{

    /**
     * @param $key
     * @param string $value
     */
    public function get($key, $value = '')
    {
        echo 'mem cache get';
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }
}

/** 控制反转
 * Class IocFoo
 * @author sjm
 */
class IocFoo
{

    protected $cache;

    /**
     * IocFoo constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        //var_dump($cache instanceof CacheInterface);
    }


    public function get()
    {
        $this->cache->get('tt');
    }
}
$iocRedis = new RedisCache();
$icoMem   = new MemCache();
$model = new IocFoo($iocRedis);
$model->get();
