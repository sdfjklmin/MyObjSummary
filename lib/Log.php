<?php


namespace lib;


class Log
{
    protected static $_init = null;

    protected $log_name = '1.log';

    protected function __construct($config)
    {
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function init(array $config = [])
    {
        if(!self::$_init) {
            self::$_init = new self($config);
        }
        return self::$_init;
    }

    /** 写入
     * @param $msg
     * @param string $type
     */
    private function write($msg,$type = 'info')
    {
        $format     = '['.date('Y-m-d H:i:s').'] ['.$type.'] ';
        $message    = $format.$msg.PHP_EOL;
        file_put_contents($this->log_name,$message, FILE_APPEND) ;
    }

    /** 数组连接
     * @param $msg
     * @return string
     */
    private function linkArr($msg)
    {
        $formatArr  = 'Array'.PHP_EOL;
        $formatArr .= '('.PHP_EOL;
        foreach ($msg as $k => $v) {
            $formatArr .= '    ['.$k.'] => '.$v.PHP_EOL;
        }
        $formatArr .= ')';
        $msg = PHP_EOL.$formatArr;
        return $msg;
    }

    /** 对象连接
     * @param $msg
     * @return string
     */
    private function linkObj($msg)
    {
        $formatArr  = get_class($msg).' Object'.PHP_EOL;
        $formatArr .= '('.PHP_EOL;
        foreach ((array)$msg as $k => $v) {
            $formatArr .= '    ['.$k.'] => '.$v.PHP_EOL;
        }
        $formatArr .= ')';
        $msg = PHP_EOL.$formatArr;
        return $msg;
    }

    /** 可抽离成单独处理某个类型的对象
     * @param $msg
     * @return string
     */
    private function link($msg)
    {
        switch (gettype($msg)) {
            case 'array' :
                $msg = $this->linkArr($msg);
                break;
            case 'object':
                $msg = $this->linkObj($msg);
                break;
            default:
                return $msg;
        }
        return $msg;
    }

    /** log
     * @param $msg
     */
    public function log($msg)
    {
        $msg  = $this->link($msg);
        $this->write($msg,'info');
    }

    /** error
     * @param $msg
     */
    public function error($msg)
    {
        $msg = $this->link($msg);
        $this->write($msg,'error');
    }

    /**
     * 清空
     */
    public function clear()
    {
        file_put_contents($this->log_name,'') ;
    }
}