<?php


namespace lib;


/** 时间差异类
 * Class DateTimeDiff
 * @author sjm
 * @package lib
 */
class DateTimeDiff
{
	/** 固定时间格式,大写为固定两位值(01),小写(1)
	 * @var array
	 */
	private static $_format = ['y','m','d','h','i','s'];

	/**
	 * @var string
	 */
	private static $_onlineTip = '当前在线';

	/** 年
	 * @param $diff
	 * @return string
	 */
	protected static function yFormat($diff)
	{
		return '很久之前';
	}

	/** 月
	 * @param $diff
	 * @return string
	 */
	protected static function mFormat($diff)
	{
		return "{$diff}个月之前";
	}

	/** 日
	 * @param $diff
	 * @return string
	 */
	protected static function dFormat($diff)
	{
		return "{$diff}天之前";
	}

	/** 时
	 * @param $diff
	 * @return string
	 */
	protected static function hFormat($diff)
	{
		return "{$diff}小时之前";
	}

	/** 分
	 * @param $diff
	 * @return string
	 */
	protected static function iFormat($diff)
	{
		if($diff > 15) {
			return "{$diff}分钟之前";
		}else{
			return self::$_onlineTip;
		}
	}

	/** 秒
	 * @param $diff
	 * @return string
	 */
	protected static function sFormat($diff)
	{
		return self::$_onlineTip;
	}

	/** 入口
	 * @param $time int
	 * @return string
	 */
	public static function diff(int $time)
	{
		try {
			$datetime1 = new \DateTime();
			$datetime2 = new \DateTime(date('Y-m-d H:i:s', $time));
		} catch (\Exception $e) {
			//异常处理
			return '--';
		}
		$interval = $datetime1->diff($datetime2);
		foreach (self::$_format as $item) {
			$diff = $interval->format("%$item");
			if($diff > 0) {
				$method = "{$item}Format";
				return self::$method($diff);
			}
		}
		//默认值,当时间相同时不会走上面的流程
		return self::$_onlineTip;
	}
}