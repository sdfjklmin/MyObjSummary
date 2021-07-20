<?php

namespace Solid;

/** 数据库
 * Class Db
 * @package UsageDesign
 * @method static table($table)
 */
class Db
{

}

/**
 * Class Order
 * @package UsageDesign
 * @property string $account
 * @property string $user_name
 */
class Order
{

}

/** 账单处理接口
 * Interface BillInterface
 * @package UsageDesign
 */
interface BillInterface
{
    public function bill($order);
}

/** 账单处理
 * Class Bill
 * @package UsageDesign
 */
class Bill implements BillInterface
{

    public function bill($order)
    {
        echo "记录账单","\n";
    }
}


/** 将订单存储抽离到当前类
 * 重复订单、保存订单 后续规则变动不需要更改订单处理类
 * 订单处理流程相当于提供了一套订单处理规范、模板等
 * Class BOrderStorage
 * @package Solid
 */
class OrderStorage
{
    public function haveRecentOrder($order): bool
    {
        echo "是否有重复订单","\n";
        return false;
    }

    public function logSave($order)
    {
        echo "保存订单信息","\n";
    }
}
