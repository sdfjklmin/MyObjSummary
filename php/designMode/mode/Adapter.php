<?php
/**
 * 适配器模式
 *
 * 将一个类的接口转换成客户希望的另外一个接口,使用原本不兼容的而不能在一起工作的那些类可以在一起工作
 */
echo <<<DES
    <h2>适配器模式 : 将一个类的接口转换成客户希望的另外一个接口,使用原本不兼容的而不能在一起工作的那些类可以在一起工作</h2>
DES;

/** 原有的类型
 * Class OldCache
 */
class OldCache
{
    public function __construct()
    {
        echo "OldCache construct<br/>";
    }

    public function store($key,$value)
    {
        echo "OldCache store<br/>";
    }

    public function remove($key)
    {
        echo "OldCache remove<br/>";
    }

    public function fetch($key)
    {
        echo "OldCache fetch<br/>";
    }
}

/** 转换接口
 * Interface Cacheable
 */
interface Cacheable
{
    public function set($key,$value); //对应原有的 store
    public function get($key); //对应原有的 fetch
    public function del($key); //对应原有的 remove
}

/** 原有类型转换接口
 * Class OldCacheAdapter
 */
class OldCacheAdapter implements Cacheable
{
    public $_oldCache = null;
    private $_cache = null;
    public function __construct()
    {
        $this->_oldCache = new OldCache();
        $this->_cache = new OldCache();
    }

    public function set($key,$value)
    {
        return $this->_cache->store($key,$value);
    }

    public function get($key)
    {
        return $this->_cache->fetch($key);
    }

    public function del($key)
    {
        return $this->_cache->remove($key);
    }
}

// 通过接口转换 只使用 原来的功能 不使用原有的方法名
$objCache = new OldCacheAdapter();
$objCache->set("test",1);
$objCache->get("test");
$objCache->del("test");

//对比
$objCache->_oldCache->store('test',1);
$objCache->_oldCache->fetch('test');
$objCache->_oldCache->remove('test');