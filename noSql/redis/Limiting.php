<?php

/** Redis 限流
 * Class Limiting
 * @author sjm
 */
class Limiting
{
	/**
	 * @var Redis
	 */
	private $_redis;

	private $limit = 5;

	private $tmp_key = 'user_limit';

	public function __construct()
	{
		$this->_redis = new Redis();
		$this->_redis->connect('127.0.0.1');
		$this->_redis->select(15);
	}

	/**
	 * 单一string只记录了对应的次数,却没有记录请求时间
	 * @error
	 * 	第一个10秒: 在一秒时请求一次,在最后几秒请求4次.
	 *  第二个10秒: 在前几秒请求4次.
	 *  结合起来 最高就请求了 8次 已经 大于 限制次数了
	 */
	public function typeString()
	{
		$check = $this->_redis->get($this->tmp_key);
		if($check) {
			if($check > $this->limit ) {
				exit('超过次数速率');
			}else{
				$this->_redis->incr($this->tmp_key);
			}
		}else{
			$this->_redis->incr($this->tmp_key);
			$this->_redis->expire($this->tmp_key,10);
		}
		var_dump($check);
	}

	/**
	 * 限制次数和时间
	 */
	public function typeList()
	{
		$lens = $this->_redis->lLen($this->tmp_key);
		if($lens) {
			if($lens > $this->limit) {
				exit('超过次数咯');
			}else{
				$lastTime = $this->_redis->lIndex($this->tmp_key,0);
				if(time() - $lastTime < 1) {
					exit('请求太快咯');
				}
				$this->_redis->lPush($this->tmp_key,time());
			}
		}else{
			$this->_redis->lPush($this->tmp_key,time());
			$this->_redis->expire($this->tmp_key,10);
		}
		var_dump('次数'.$lens);
	}
}

(new Limiting())->typeList();