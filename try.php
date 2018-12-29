<?php
//file_put_contents('xss.txt',json_encode($_REQUEST,true).PHP_EOL, FILE_APPEND) ;
//特定测试文件
require './vendor/autoload.php';
require './bookLog/buildApis/index.php';
$t = new \bookLog\buildApis\ChapterOne();
$t->build();