<?php
namespace bookLog\buildApis;
/**
 * @remark
 * 使用PHP和Guzzle HTTP库发出HTTP请求
 * GraphQL(所见即所得) 和 REST Ful API 的差异
 *
 * 1.请求:
 *  POST /try.php HTTP/1.1
 *  Host: localhost:20002
 *  Content-Type: application/json
 *  Cache-Control: no-cache
 *  Postman-Token: 2d7cca56-c0a4-ea21-68ad-0aa6e3482da8
 *  {"query": "query { echo(message: \"Hello World 111\") }" }
 *
 * 2.响应:
 * HTTP/1.1 200 OK
 * Date: Fri, 04 Jan 2019 07:33:38 GMT
 * Content-Type: application/json; charset=utf-8
 * Transfer-Encoding: chunked
 * Server: localhost:20002
 * Status: 200 OK
 * {"code":"200","data":"test"}
 *
 * 3.格式:
 *  Content-Type: application/x-www-form-urlencoded
 *  Content-Type: multipart/form-data; ...
 *  js: $(selector).serializeArray() 将匹配的元素转成json字符串
 *  多维数组拼接: arr[test]=1&arr[name]=name
 *
 * 4.JSON 和 XML(不易于存储,文件较大,一切都为字符串[整数,布尔,null可能会混淆])
 *
 * 5.内容结构
 *   Json Api (优点:响应一致,结构相同|确定:无法多响应)
 *   名称空间
 */
/**输入输出理论(HTTP请求和响应)
 * Class Chapter3
 * @package bookLog\buildApis
 */
class Chapter03
{
    //GraphQL
    //get /compareGraphUsers
    /*{
     user(id: "1") {
        id
        email
        }
    }*/  //获取用户1的id和email
    public function compareGraphUsers()
    {

    }
    //RESTFul
    //get /compareRestFulUsers/1 (获取用户1的信息)
    public function compareRestFulUsers()
    {

    }

    //(new Chapter03())->test(function(){return 11;});
    public function test(?callable $function)
    {
        $a = $function();
        return $a ;
    }

    //Json Api 名称空间
    public function nameSpace5()
    {
        $json = '{"name":"Tome","age":"17"}';
        $jsonNameSpace = '{"data":[{
            "name":"Tome","age":"17"
        }]}';
    }
}