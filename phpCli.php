#!/usr/local/bin/php -Cq
<?php
//1.头部为 Hash-Bang 声明
//将脚本放置 linux 系统 默认shell执行位置 /usr/local/bin
//文件名(一般无后缀)就是命令名称 phpCli 对应执行命令为 /usr/local/bin/php -Cq /usr/local/bin/phpCli 参数输入
//脚本操作比较大时建议 用后即焚 (关闭文件操作符,清空数组,关闭连接等)
//eg : phpCli test ; 此时 $argv[0] = '/usr/local/bin/php' ;(当PHP最后开始运行是,它会导入 ./phpCli 和 输入参数 到 $argv 中)

//exec('docker ps');
# 基本参数 入口文件
$index = $argv[0] ;
array_shift($argv);

if(empty($argv)) {
    //使用说明
    print 'Usage: phpCli [-vh] <params>'."\n";
    print 'Options:'."\n";
    print '         -v, Show phpCli version'."\n";
    print '         -h, Display this help'."\n";
    exit();
}


//2.用户输入,标准输入在 PHP流的 STDIN 中,或者 unix风格的 '终端输入' 设备 /dev/tty 中获得
//$message = trim(fgets(STDIN));
//var_dump($message);

//3.解析命令行选项  PEAR 提供了 Console_Getopt包可以同时支付简短和长格式(GNU风格)的选项.
//默认是与PHP绑定安装的,除非你关闭了PEAR
//也可以自行定义
switch ($argv[0]) {
    case '-h':
    case '-help':
        echo '这是帮助命令行选项';
        break;
    case '-v':
        echo 'phpCli V.0.1';exit();
        break;
    default:
        echo "Run 'phpCli -help' for more information on a command" ;
        exit();
}

//4.良好的习惯
//使用信息
//退出代码
//错误信息
//大多数都是用 一个简短信息来响应 -h | -help

//5.进程控制(546)
//进程概念
//Forking概念

/** pcntl_fork — 在当前进程当前位置产生分支（子进程）。
 * 译注：fork是创建了一个子进程，父进程和子进程 都从fork的位置开始向下继续执行，
 * 不同的是父进程执行过程中，得到的fork返回值为子进程 号，而子进程得到的是0。 */

//$pid = pcntl_fork();
//父进程和子进程都会执行下面代码
/*if ($pid == -1) {
    //错误处理：创建子进程失败时返回-1.
    die('could not fork');
} else if ($pid) {
    //父进程会得到子进程号，所以这里是父进程执行的逻辑
    echo 'this is parent test' ;
    pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
} else {
    //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
    echo 'this is son test';
}*/





