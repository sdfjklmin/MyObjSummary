<?php
//file_put_contents('xss.txt',json_encode($_REQUEST,true).PHP_EOL, FILE_APPEND) ;
//特定测试文件
use bookLog\buildApis\ChapterOne;
require './vendor/autoload.php';
$t = new ChapterOne();
dd($t->build());