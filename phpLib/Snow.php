<?php


class Snow
{
	/** @var string 固定首位 */
	protected $bit_0 = "0";

	/** @var string 起始时间 ms */
	protected $start_time = '1594178723567';

	/** @var string 41位时间戳字节 */
	protected $bit_41 = "";#能够使用69年

	/** @var string 工作机器 10字节 */
	protected $bit_10 = "";

	/** @var string 工作机器-数据中心 5字节 */
	protected $bit_machine_data_center_5 = "";

	/** @var string 工作机器-工作ID 5字节 */
	protected $bit_machine_worker_5 = "";

	/** @var string 12字节符序列号 */
	protected $bit_12 = "";

	/** @var string 总字节数 */
	protected $bit_64 = "";#对应 10 进制最大为 19 位

	/** 获取ID,每秒 26 万
	 * @return float|int
	 */
	public function getId()
	{
		$this->setBit64();
		if(strlen($this->bit_64) != 64) {
			return null;
		}
		return bindec($this->bit_64);
	}

	/** 补零
	 * @param $int
	 * @return string
	 */
	protected function fillZero($int)
	{
		$zero = "";
		for ($i = 1; $i <= $int; $i ++) {
			$zero .= "0";
		}
		return $zero;
	}

	/**
	 *  set bit 0
	 */
	protected function setBit0()
	{
		$this->bit_64 = $this->bit_0;
	}

	/**
	 * set 41bit
	 */
	protected function setBit41()
	{
		list($ms, $s) = explode(" ", microtime());
		$millisecond  = intval(($s + $ms) * 1000);
		$diff         = $millisecond - $this->start_time;
		$binDiff      = decbin($diff);
		$len          = strlen($binDiff);
		$fillLen      = 41 - $len;
		$zero         = $this->fillZero($fillLen);
		$this->bit_41 = $zero . $binDiff;
		$this->bit_64 .= $this->bit_41;
	}

	/**
	 * set 10bit
	 */
	protected function setBit10()
	{
		//这里分两部分,获取当前服务器的一些信息
		//这里简单处理
		$bit10 = "";
		for ($i = 1; $i <= 10; $i++) {
			$bit10 .= rand(0, 1);
		}
		$this->bit_10 = $bit10;
		$this->bit_64 .= $this->bit_10;
	}

	/**
	 * set bit12
	 */
	protected function setBit12()
	{
		//这里是序列号,简单处理
		$bit12 = "";
		for ($i = 1; $i <= 12; $i++) {
			$bit12 .= rand(0, 1);
		}
		$this->bit_12 = $bit12;
		$this->bit_64 .= $this->bit_12;
	}

	/**
	 * 设置 64bit
	 */
	protected function setBit64()
	{
		$this->setBit0();
		$this->setBit41();
		$this->setBit10();
		$this->setBit12();
	}
}
$model = new Snow();
$id    = $model->getId();
var_dump($id);