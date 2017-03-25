<?php
# 模式限定
if (PHP_SAPI != 'cli') {
    echo '只能在CLI命令行模式运行' ;
    exit;
}
# 基本参数 入口文件
$index = $argv[0] ;
array_shift($argv);

if(!$argv) {
    echo "正确格式:
         php $index [分钟m|小时h]";
         style();
}
# 执行参数
$v  = $argv[0];
$e = substr($v, -1);
# 默认时间
$s = 1 ;
$tip = '' ;
if(in_array($e,array('m','M','h','H'))) {
    $v = rtrim($v, $e);
    # 判断数字
    $check = preg_replace("/([0-9])_*,*/" , false , $v);
    if($check) {
        echo "时间不对,请输入数字" ;
        style();
    }
    # 时间类型
    if( ($e=='m') || ($e=='M')) {
        $s      =   $v*60;
        $tip    =   '分钟';
    }else if(($e=='h') || ($e=='H')) {
        $s      = $v*60*60;
        $tip    =   '小时';
    }else{
        echo "时间格式不对,请以m或者h结尾";
        style();
    }
}else{
    style('时间格式不对,请以m或者h结尾');
}

# 成功提示
echo "成功开启休息功能:
            您设置的休息间隔为 $v $tip " ;
# 自定义配置
$conf = array(
    # 时钟路径
    'jump'=>[
        'request'=>'http://',
        'host'   =>'127.0.0.1',
        'dir'    =>'/Zin/time/index.php'
    ],
    # 时间设置
    'time'=>[
        'r'=> 1.5 ,  # 休息间隔
        't'=> 60 ,   # 单位 60秒
    ],

);
# 消息提醒
$msg = [
    '哎,好累哦,该休息休息了',
    '眼睛累了,让它休息休息吧',
    '这么久了,起来走走吧',
    '起来起来,该上厕所了',
    '水开了,快去喝水',
    '休息是为了更好地工作',
    '不要看电脑了,电脑受不了了',
    '让电脑休息休息吧'
];
# 时钟路径
$strConf = implode($conf['jump'],'') ;
# 休息次数
$t = 0;
while(true) {
    $show = $msg[rand(0,(count($msg)-1))];
    $r = $conf['time']['r']*$conf['time']['t'] ;
    sleep($s);
    exec("msg ".$_SERVER['USERNAME']." /TIME:2 $show");
    $file  = 'log.txt';
    file_put_contents($file, "") ;
    file_put_contents($file, $r,FILE_APPEND) ;
    exec("explorer $strConf");
    sleep($r) ;
    if($t == 0) {
        file_put_contents('rest.txt','') ;
    }
    $t++;
    file_put_contents('rest.txt',"今天已经休息".$t."次"."\r\n",FILE_APPEND) ;
}

# 格式方法
function style($s='')
{
    if($s) {
        echo "^_^:"."\n".'    ' ;
        echo $s ;
    }
    echo "\n"; exit ;
}