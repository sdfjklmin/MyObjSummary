<?php
#观察者模式
#定义对象间的一种 一对多 的依赖关系,
#以便当一个对象的状态发生改变时,所有依赖于它的对象都得到通知并自动刷新 
#能够便利地创建查看目标对象状态的对象,并且提供与核心对象非耦合的指定功能 
#插件系统
namespace MyObjSummary\php\designMode ;
/** 观察者模式类
 *  只用于继承
 * Class Observerable
 */
abstract class Observerable
{ 
	private $_observers = array();

    /** 注册
     * @param $observer
     */
	public function registerObserver($observer) 
	{ 
		$this->_observers[] = $observer; 
	}

    /** 移除
     * @param $observer
     */
	public function removeObserver($observer) 
	{ 
		$key = array_search($observer,$this->_observers); 
		if(!($key === false)) 
		{ 
		    unset($this->_observers[$key]);
		} 
	}

    /**
     * 全体更新
     */
	public function notifyObservers() 
	{ 
		foreach($this->_observers as $observer) 
		{
		    if($observer instanceof Observer) $observer->update($this);
		} 
	}

    /**
     * 全体移除
     */
    public function remove()
    {
        $this->_observers = [] ;
    }
}

/** update observe
 * Interface Observer
 */
interface Observer 
{ 
	public function update($observer); 
}

/**  show some info
 * Interface DisplayElement
 */
interface DisplayElement 
{ 
	public function display(); 
} 

// -- 实例类定义

/** 消息类型
 *  体育消息和本地消息
 * @action 设置消息和获取消息
 * Class NewsObserverable
 */
class NewsObserverable extends Observerable 
{ 
	private $_sports_news; #体育消息
    private $_local_news;  #本地消息
    private $_other_news;  #本地消息

    /** 逐个清空
     * 重置父类
     * @param $observer
     */
    public function removeObserver($observer)
    {
        parent::removeObserver($observer);
        $this->_remove() ;
    }

    /**
     * 一键清空
     */
    public function removeAll()
    {
        $this->remove() ;
        $this->_remove() ;
    }

    /**
     * 子类
     */
    private  function _remove()
    {
        $this->remove();
        $this->_local_news  = null;
        $this->_sports_news = null ;
        $this->_other_news  = null ;
    }

    /** 全局通知
     * @param $data
     */
    public function commonSet($data)
    {
        $this->_local_news  = $data;
        $this->_sports_news = $data ;
        $this->_other_news  = $data ;
        $this->notifyObservers() ;
    }

    /*********************体育设置*******************/
    /** 设置体育消息
     * @param $data
     */
    public function setSportsNews($data)
	{ 
		$this->_sports_news = $data; 
		$this->notifyObservers(); 
	}

    /** 获取体育消息
     * @return mixed
     */
	public function getSportsNews() 
	{ 
		return $this->_sports_news; 
	}


    /*********************本地设置*******************/
    /** 设置本地消息
     * @param $data
     */
	public function setLocalNews($data)
	{ 
		$this->_local_news = $data; 
		$this->notifyObservers(); 
	}

    /** 获取本地消息
     * @return mixed
     */
	public function getLocalNews()
	{
		return $this->_local_news;
	}

    /*********************其他设置*******************/
    /** 设置消息
     * @param $data
     */
    public function setOtherNews($data)
    {
        $this->_other_news = $data;
        $this->notifyObservers();
    }

    /** 获取消息
     * @return mixed
     */
    public function getOtherNews()
    {
        return $this->_other_news;
    }


}

/** 体育消息
 * Class SportsNews
 */
class SportsNews implements Observer,DisplayElement 
{ 
	private $_data = null;
	public function update($observer)
	{
        /**
         * @var $observer NewsObserverable
         */
		if($this->_data != $observer->getSportsNews())
		{ 
			$this->_data = $observer->getSportsNews();
			$this->display(); 
		} 
	} 

	public function display() 
	{ 
		echo $this->_data.date("Y-m-d H:i:s")."<br/>";
	} 
}

/** 本地消息
 * Class LocalNews
 */
class LocalNews implements Observer,DisplayElement 
{ 
	private $_data = null; 
	public function update($observer) 
	{
        /**
         * @var $observer NewsObserverable
         */
		if($this->_data != $observer->getLocalNews()) 
		{ 
			$this->_data = $observer->getLocalNews(); 
			$this->display(); 
		} 
	} 

	public function display() 
	{ 
		echo $this->_data.date("Y-m-d H:i:s")."<br/>"; 
	} 
}

/** 其他消息
 * Class OtherNews
 */
class OtherNews implements Observer,DisplayElement
{
    private $_data = null;
    public function update($observer)
    {
        /**
         * @var $observer NewsObserverable
         */
        if($this->_data != $observer->getOtherNews())
        {
            $this->_data = $observer->getOtherNews();
            $this->display();
        }
    }
    public function display()
    {
        echo $this->_data.date("Y-m-d H:i:s")."<br/>";
    }
}

//主体
$objObserver = new NewsObserverable();

//多个对应
$local = new LocalNews(); 
$sports = new SportsNews(); 
$other = new OtherNews();

//注册 将多个消息注册到基类中
$objObserver->registerObserver($local);
$objObserver->registerObserver($sports);
$objObserver->registerObserver($other);

//一键更改 主体中提供一个全体修改的方法
$objObserver->commonSet('这是个全局消息!') ;
var_dump($objObserver) ;

//一键清空 后 需要再次注册
/*$objObserver->removeAll();
var_dump($objObserver) ;*/

//设置消息  将基类中的注册对象逐个更新 数据
$objObserver->setLocalNews("本地消息 one ");
$objObserver->setSportsNews("体育消息 one ");
$objObserver->setOtherNews("其他消息 one ");
var_dump($objObserver) ;

//移除 清空消息内容
$objObserver->removeObserver($sports);
$objObserver->removeObserver($local);
$objObserver->removeObserver($other);

var_dump($objObserver) ;
