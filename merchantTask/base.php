<?php
// 限制CLI模式
if (PHP_SAPI != 'cli') {
    echo 'This script can only be run in CLI mode' . PHP_EOL;
    exit;
}

// 修改进程名
cli_set_process_title('merchantTask');

// 加载配置文件
$config = include ROOT . 'config.php';
include ROOT . 'lib/Conf.php';
lib\Conf::init($config);

// 引入公共方法文件
include ROOT . 'fun.php';

// 进程锁
$f = fopen(c('lock_file'), 'a');
if (false == flock($f, LOCK_EX | LOCK_NB)) {
    echo '错误：[ERROR] 服务启动失败，该服务正在运行，不能重复启用！' . PHP_EOL;
    exit;
}

// 自动加载类
autoload();
