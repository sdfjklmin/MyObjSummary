<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
/**
 * 路由地址
 */
Route::get('hello/:name', 'index/hello');
Route::get('test', 'index/test');
Route::get('min', 'min/test');

//路由到操作方法 @[模块/控制器/]操作
Route::get('blog','@index/min/read');
//路由到类的方法 \类的命名空间\类名@方法名
Route::get('blog1','\app\index\controller\Min@blog1');
//路由到重定向地址 重定向的外部地址必须以“/”或者http开头的地址。
//如果路由地址以“/”或者“http”开头则会认为是一个重定向地址或者外部地址
Route::get('blog2','http://www.baidu.com');

/**
 * 闭包支持 一些特殊需求的路由，而不需要执行控制器的操作方法
 */
Route::get('hello/:name',function ($name=''){
    return 'hello 闭包'.$name ;
});
//依赖注入
Route::get('hello1/:name',function (\think\Request $response ,$name) {
    $method = $response->method();
    return $method .' hello '.$name ;
});
//指定响应对象
Route::get('hello3/:name', function (\think\Response $response, $name) {
    return $response
        ->data('Hello,' . $name)
        ->code(500)
        ->contentType('text/plain');
});
//对于不存在的static目录下的资源文件设置404访问
Route::get('static', response()->code(404));

/**
 * 路由参数
 */
//url后缀

// 定义GET请求路由规则 并设置URL后缀为html的时候有效
// www.abc.com/hello4/abc.html
// ext不传值,表示不允许使用后缀访问
Route::get('hello4/:name','min/blog4')->ext('html') ;
Route::get('hello5/:name','min/blog4')->ext('html|php') ; //多个后缀
//禁止访问的URL后缀 denyExt不传值,表示必须使用后缀访问
Route::get('hello6/:name','min/blog4')->denyExt('jpg|png|gif');

//域名检测
// 完整域名检测 只在news.thinkphp.cn访问时路由有效
Route::get('hello7/:id', 'min/blog4')
    ->domain('news.thinkphp.cn');
// 子域名检测
Route::get('new/:id', 'News/read')
    ->domain('news');
return [

];
