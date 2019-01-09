<?php
namespace bookLog\buildApis;

/**
 * @remark 数据关系
 * 1.介绍
 *   API输出的关系不需要直接映射到数据库关系。
 *   如果正确地构建了数据库关系，那么关系通常是相似的，但是输出可能具有额外的动态关系，这些关系不是由连接定义的，而且不一定包含所有可能的数据库关系。
 *   REST组件:https://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm#sec_5_2
 *
 * 2.子资源
 *   Station:
 *      一个区域有50个地点
 *   Get place/x/info  info(地点,经纬度,图像,相关信息,[这里假设是4个请求])
 *      请求数: 1 + (50×4) = 251 ;请求太过频繁
 *   Get places 一个请求获取所有的数据,数据量太大,对客户端不友好.
 *   Get places/main 获取地点的主要信息(图像) 点击时获取其他信息 Get place/x/info
 *   这里的权衡是，下载足够的数据以避免用户等待后续加载和下载太多数据以使他们等待初始加载是困难的。
 *   API需要这种灵活性，而将子资源作为加载相关数据的唯一方式对于API使用者来说是有限制的。
 *
 * 3.外键数组
 *  {
 *      "post": {
 *          "id": 1,
 *          "title": "Progressive Enhancement is Dead",
 *          "_links": {
 *                      "comments": ["1", "2"]
 *                    }
 *          }
 *  }
 *  使用 _links作为外键数组,即有数据的时候需要再次请求
 *   Get comments/1  Get comments/2  或 Get comments/1,2
 *  变向的减少了http并发请求
 *  缺点是API使用者必须将所有这些数据连接在一起，这对于大型数据集来说可能需要大量的工作。
 *
 * 4.复合文件(边读) @link https://canvas.instructure.com/doc/api/file.compound_documents.html
 *  获取作者有那些文章
    {
        "posts": [
                    {
                        "id": "1",
                        "title": "Awesome API Book",
                        "_links": { "comments": ["1","2"] }
                    },
                    {
                        "id": "2",
                        "title": "But Really That API Book",
                        "_links": { "comments": ["3"] }
                    }
                ],
        "_linked": {
        "comments": [{
                    "id": "1",
                    "message": "Great book",
                    "created_at": "2014-08-23T18:20:03Z"
                },
                {
                    "id": "2",
                    "message": "I lolled",
                    "created_at": "2014-08-24T20:04:01Z"
                },
                {
                    "id": "3",
                    "message": "Ugh JSON-API...",
                    "created_at": "2014-08-29T14:01:13Z"
                }
            ]
        }
    }
 * 也是需要做大量的拼接和映射
 *
 * 5.嵌入式文档(嵌套)
 *  Get place?include=img,merchant,check
    {
        "data": [
            {
                "id": 2,
                "name": "Videology",
                "lat": 40.713857,
                "lon": -73.961936,
                "created_at": "2013-04-02",
                "check": [],
                "merchant": [],
                "img": []
            }
        ]
    }
 * 用Rails嵌入 (将公用信息提取出来,进行分层)
{
    "id": 1,
    "name": "Konata Izumi",
    "age": 16,
    "created_at": "2006/08/01",
    "awesome": true,
    "posts": [
        {
            "id": 1,
            "author_id": 1,
            "title": "Welcome to the weblog"
        },
        {
            "id": 2,
            "author_id": 1,
            "title": "So I was thinking"
        }
    ]
}
 *
 * 6.总结
 *      根据业务数据进行分析
 *      http://fractal.thephpleague.com/serializers/
 */
/**
 * Class Chapter07
 * @package bookLog\buildApis
 */
class Chapter07
{

}