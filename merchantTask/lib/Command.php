<?php
namespace lib;

/**
 * 命令行类库
 */
class Command
{
    /**
     * 接收的命令
     * @var array
     */
    private static $options = [
        ['name' => 'c', 'man' => '无色模式，用于显示乱码的Windows平台'],
        ['name' => 'f', 'isopt' => true, 'optinfo' => '文件名', 'man' => '单独运行某个文件'],
        ['name' => 'h', 'man' => '帮助'],
        ['name' => 'o', 'man' => '全量数据模式'],
        ['name' => 'Y', 'man' => '-y'],
        ['name' => 'y', 'man' => '跳过确认'],
        ['name' => 'debug', 'man' => '开启调试模式，显示调试信息'],
        ['name' => 'help', 'man' => '-h'],
    ];

    /**
     * 命令
     * @var array
     */
    private static $command;

    public static function parse()
    {
        $opt_str = '';
        $opt_arr = [];
        foreach (self::$options as $o) {
            $command = $o['name'];
            if (isset($o['isopt']) && $o['isopt'] === true) {
                $command .= ':';
            }

            if (strlen($o['name']) == 1) {
                $opt_str .= $command;
            } else {
                $opt_arr[] = $command;
            }

        }
        self::$command = getopt($opt_str, $opt_arr);
    }

    public static function get($name)
    {
        if (isset(self::$command[$name]) && self::$command[$name]) {
            return self::$command[$name];
        }
    }

    public static function has($name)
    {
        return isset(self::$command[$name]);
    }

    public static function help()
    {
        foreach (self::$options as $o) {
            if (!isset($o['man'])) {
                continue;
            }
            $pre = '-';
            $len = strlen($o['name']);
            if (strlen($o['name']) > 1) {
                $len++;
                $pre .= '-';
            }
            $pre2 = str_repeat(' ', 12 - $len);

            if (isset($o['isopt']) && $o['isopt']) {
                $mblen = intval(strlen($o['optinfo']) / 2 + mb_strlen($o['optinfo']) / 2) + 2;

                $pre2 .= Log::colorString('<' . $o['optinfo'] . '>', 'dark_gray') . str_repeat(' ', 18 - $mblen);
            } else {
                $pre2 .= str_repeat(' ', 18);
            }

            o('        ' . Log::colorString($pre . $o['name'], 'cyan') . $pre2 . $o['man']);
        }
    }
}
