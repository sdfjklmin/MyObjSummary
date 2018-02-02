<?php
namespace think;
//修改默认入口地址
define('APP_PATH',__DIR__.'/application/') ;
require __DIR__.'/thinkphp/base.php' ;
Container::get('app',[APP_PATH])->run()->send() ;