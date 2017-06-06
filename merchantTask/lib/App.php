<?php
namespace lib;

/**
 * 项目控制-主线程
 */
class App
{
    private static $confMd5;
    private static $thread;
    private static $times;
    private static $mem;

    public static function run()
    {
        // 解析命令
        Command::parse();

        // 异常处理
        if (!Command::has('debug')) {
            set_exception_handler(function ($e) {
                Log::error($e->getMessage());
            });
        }

        // 帮助
        if (Command::has('h') || Command::has('help')) {
            Command::help();
            exit;
        }

        // 确认框
        if (!Command::has('f') && !Command::has('y') && !Command::has('Y')) {
            o('确定执行所有脚本？ ' . Log::colorString('Y', 'green') . '/' . Log::colorString('N', 'red'));
            $is_y = trim(fgets(STDIN));
            if ($is_y != 'y' && $is_y != 'Y') {
                exit;
            }
        }

        self::$mem     = memory_get_usage();
        self::$confMd5 = md5_file(ROOT . 'config.php');
        $files         = glob(ROOT . 'exec/*.php');

        // -f 模式
        if (Command::has('f')) {
            $cf = Command::get('f');
        }

        $isrun = false;
        // 循环多线程
        foreach ($files as $k => $f) {
            if (isset($cf)) {
                $bname = basename($f, '.php');
                // -f 模式
                if (strstr($bname, $cf)) {
                    if ($bname == $cf) {
                        $isrun = true;
                    } else {
                        o('你要执行的脚本是 ' . Log::colorString($bname, 'cyan') . ' 吗? ' . Log::colorString('Y', 'green') . '/' . Log::colorString('N', 'red'));
                        $is_y = trim(fgets(STDIN));
                        if ($is_y != 'y' && $is_y != 'Y') {
                            exit;
                        }
                        $isrun = true;
                    }
                } else if (basename($cf, '.php') == $bname) {
                    $isrun = true;
                }

                if (!$isrun) {
                    continue;
                }
            }

            self::$thread[$k] = new Exec($f);
            self::$thread[$k]->start();
            if ($isrun) {
                break;
            }
        }
        if (isset($cf) && !$isrun) {
            o(Log::colorString('错误: 找不到脚本:' . Command::get('f'), 'red'));
            exit;
        }
        self::watchs();
    }

    /**
     * 检测运行状态信息
     */
    private static function watchs()
    {
        $r = 1;
        while (true) {
            // 检查版本号变更
            self::checkConf();
            foreach (self::$thread as $k => $t) {
                if ($t->isEnd) {
                    if (Command::has('debug')) {
                        o(Log::colorString('退出脚本', 'red') . ' ' . Log::colorString($t->name, 'cyan') . '，已执行 ' . Log::colorString($t->times, 'yellow') . ' 次。');
                    }
                    unset(self::$thread[$k]);
                    continue;
                }
                if (Command::has('debug')) {
                    if (!isset(self::$times[$t->id])) {
                        self::$times[$t->id] = $t->times;
                    }
                    if (self::$times[$t->id] != $t->times) {
                        self::$times[$t->id] = $t->times;
                        $str                 = '脚本：' . Log::colorString($t->name, 'cyan')
                        . '  执行第 ' . Log::colorString($t->times, 'yellow') . ' 次';
                        o($str);
                    }
                }
            }
            if (empty(self::$thread)) {
                break;
            }
            if ($r % 10 == 0) {
                // o('MEM: ' . (memory_get_usage() - self::$mem) . ' bytes');
            }
            $r++;

            usleep(100000);
        }
    }

    /**
     * 检查配置变更
     */
    private static function checkConf()
    {
        $conf_md5 = md5_file(ROOT . 'config.php');
        if ($conf_md5 != self::$confMd5) {
            o(Log::colorString('配置变更，系统退出...', 'red'));
            foreach (self::$thread as $t) {
                $t->kill();
            }
        }
    }
}
