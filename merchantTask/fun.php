<?php
use lib\Conf;

/**
 * 输出内容
 */
function o($str = '')
{
    // win平台编码转换
    if (strstr(PHP_OS, 'WIN')) {
        $str = iconv('UTF-8', 'gbk', $str);
    }
    echo $str . PHP_EOL;
}

/**
 * 获取配置文件
 */
function c($key = '')
{
    return Conf::get($key);
}

/**
 * 自动加载类
 */
function autoload()
{
    spl_autoload_register(function ($class) {
        $file = ROOT . str_replace('\\', '/', $class) . '.php';
        include $file;
    });
}
