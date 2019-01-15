<?php
namespace bookLog\buildApis;

/**
 * @remark  HATEOAS
 * 1.介绍
 *   HATEOAS : 它代表作为应用程序状态引擎的超媒体，被宣布为hat-ee-os、hate O-A-S或hate-ee-ohs;
 *             包括 : 内容协商 , 超媒体控制
 *             更改Accept标头并在响应中将内容类型标头从JSON切换到XML或CSV非常好，而且非常容易做到。
 *
 * 2.内容协商
 *      /status/show.json?id=123  ×   json格式
 *      /status/show.xml?id=123   ×   xml格式
 *  上面API有点滥用资源概念
 *      /status/123     √ : 这样做的双重好处是，允许API使用默认的内容类型进行响应，或者尊重Accept头并输出请求内容类型，或者在API不支持的情况下输出415状态代码。
 *  URI = Universal Resource Identifier 统一资源标志符
 *  URL = Universal Resource Locator 统一资源定位符
 *  URN = Universal Resource Name 统一资源名称
 *  URI = ( URL or URN or (URL and URN))
 *  大多数流行API默认情况下都支持 JSON ,除此之外还有 Xml ,YAml(https://symfony.com/doc/current/components/yaml.html) 等等 .
 *
 * 3.超媒体控制 : 它们只是指向其他内容、关系和进一步操作的链接。
 *      超媒体的基本主题是API应该能够对API客户机应用程序和人的外观产生完美的意义 .
 *      缩写“URI”通常用于仅指协议、主机名和端口之后的内容(意味着URI是路径、扩展名和查询字符串)，而“URL”用于描述完整地址。
    {
        "data": [
            "id": 1,
            "name": "Mireille Rodriguez",
            "links": [
                {
                    "rel": "self",
                    "uri": "/places/2"
                },
                {
                    "rel": "place.checkins",
                    "uri": "/places/2/checkins"
                },
                {
                    "rel": "place.image",
                    "uri": "/places/2/image"
                }
            ]
        ]
    }
 * rel 代表关系,uri 通用资源指定器
 */
/**
 * Class Chapter12
 * @package bookLog\buildApis
 */
class Chapter12
{
    public function accept()
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ;
        $acceptArr = (array)explode(';',$accept);
        $acceptType = strtolower($acceptArr[0]) ;
        switch ($acceptType) {
            case "application/json" :
                //json_decode()
                break ;
            case "application/x-yaml";
                break ;
            default :
                break ;
        }
        return $acceptType ;
    }
}

/**
 * @remark 版本控制
 * 1.介绍
 *      你会发现大多数专家给出的一般建议是:尽量限制变化。
 *      这是一个非常公平的声明，但似乎有点逃避。
 *      无论您的API规划得多么好，您的业务需求最终都可能迫使您做出重大更改。
 *
 * 2.不同API的版本控制
 *      URI : 在URI中抛出版本号是流行的公共api中非常常见的做法。 /v1/places   /v2/places
 *            根据业务 v1 v2可以对应不同的代码,服务器,甚至是编程语言
 *      Hostname : http://api-v1.com/places  http://api-v2.com/places
 *      Body And Query Params(主体和查询参数) :
 *          POST /places HTTP/1.1
 *          Host: api.example.com
 *          Content-Type: application/json
 *          {   "version" : "1.0"  } (自定义版本参数)
 *      自定义请求头 :
 *          Request :
 *              GET /places HTTP/1.1
 *              Host: api.example.com
 *              BadApiVersion: 1.0  (自定义header头)
 *          Response :
 *              HTTP/1.1 200 OK
 *              BadAPIVersion: 1.1
 *              Vary: BadAPIVersion
 *      内容协商 :
 *              application/vnd.github[.version].param[+json]
 *              eg:
 *                  Accept: application/vnd.github.v3+json|xml|yaml|...
 *      资源的内容协商 :
 *               application/vnd.github[.version].param[+json] ; version=1.0
 *               Accept: application/vnd.github.user.v4+json
 *               Accept: application/vnd.github.user+json; version=4.0
 *      特性标记 :
 * 3.询问用户
 *
 */
/**
 * Class Chapter13
 * @package bookLog\buildApis
 */
class Chapter13
{

}