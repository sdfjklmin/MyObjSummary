<?php
namespace bookLog\buildApis;
/**
 * @remark 分页
 * 1.介绍
 *      为了限制HTTP响应大小，可以将数据分成多个HTTP请求。
 *          下载更多的东西需要更长的时间
 *          您的数据库可能不喜欢尝试一次返回100,000条记录
 *          迭代超过100,000条记录的表示逻辑并不有趣
 *          API可以有端点 100,000 可以是任意数字,(定义一个最大值)
 *
 * 2.Paginators(laravel 分页器)
    {
        "data": [
        "..."
    ],
        "pagination": {
        "total": 1000,
            "count": 12,
            "per_page": 12,
            "current_page": 1,
            "total_pages": 84,
            "next_url": "/places?page=2&number=12"
        }
    }
 *3.偏移量和游标
 *      游标通常是唯一标识符或偏移量，因此API只能请求更多数据。
 *      使用偏移量很简单。不管您的id是什么—自动递增的，UUID等等—您只需在其中输入12，
 *          然后说“我想要12条记录，偏移量为12”，而不是说“我想要id=12之后的记录”。
 *   模糊游标: 加密ID . eg: base64_encode(1) // MQ==
 *   额外请求 = 悲伤 : 一些客户端开发人员不喜欢这种方法，因为他们不喜欢必须发出额外的HTTP请求才能发现没有数据的想法。
 *   使用链接头分页
 *       <https://api.github.com/user/repos?page=3&per_page=100>; rel="next"
 *       <https://api.github.com/user/repos?page=50&per_page=100>; rel="last"
 */
/**
 * Class Chapter10
 * @package bookLog\buildApis
 */
class Chapter10
{

}

/**
 * @remark 文档
 *  phpDocument : https://phpdoc.org/
 *  soundCloudApi : http://developers.soundcloud.com/docs/api/guide
 *  Sculpin : https://sculpin.io/ 是一个用PHP编写的静态站点生成器。它将Markdown文件，Twig模板和标准HTML转换为可轻松部署的静态HTML站点。
 *  Swagger定义了一个规范，各种语言或框架特定于spe的实现都有自己的解决方案。对于PHP，实现这一点的方法是通过一组相当混乱(且文档很少)的带有奇怪名称的注释。
 *              此外，它要求您将这些注释分布到应用程序的一大块区域，包括您可能甚至没有的数据映射器样式模型。
 *              它需要属性级注释，而我的模型和分形转换器都没有属性，所以这是一种疯狂而古怪的尝试和工作方式。
 *              https://swagger.io/
 *  Apiary API Blueprint : https://apiary.io/blueprint | https://apiary.io/ (本书推荐)
 */
/**
 * Class Chapter11
 * @package bookLog\buildApis
 */
class  Chapter11
{

}