<?php
namespace bookLog\buildApis;
/**
 * @remark 端点测试
 * 1.介绍
 *   API测试
 *
 * 2.概念和工具
 *   对于API，有一些东西需要测试，但最基本的思想是，“当我请求这个URL时，我想看到一个foo资源”，“当我向API抛出一个JSON时，它应该接受它或拒绝它。”
 *   TDD(测试驱动开发):API接口测试,如果有很多接口,那么测试代码就会越写越多,一处报错整体无法运行 phpunit
 *   BDD(行为驱动开发) :
 *      cucumber(ruby) : https://cucumber.io/
 *      behat(php) : http://docs.behat.org/en/latest/
 *   行为驱动开发（BDD）采取的立场是:
 *      您可以简单有效地将需求的想法转变为实施，测试，生产就绪的代码，只要要求足够具体，每个人都知道发生了什么。
 *      要做到这一点，我们需要一种方法来描述需求，以便每个人 - 业务人员，分析师，开发人员和测试人员 - 对工作范围有共同的理解。
 *      由此他们可以达成共同的“完成”定义，并且我们摆脱了“那不是我要求的”或“我忘了告诉你关于其他事情” 的双重陷阱。
 *
 * 3.安装(behat)
 *   composer require --dev behat/behat
 *   ./vendor/bin/behat -V  查看当前版本
 *
 * 4.初始化
 *   ./vendor/bin/behat --init (会自动生成框架所需文件=>features\bootstrap\)
 *
 * 5.特性(编码)
 *   在features\bootstrap\FeatureContext.php中进行API测试编写
 *   Action         Endpoint        Feature
 *   Create         POST            /users features/users.feature
 *   Read           GET             /users/X features/users.feature
 *   Update         PUT             /users/X features/users.feature
 *   Delete         DELETE          /users/X features/users.feature
 *   List           GET             /users features/users.feature
 *   Image          PUT             /users/X/image features/users-image.feature
 *   Favorites      GET             /users/X/favorites features/users-favorites.feature
 *  Checkins        GET             /users/X/checkins features/users-checkins.feature
 *
 * 6.示例
 * Endpoint Testing (测试):
 * Feature: Places
 * Scenario: Finding a specific place
 *      When I request "GET /places/1"
 *      Then I get a "200" response
 *      And scope into the "data" property
 *          And the properties exist:
 *              """
 *              id
 *              name
 *              lat
 *              lon
 *              address1
 *              address2
 *              city
 *              state
 *              zip
 *              website
 *              phone
 *              """
 *          And the "id" property is an integer
 * Scenario: Listing all places is not possible
 *      When I request "GET /places"
 *      Then I get a "400" response
 * Scenario: Searching non-existent places
 *      When I request "GET /places?q=c800e42c377881f8ae509cf9a516d4eb59&lat=1&lon=1"
 *      Then I get a "200" response
 *      And the "data" property contains 0 items
 * Scenario: Searching places with filters
 *      When I request "GET /places?lat=40.76855&lon=-73.9945&q=cheese"
 *      Then I get a "200" response
 *      And the "pagination" property is an object
 *      And the "data" property is an array
 *      And scope into the first "data" property
 *          And the properties exist:
 *              """
 *              id
 *              name
 *              lat
 *              lon
 *              address1
 *              address2
 *              city
 *              state
 *              zip
 *              website
 *              phone
 *              """
 *          And reset scope
 *
 * 7.编写behat
 *
 * 8.运行behat
 *   ./vendor/bin/behat
 *
 * 9.其他
 *  / ** ... * /是PHP中的一种称为doc-block的特殊语法。
 *      它在运行时可被发现，并被不同的PHP框架用作为类，方法和函数提供附加元信息的方法。
 *      Behat使用doc-blocks进行步骤定义，步骤转换和钩子。
 *  首先，像Behat这样的工具实际上关闭了故事的沟通循环。
 *      这意味着不仅您和您的利益相关者可以共同定义您的功能在实现之前应该如何工作，BDD工具允许您在实现此功能后自动执行该行为检查。
 *      所以每个人都知道什么时候完成以及团队何时可以停止编写代码。
 *      从本质上讲，这就是Behat。
 */
/** 购买商品,获取商品价格
 * Class Chapter05
 * @package bookLog\buildApis
 */
final class Chapter05
{

    private $priceMap = array();

    public function setProductPrice($product, $price)
    {
        $this->priceMap[$product] = $price;
    }

    public function getProductPrice($product)
    {
        return $this->priceMap[$product];
    }
}

/** 添加商品[计算价格],获取总价[单物品不同的规则],物品总数[总共多少物品]
 * Class Chapter05Son
 * @package bookLog\buildApis
 */
final class Chapter05Son
{
    private $shelf;
    private $products;
    private $productsPrice = 0.0;

    public function __construct(Chapter05 $shelf)
    {
        $this->shelf = $shelf;
    }

    public function addProduct($product)
    {
        $this->products[] = $product;
        $this->productsPrice += $this->shelf->getProductPrice($product);
    }

    public function getTotalPrice()
    {
        return $this->productsPrice
            + ($this->productsPrice * 0.2)
            + ($this->productsPrice > 10 ? 2.0 : 3.0);
    }

    public function count()
    {
        return count($this->products);
    }
}