<?php

//---------------------------------
// String
// get ->  get key
//
//---------------------------------
class RedisCommand
{
	/**
	 * @var Redis|null
	 */
	private $_server = null;

	public function __construct($server = null,$conf = [])
	{
		if(!$server) {
			$this->_server = new Redis();
			$this->_server->connect('127.0.0.1');
		}
	}

	/** string get
	 * @param $key
	 * @param string $default
	 * @return bool|mixed|string
	 */
	public function get($key,$default = '')
	{
		$value = $this->_server->get($key);
		if($value === false) {
			return $default;
		}else{
			return $value;
		}
	}

	/** 扩展版 string set
	 * @param $key
	 * @param $value
	 * @param null $timeout 默认秒
	 * @param null $nxXx
	 *        NX -- Only set the key if it does not already exist.
	 *        XX -- Only set the key if it already exist.
	 * @return mixed
	 */
	public function set($key,$value,int $timeout = null,$nxXx = null)
	{
		if($timeout) {
			if ($nxXx) {
				$ret = $this->_server->rawCommand('set',$key,$value,'ex',$timeout,$nxXx);
			}else{
				$ret = $this->_server->rawCommand('set',$key,$value,'ex',$timeout);
			}
		}elseif($nxXx) {
			if ($timeout) {
				$ret = $this->_server->rawCommand('set',$key,$value,'ex',$timeout,$nxXx);
			}else{
				$ret = $this->_server->rawCommand('set',$key,$value,$nxXx);
			}
		}else{
			$ret = $this->_server->rawCommand('set',$key,$value);
		}
		return $ret;
	}


	public function listLPush($key,$value)
	{
		$t = $this->_server->lPush($key,$value);
		var_dump($t);exit();
	}

}