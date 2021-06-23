<?php
namespace MyObjSummary\php\designMode ;
echo <<<DES
    策略模式 : 定义一系列算法,把它们一个个封装起来,并且使它们可相互替换,使用得算法的变化可独立于使用它的客户
DES;
/**
 * 策略模式
 *
 * 定义一系列算法,把它们一个个封装起来,并且使它们可相互替换,使用得算法的变化可独立于使用它的客户
 *
 */

/** 缓存 算法的封闭
 * Class CacheAbstract
 * @package MyObjSummary\php\designMode
 */
abstract class CacheAbstract
{
    public $_data = [] ;#实际操作为NoSql缓存
    abstract public function get($key) ;
    abstract public function set($key,$value);
    abstract public function del($key);
}

/** 缓存 算法的封闭
 * Interface CacheTable
 * @package MyObjSummary\php\designMode
 */
interface CacheTable
{
    /** 获取
     * @param $key
     * @return mixed
     */
    public function get($key);

    /** 设置
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key,$value);

    /** 删除
     * @param $key
     * @return mixed
     */
    public function del($key);
}

/** 不使用缓存
 * Class NoCache
 * @package MyObjSummary\php\designMode
 */
class NoCache extends CacheAbstract
{
    public function __construct(){
        echo "Use NoCache<br/>";
    }
    public function get($key)
    {
       return false ;
    }

    public function set($key,$value)
    {
        return false;
    }

    public function del($key)
    {
        return false;
    }
}

// 文件缓存
class FileCache extends CacheAbstract
{
    public function __construct()
    {
        echo "Use FileCache<br/>";
        // 文件缓存构造函数
    }

    public function get($key)
    {
        if(isset($this->_data[$key])) {
            return $this->_data[$key] ;
        }
        return false;

    }

    public function set($key,$value)
    {
        $this->_data[$key] = $value ;
        return true ;
    }

    public function del($key)
    {
        unset($this->_data[$key]) ;
        return true ;
    }
}

/** 策略类
 * Class Model
 * @package MyObjSummary\php\designMode
 */
class Model
{
    public $_cache;

    /** 默认无缓存策略
     * Model constructor.
     */
    public function __construct()
    {
        $this->_cache = new NoCache();
    }

    /** 动态更改策略
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->_cache = $cache;
    }
}

/** 测试
 * Class PorductModel
 * @package MyObjSummary\php\designMode
 */
class PorductModel extends Model
{

}

//无缓存
$mdlProduct = new PorductModel();
var_dump($mdlProduct->_cache->set('test','abc')) ;
var_dump($mdlProduct->_cache->get('test')) ;
var_dump($mdlProduct) ;

//改变缓存策略 有缓存
$mdlProduct->setCache(new FileCache());
var_dump($mdlProduct->_cache->set('test','abc')) ;
var_dump($mdlProduct->_cache->get('test')) ;
var_dump($mdlProduct) ;
