<?php

namespace app\vdsns\log;
use app\vdsns\model\v2\VdLogInfo; //入库模型
use think\App;
use think\Exception;
use think\log\driver\File;

//---------------------------------------
// log.php
/*return [
	// 日志记录方式，内置 file socket 支持扩展
	'type'        => '\\app\\vdsns\\log\\LogFileWithDb', // 对应 LogFileWithDb的命令空间
	//'type'        => 'File',
	// 日志保存目录
	'path'        => '',
	// 日志记录级别
	'level'       => [],
	// 单文件日志写入
	'single'      => false,
	// 独立日志级别
	'apart_level' => [],
	// 最大日志文件数量
	'max_files'   => 0,
	// 是否关闭日志写入
	'close'       => false,
];*/
//---------------------------------------
/**自定义日志驱动
 * Class VdLog Driver
 * @author sjm
 * @package app\vdsns\log
 */
class LogFileWithDb extends File
{
	/**
	 * 对应的level
	 */
	const DE_EMERGENCY = 'emergency';
	const DE_ALERT     = 'alert';
	const DE_CRITICAL  = 'critical';
	const DE_ERROR     = 'error';
	const DE_WARNING   = 'warning';
	const DE_NOTICE    = 'notice';
	const DE_INFO      = 'info';
	const DE_DEBUG     = 'debug';
	const DE_SQL       = 'sql';

	/**
	 * @var bool
	 */
	protected $save_db = true;

	/**
	 * VdLog constructor.
	 * @param mixed ...$args
	 */
	public function __construct(App $app,$config = [])
	{
		parent::__construct($app,$config);

		//cli模式日志不入库
		if (PHP_SAPI == 'cli') {
			$this->save_db = false;
		}
	}

	/**
	 * @param array $log
	 * @param bool $append
	 * @return bool
	 */
	public function save(array $log = [], $append = false)
	{
		//TODO::自定义写入规则
		//$this->intoSave($log);
		return parent::save($log,$append);
	}

	/**
	 * @param array $message
	 * @param string $destination
	 * @param bool $apart
	 * @param bool $append
	 * @return bool
	 */
	public function write($message, $destination, $apart = false, $append = false)
	{
		$this->intoWrite($message, $destination, $apart, $append);
		return parent::write($message, $destination, $apart, $append);
	}

	/**
	 * @param $log
	 */
	protected function intoSave($log)
	{
		$this->intoDB($log);
	}

	/**
	 * @param $message
	 * @param $destination
	 * @param bool $apart
	 * @param bool $append
	 */
	protected function intoWrite($message, $destination, $apart = false, $append = false)
	{
		$log = [
			'message'     => $message,
			'destination' => $destination,
			'apart'       => $apart,
			'append'      => $append,
		];
		//TODO::自定义写入规则
		if(isset($message['error'])) {
			//只记录错误
			$this->intoDB($log);
		}
	}

	/**
	 * @param $log
	 */
	protected function intoDB($log)
	{
		if($this->save_db) {
			$ruleLog = $this->dbLogRule($log);
			if($ruleLog) {
				try {
					VdLogInfo::create($this->dbLogRule($log));
				}catch (Exception $exception) {

				}
			}
		}
	}

	/** 规则
	 * @param $log
	 * @return array
	 */
	protected function dbLogRule($log)
	{
		//TODO::定义模型规则
		$data = [
			'logs'        => json_encode($log, true),
			'create_time' => date('Y-m-d H:i:s'),
		];
		return $data;
	}
}