/*
 * PHP生命周期
 * PHP程序的启动
 *              前置初始化(Apache或Nginx相关操作)
 *              模块初始化       对应扩展 php.dll
 *              请求初始化       $_SERVER等参数      I
 *      frame   执行php脚本      code               I   I可以重复执行(一般为框架内容)
 *              请求处理完成     request            I
 *              关闭模块        close
 *
 * Apache:
 *       A: php作为Apache的一个模块的启动和终止.
 *          这次php会初始化一些必要的数据(PHP_MINIT_FUNCTION),比如和Apache有关的,这些数据时常驻内存的!终止与之对应.
 *       B: Apache分配一个页面请求过来的时候,php会有一次启动和终止
 *
 * PHP扩展周期:
 *      http://www.cunmou.com/phpbook/1.md
 *      Module init、Request init、Request Shutdown、Module shutdown 四个过程
 *      具体的执行顺序如下
 */

// 这四个宏都是在walu.c里完成最终实现的，而他们的则是在/main/php.h里被定义的(其实也是调用的别的宏)
// 这些代码都在walu.c里面，不再.h里 php内核代码

//模块初始化 前置初始化必要数据 {扩展(系统模块),(常量,类,资源)(自定义)}
int time_of_minit;//在MINIT中初始化，在每次页面请求中输出，看看是否变化
PHP_MINIT_FUNCTION(walu)
{
    time_of_minit=time(NULL);//我们在MINIT启动中对他初始化
    return SUCCESS;
}

//(启动)当一个页面请求到来时候，PHP会打了鸡血似的马上开辟一个新的环境，并重新扫描自己的各个扩展，
//挨个执行它们各自的RINIT方法(俗称Request Initialization)，
//这时候一个扩展可能会初始化自己扩展使用的变量啊，初始化等会用户端即PHP脚本中的变量啊之类的，
//内核预置了PHP_RINIT_FUNCTION()这个宏函数来帮我们实现这个功能：
int time_of_rinit;//在RINIT里初始化，看看每次页面请求的时候变不。
PHP_RINIT_FUNCTION(walu)
{
    time_of_rinit=time(NULL);
    return SUCCESS;
}

//(结算)处理(顺利运行完文件,用户主动exit/die,致命error)
//回收程序
//释放掉这次请求使用过的所有东西:包括变量表的所有变量、所有在这次请求中申请的内存等等
PHP_RSHUTDOWN_FUNCTION(walu)
{
    FILE *fp=fopen("/cnan/www/erzha/time_rshutdown.txt","a+");//请确保文件可写，否则apache会莫名崩溃
    fprintf(fp,"%d\n",time(NULL));//让我们看看是不是每次请求结束都会在这个文件里追加数据
    fclose(fp);
    return SUCCESS;
}

//Apache通知PHP自己要Stop的时候，PHP便进入MSHUTDOWN（俗称Module Shutdown）阶段。
//这时候PHP便会给所有扩展下最后通喋，如果哪个扩展还有未了的心愿，就放在自己MSHUTDOWN方法里
//这可是最后的机会了，一旦PHP把扩展的MSHUTDOWN执行完，便会进入自毁程序，
//这里一定要把自己擅自申请的内存给释放掉，否则就杯具了
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


# 线程安全与非线程安全
# web: http://www.cunmou.com/phpbook/1.4.md

# TRSM (thread safe resource management) php抽象层,php多线程管理
