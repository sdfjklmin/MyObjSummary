<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019/1/8
 * Time: 14:03
 */

namespace bookLog\buildApis;
use Behat\Behat\Context\SnippetAcceptingContext;
use bookLog\buildApis\Chapter05;
use bookLog\buildApis\Chapter05Son;

class Chapter05Behat implements SnippetAcceptingContext
{
    private $station = "
特点：产品篮子
  购买产品
  作为客户，
  我需要能够将有趣的产品放入购物篮

  规则：
  - 增值税为20％
  - 低于10英镑的篮子交付为3英镑 超过10英镑的篮子交付为2英镑

  情景：购买10英镑以下的单品
    鉴于有一个“ 西斯勋爵光剑”，价格为5 英镑
    当我把“ 西斯勋爵光剑” 
    添加到篮子里然后我应该在篮子里有1个产品而整个篮子的价格应该是9 英镑    

  情景：购买单个产品超过10英镑
    鉴于有一个“ 西斯勋爵光剑”，花费15 英镑
    当我把“ 西斯勋爵光剑” 
    添加到篮子里然后我应该在篮子里有1个产品而整个篮子的价格应该是20 英镑    

  情景：购买两件超过10英镑的产品
    鉴于有一个“ 西斯勋爵光剑”，价格为10 英镑
    而且还有一个“ 绝地光剑”，这花费£ 5
    当我添加了“ 西斯光剑勋爵”到篮下
    和我添加了“ 绝地光剑”篮筐那么我应该有2种产品在篮下和整体篮子价格应该是£ 20    
    " ;
    private $shelf;  //业务场景(购买商品,获取商品价格)
    private $basket; //业务方案(添加商品[计算价格],获取总价[单物品不同的规则],物品总数[总共多少物品])

    public function __construct()
    {
        $this->shelf = new Chapter05();
        $this->basket = new Chapter05Son($this->shelf);
    }

    /**
     * @Given there is a :product, which costs £:price
     */
    public function thereIsAWhichCostsPs($product, $price)
    {
        $this->shelf->setProductPrice($product, floatval($price));
    }

    /**
     * @When I add the :product to the basket
     */
    public function iAddTheToTheBasket($product)
    {
        $this->basket->addProduct($product);
    }

    /**
     * @Then I should have :count product(s) in the basket
     */
    public function iShouldHaveProductInTheBasket($count)
    {
        \PHPUnit_Framework_Assert::assertCount(
            intval($count),
            $this->basket
        );
    }

    /**
     * @Then the overall basket price should be £:price
     */
    public function theOverallBasketPriceShouldBePs($price)
    {
        \PHPUnit_Framework_Assert::assertSame(
            floatval($price),
            $this->basket->getTotalPrice()
        );
    }
}
//$context = new  FeatureContext ;
//dd($context);