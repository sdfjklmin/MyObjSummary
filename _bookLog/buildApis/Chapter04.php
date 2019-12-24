<?php
namespace bookLog\buildApis;
/**
 * @remark
 * 1.介绍
 *   HTTP状态代码
 *   自定义错误代码和消息
 *
 * 2.HTTP状态码
 *   2xx是关于成功的(在发送响应之前，客户机尝试做的任何事情都是成功的。请记住，像202 accept这样的状态并不表示实际结果，它只表示接受了一个请求并正在异步处理。)
 *   3xx是关于重定向的(这些都是关于将调用应用程序发送到实际资源的其他地方。其中最著名的是303 See Other和301永久移动，web上经常使用它们将浏览器重定向到另一个URL。)
 *   4xx是关于客户端错误的
 *   5xx是关于服务器的
 *
 * 3.错误代码和错误消息
 *   以编程的方式去检查错误代码(测试脚本)
 *
 * 4.错误或者更多错误
 *   (通常情况下,在验证的时候,有一个错误后就会终止控制器)
 *   (一次性验证所有的信息,若有错就返回所有的错误信息)
 *
 * 5.错误响应标准
 *   Json Api: https://jsonapi.org
 *   RFC : https://tools.ietf.org/html/draft-nottingham-http-problem-07
 *   Crell/ApiProblem PHP: https://github.com/Crell/ApiProblem
 *
 * 6.常见的陷阱
 *   200 有错误信息 ×
 */
/** 状态码,错误,信息
 * Class Chapter04
 * @package bookLog\buildApis
 */
class Chapter04
{
    /** 当个错误提示
     * @return string
     */
    public function errorOne()
    {
        $backData = [
            'code' => '400' ,
            'messages'=>'操作错误!',
            'errors'=>[],
        ] ;
        return json_encode($backData,true);
    }

    /** 多个错误提示
     * @return string
     */
    public function errorMore()
    {
        $backData = [
            "errors" => [
                [
                    'code'=>'400',
                    'messages'=>'操作错误!',
                ],
                [
                    'code'=>'403',
                    'messages'=>'无操作权限!',
                ]
            ]
        ];
        return json_encode($backData,true);
    }

    /** Json API 返回格式
     * @return string
     */
    public function errorJsonApi()
    {
        //错误 : [{ 代码,标题,详细信息,问题的更多细节 }]
        $json = '{
        "errors": 
            [{
            "code": "ERR-01234",
            "title": "OAuth Exception",
            "details": "Session has expired at unix time 1385243766. The current unix time is 1385848532.",
            "href": "http://example.com/docs/errors/#ERR-01234"
            }] 
        }';
        return $json ;
    }
}