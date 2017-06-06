<?php
namespace lib;

use \Thread;

class Log
{
    /**
     * 前景色
     * @var array
     */
    private static $fg = [
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'brown'        => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'white'        => '1;37',
    ];

    /**
     * 背景色
     * @var array
     */
    private static $bg = [
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    ];

    private static $logs;

    // 彩色字体输出
    public static function colorString($str, $fg, $bg = null)
    {
        // 无色模式
        if (Command::has('c')) {
            return $str;
        }
        $string = "";
        if (isset(self::$fg[$fg])) {
            $string .= "\033[" . self::$fg[$fg] . "m";
        }
        if (isset(self::$bg[$bg])) {
            $string .= "\033[" . self::$bg[$bg] . "m";
        }
        $string .= $str . "\033[0m";
        return $string;
    }

    private static function L($str, $file = '')
    {
        if (empty($file)) {
            $file = (new Thread())->getCurrentThread()->name;
            $file = str_replace('.php', '', $file);
        }
        $filename = ROOT . 'Logs/' . date('Ymd') . '/' . $file . '.log';
        self::mkLogdir(dirname($filename));
        $f = fopen($filename, 'a');
        fwrite($f, $str . PHP_EOL);
        fclose($f);
    }

    /**
     * 日志写入
     */
    public static function write($str, $type = 'info', $file = '')
    {
        $tid                         = (new Thread())->getCurrentThread()->id;
        self::$logs[$tid]['hasLogs'] = true;
        $str                         = '[' . date('Y-m-d H:i:s') . '] <' . strtoupper($type) . '> ' . $str;
        self::L($str, $file);
    }

    public static function info($str, $file = '')
    {
        self::write($str, 'info', $file);
    }

    public static function error($str, $file = '')
    {
        self::write($str, 'error', $file);
        self::write($str,'error','error');
        o(self::colorString((new Thread())->getCurrentThread()->name . '  错误', 'red'));
        o($str);
    }

    public static function br()
    {
        $tid = (new Thread())->getCurrentThread()->id;
        if (isset(self::$logs[$tid]['hasLogs']) && self::$logs[$tid]['hasLogs'] == true) {
            self::L('');
            self::$logs[$tid]['hasLogs'] = false;
        }
    }

    /**
     * 自动创建文件夹
     */
    private static function mkLogdir($dir)
    {
        return file_exists($dir) or self::mkLogdir(dirname($dir)) and mkdir($dir);
    }
}
