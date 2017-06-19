<?php
namespace lib;

use \Thread;

/**
 * 脚本执行-子线程
 */
class Exec extends Thread
{
    /**
     * 脚本文件
     * @var string
     */
    private $file;

    /**
     * 线程ID
     */
    public $id;

    /**
     * 线程名
     * @var string
     */
    public $name;

    /**
     * 全量间隔
     * @var integer
     */
    public $init;

    /**
     * 增量间隔
     * @var integer
     */
    public $update;

    /**
     * 是否为一次性脚本
     * @var boolean
     */
    public $isOne = false;

    /**
     * 全量需要清空的表
     * @var array
     */
    public $tbName;

    /**
     * 执行次数
     * @var integer
     */
    public $times = 0;

    /**
     * 结束标记
     * @var boolean
     */
    public $isEnd = false;

    /**
     * 开始标记
     * @var boolean
     */
    private $isStart = false;

    /**
     * 全局线程ID分配
     */
    private static $gid = 1;

    public function __construct($file)
    {
        $this->file = $file;
        $this->id   = self::$gid++;
        $this->name = basename($file, '.php');

    }

    public function run()
    {
        // 开始标记
        if (!$this->isStart) {
            $this->isStart = true;
        }
        // 加载基础库
        $this->base();
        $obj      = new Import($this->file);
        $obj->db  = new Db();
        $obj->gbd = new Db('gbd');

        // 第一次执行并初始化设置
        usleep(200000);
        $this->init($obj);

        // 循环脚本
        if (!$this->isOne) {
            // 循环时间
            $time = 1;

            // 有效循环次数
            $runtime = 1;

            $sleep_time = c('sleep_time') ?: 1;
            while (true) {

                // 退出线程
                if ($this->isEnd === true) {
                    break;
                }
                sleep(1);

                if ($time++ % $sleep_time != 0) {
                    continue;
                }

                // 检查mysql连接状态，失去连接时重连
                $obj->db->checkConnect();
                $obj->gbd->checkConnect();

                if (isset($this->init) && $runtime % $this->init == 0) {
                    // 校准次数，重新计算
                    $runtime = 0;
                    $this->init($obj);
                } else if (isset($this->update) && $runtime % $this->update == 0) {
                    $this->update($obj);
                }

                $runtime++;
            }
        }
        $this->end();
    }

    /**
     * 脚本基础库
     */
    private function base()
    {
        error_reporting(E_ERROR | E_WARNING);
        // 自动加载类
        autoload();
        // 加载配置文件
        $config = include ROOT . 'config.php';
        Conf::init($config);
    }

    /**
     * 全量操作
     */
    private function init($obj)
    {
        if ($this->tbName) {
            $obj->tb = $this->tbName . c('tmp_suffix');
        }
        $obj->startTime = 0;

        $this->initData();
        $this->task($obj);
        $this->endData();

        $this->times++;
    }

    /**
     * 增量操作
     */
    private function update($obj)
    {
        $obj->tb        = $this->tbName;
        $obj->startTime = $obj->endTime;

        $this->task($obj);

        $this->times++;
    }

    private function task($obj)
    {
        $obj->endTime = time();
        $obj->run();
        Log::br();
    }

    public function initData()
    {
        if ($this->init) {
            // 创建临时表
            $db  = new Db();
            $str = 'DROP TABLE IF EXISTS `' . $this->tbName . c('tmp_suffix') . '`;';
            $str .= 'CREATE TABLE `' . $this->tbName . c('tmp_suffix') . '` LIKE `' . $this->tbName . '`;';

            $db->exec($str);
        }
    }

    private function endData()
    {
        if ($this->init) {
            $db  = new Db();
            $str = 'DROP TABLE IF EXISTS `' . $this->tbName . '`;';
            $str .= 'ALTER TABLE `' . $this->tbName . c('tmp_suffix') . '` RENAME `' . $this->tbName . '`;';
            $tb = $db->query("SHOW TABLES LIKE '{$this->tbName}%'");
            $ct = count($tb);
            if ($ct >= 2) {
                // $s1 = microtime(true);
                $db->exec($str);
                // o(bcsub(microtime(true), $s1, 3));
            }
        }
    }

    /**
     * 判断一次性脚本是否已经执行
     */
    public function hasOne()
    {
        $db  = new Db();
        $md5 = md5_file($this->file);
        $ret = $db->query("SELECT `id` FROM `m_task_log` WHERE `file` = '{$this->name}' AND `md5` = '{$md5}'");

        if (empty($ret)) {
            $db->exec("INSERT INTO `m_task_log` (`file`, `md5`, `time`) VALUES ('{$this->name}', '{$md5}', " . time() . ")");
            return false;
        } else {
            return true;
        }
    }

    /**
     * 终止线程
     */
    public function kill()
    {
        $this->isEnd = true;
    }

    /**
     * 结束信息
     */
    public function end()
    {
        Log::write('脚本：' . $this->name . '共执行' . $this->times . '次');
        $this->isEnd = true;
    }

    /**
     * 析构-结束时标记
     */
    public function __destruct()
    {
        if ($this->isStart) {
            $this->isStart = false;
            $this->isEnd   = true;
        }
    }
}
