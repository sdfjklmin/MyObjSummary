<?php

namespace Solid;

use Exception;

require "./BaseClass.php";


/** 订单处理
 * Class AOrderProcess
 * @package Solid
 * @remark
 * 用于订单处理的类，在 process 中
 *  逻辑处理为: 优先判断是否有重复订单、再进行记账、最后保存订单信息。
 * 重复订单和订单保存 都涉及到数据仓库，这些可以归属于 订单仓库。
 * 订单处理 只处理订单流程，不用去关心具体的验证、存储、逻辑，相当于一个 订单处理 流程|模板|盒子等抽象的类。
 * 订单仓库 提供和数据相关的接口逻辑。
 * 这样看来职责视乎要明确一些。
 */
class AOrderProcess
{
    /**
     * @var BillInterface
     */
    protected $baller;

    /**
     * AOrderProcess constructor.
     * @param BillInterface $bill
     */
    public function __construct(BillInterface $bill)
    {
        $this->baller = $bill;
    }

    /** 订单处理
     * @param Order $order
     * @throws Exception
     */
    public function process(Order $order)
    {
        //是否有重复订单
        if ($this->haveRecentOrder($order)) {
            throw new Exception('订单重复');
        }
        //记录账单信息
        $this->baller->bill($order);

        //记录订单信息
        Db::table('order')->save([
            'account' => $order->account,
            'user_name' => $order->user_name,
            'create_at' => time()
        ]);
    }

    // 重复订单
    protected function haveRecentOrder(Order $order): bool
    {
        $count = Db::table('order')->where(['user_name' => $order->user_name])->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}