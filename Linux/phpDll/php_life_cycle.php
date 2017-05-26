<?php
/**
 * PHP程序的启动
 *              前置初始化(Apache相关操作)
 *              模块初始化       对应扩展 php.dll
 *              请求初始化       $_SERVER等参数                       I
 *      frame   执行php脚本      code                               I   I  重复执行
 *              请求处理完成     request                            I
 *              关闭模块        close
 *
 * Apache:
 *       A: php作为Apache的一个模块的启动和终止.
 *          这次php会初始化一些必要的数据,比如和Apache有关的,这些数据时常驻内存的!终止与之对应.
 *       B: Apache分配一个页面请求过来的时候,php会有一次启动和终止
 * PHP扩展周期:
 *      http://www.cunmou.com/phpbook/1.md
 *      Module init、Request init、Request Shutdown、Module shutdown 四个过程
 *
 */


// 这些代码都在walu.c里面，不再.h里 php内核代码
int time_of_minit;//在MINIT中初始化，在每次页面请求中输出，看看是否变化
PHP_MINIT_FUNCTION(walu)
{
    time_of_minit=time(NULL);//我们在MINIT启动中对他初始化
    return SUCCESS;
}
 
int time_of_rinit;//在RINIT里初始化，看看每次页面请求的时候变不。
PHP_RINIT_FUNCTION(walu)
{
    time_of_rinit=time(NULL);
    return SUCCESS;
}
 
PHP_RSHUTDOWN_FUNCTION(walu)
{
    FILE *fp=fopen("/cnan/www/erzha/time_rshutdown.txt","a+");//请确保文件可写，否则apache会莫名崩溃
    fprintf(fp,"%d\n",time(NULL));//让我们看看是不是每次请求结束都会在这个文件里追加数据
    fclose(fp);
    return SUCCESS;
}
 
PHP_MSHUTDOWN_FUNCTION(walu)
{
    FILE *fp=fopen("/cnan/www/erzha/time_mshutdown.txt","a+");//请确保文件可写，否则apache会莫名崩溃
    fprintf(fp,"%d\n",time(NULL));
    return SUCCESS;
}
 
//我们在页面里输出time_of_minit和time_of_rinit的值
PHP_FUNCTION(walu_test)
{
    php_printf("%d&lt;br /&gt;",time_of_minit);
    php_printf("%d&lt;br /&gt;",time_of_rinit);
    return;
}