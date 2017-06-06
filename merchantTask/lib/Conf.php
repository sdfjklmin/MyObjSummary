<?php
namespace lib;

/**
 * 配置文件
 */
class Conf
{
    private static $config = [];

    public static function init($config)
    {
        self::$config = $config;
    }

    public static function get($key = '')
    {
        if (empty($key)) {
            return self::$config;
        } else {
            return self::$config[$key];
        }
    }
}
