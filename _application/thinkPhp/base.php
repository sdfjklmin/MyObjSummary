<?php
define('APP_ROOT',__DIR__);
define('APP_STATIC',APP_ROOT.'/../../_static/');
define('APP_INIT_ROOT',APP_ROOT.'/../..');
define('APP_CURRENT_ROOT',APP_ROOT.'/..');
//不需要生成的 后缀 可提成配置文件
//文件夹
defined('NOT_LINK') or define('NOT_LINK', ['.','frame','html','object','static','tool','vendor','min','config']) ;
//文件后缀(避免无法解析时下载)
defined('NOT_SUFFIX') or define('NOT_SUFFIX', ['png','md','jpg','zip']) ;