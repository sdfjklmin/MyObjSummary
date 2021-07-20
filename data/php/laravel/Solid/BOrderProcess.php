<?php

namespace Solid;

use Exception;

require "./BaseClass.php";

/** 单一职责
 * Class BOrderProcess
 * @package Solid
 */
class BOrderProcess
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
     * AOrderProcess constructor.
     * @param BillInterface $bill
     * @param OrderStorage $orderStorage
     */
    public function __construct(BillInterface $bill, OrderStorage $orderStorage)
    {
        //账单和订单存储通过依赖注入的方式传入
        //可以动态的更改注入的对象来实现不同层次的扭转
        //可以注入实体进行业务逻辑、注入抽象进行反转

        $this->baller = $bill;

        $this->order_storage = $orderStorage;
    }

    /** 订单处理
     * @param Order $order
     * @throws Exception
     */
    public function process(Order $order)
    {
        //是否有重复订单：虽然这里做了订单验证，
        //  但是订单规则不断增加，这里的代码也会逐步做判断。
        $ret = $this->order_storage->haveRecentOrder($order);
        if ($ret) {
            throw new Exception('订单重复');
        }

        // 这里会一直做验证做判断，想想如何修改呢 ？
        //$this->order_storage->verifyA();
        //$this->order_storage->verifyB();

        //记录账单信息
        $this->baller->bill($order);

        //记录订单信息
        $this->order_storage->logSave($order);
    }
}