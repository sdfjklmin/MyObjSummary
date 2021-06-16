<?php
namespace bookLog\buildApis;
/**
 * @remark
 * TDD[测试驱动开发（Test-Driven Development）]开发模式
 * 端点理论:将操作计划转换为实际端点需要的功能(参照:RESTful api的理论和命名约定的最佳实践)
 * API资源应该避免输出自增变量,而是唯一标识
 *
 * POST 和 PUT 的区别!
 * PUT是在您事先知道整个URL并且操作是幂等的情况下使用的。(幂等性是一个很花哨的词，意思是“可以一遍又一遍地做而不会产生不同的结果”)
 * 复数、单数还是两者都有?( /user/1[用户1的信息]  /users[所有用户|1,2用户信息])
 * 一个谓词是一个操作,一个执行术语,我们的API只需要一个谓词—HTTP方法。所有其他动词都需要远离URL。
 * 名词是地方或事物。资源就是事物，而URL就是事物存在于互联网上的地方。( POST /users/5/messages HTTP/1.1 )
 * GET POST PUT PATCH DELETE COPY HEAD OPTIONS LINK UNLINK LOCK UNLOCK PROPFIND VIEW
 *
 * 规划端点:控制器,路由
 * laravel example:
 * Create POST /users Route::post('users','UsersController@create');
 * 常见的REST-ful API请求
 */
/** Planning and Creating Endpoints
 *  规划 和 创造 端点(url访问路径)
 * (分析功能需求,映射成单一的功能模块)
 * Class ChapterTwo
 * (简单的功能需求[增删改查,列表]):
 *      创建, 读取, 更新, 删除, 列表
 * @package bookLog\buildApis
 */
class Chapter02
{
    //获取资源(功能点)
    //•获取/资源——特定数据的分页列表，以某种逻辑默认顺序。
    //•GET /resources/X -实体X，它可以是ID、散列、段塞、用户名等，只要它是唯一的一个“资源”。
    public function resourceGet()
    {

    }

    //删除资源(功能点)
    //•删除/位置/X -删除单个位置。
    //•删除/位置/X,Y,Z -删除一些位置。
    //•删除/位置-这是一个潜在的危险端点，可以跳过，因为它应该删除所有位置。
    //•删除/位置/X/图像-删除一个位置的图像，或:
    //•删除/位置/X/图像-如果你选择多个图像，这将删除所有的图像。
    public function resourceDel()
    {

    }
}