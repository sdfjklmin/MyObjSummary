<?php

namespace Solid;

use Exception;

require "./BaseClass.php";

/** 开闭原则
 * Class BOrderProcess
 * @package Solid
 */
class COrderProcess
{
    /**
     * @var BillInterface
     */
    protected $baller;

    /**
     * @var OrderStorage
     */
    protected $order_storage;

    /**
     * @var array
     */
    protected $validators;

    /**
     * AOrderProcess constructor.
     * @param BillInterface $bill
     * @param OrderStorage $orderStorage
     * @param array $validators
     */
    public function __construct(BillInterface $bill, OrderStorage $orderStorage, $validators = [])
    {
        //账单和订单存储通过依赖注入的方式传入
        //可以动态的更改注入的对象来实现不同层次的扭转
        //可以注入实体进行业务逻辑、注入抽象进行反转

        $this->baller = $bill;

        $this->order_storage = $orderStorage;

        $this->validators = $validators;
    }

    /** 订单处理
     * @param Order $order
     * @throws Exception
     */
    public function process(Order $order)
    {
        //开闭原则：规定代码对扩展是开发的，对修改是封闭的。
        // 👇🏻 下面代码如果不断的加入规则，那这里也需要不断的添加验证。
        // 通过 validators 来承接扩展信息。

        //是否有重复订单：虽然这里做了订单验证，
        //  但是订单规则不断增加，这里的代码也会逐步做判断。
        // $ret = $this->order_storage->haveRecentOrder($order);
        // if ($ret) {
        //     throw new Exception('订单重复');
        // }
        // 这里会一直做验证做判断，想想如何修改呢 ？
        //$this->order_storage->verifyA();
        //$this->order_storage->verifyB();

        foreach ($this->validators as $validator) {
            /** @var OrderValidatorInterface $validator */
            $validator->validator($order);
        }

        //记录账单信息
        $this->baller->bill($order);

        //记录订单信息
        $this->order_storage->logSave($order);
    }
}

interface OrderValidatorInterface
{
    public function validator(Order $order);
}

/** 订单重复验证
 * Class OrderRecentValidator
 * @package Solid
 */
class OrderRecentValidator implements OrderValidatorInterface
{

    public function validator(Order $order)
    {
        echo "重复验证","\n";
    }
}

/** 订单账号验证
 * Class OrderAccountValidator
 * @package Solid
 */
class OrderAccountValidator implements OrderValidatorInterface
{

    public function validator(Order $order)
    {
        echo "账号验证","\n";
    }
}

//使用
//基于框架的容器可以很方便的注入和获取对应的实例
//这里通过对象方式进行调用，具体带来的代码效果还需要按自己的业务来判断，规则是为了代码更简洁有效，而不是一成不变的。
$bill         = new Bill();//账单
$orderStorage = new OrderStorage();//数据存储
$validators   = [
    new OrderRecentValidator(),
    new OrderAccountValidator
];//验证规则
$order        = new Order();//订单信息
$orderProcess = new COrderProcess($bill, $orderStorage, $validators);
$orderProcess->process($order);//订单处理
//这里将对象依赖全部在调用链外部获取，这样可以通过容器来优化一大段代码。