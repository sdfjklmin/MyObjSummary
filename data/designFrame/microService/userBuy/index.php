<?php
/**
 * @tip 如何保证微服务间的业务数据一致性。
 * @msg  商品采购的业务，在Dubbo的微服务架构下，如何通过Fescar来保障业务的数据一致性。
 * @use  以下例子中，Dubbo 和 Fescar 的注册配置服务中心均使用 Nacos。Fescar 0.2.1+ 开始支持 Nacos 注册配置服务中心
 * @link https://github.com/fescar-group/fescar-samples/tree/master/nacos
 *
 * 以下3个服务为相互独立的微服务,均是独立部署
 *  库存服务: 扣减给定商品的库存数量。
 *  订单服务: 根据采购请求生成订单。
 *  账户服务: 用户账户金额扣减。
 */


/** 库存服务
 * Class Storage
 */
class StorageService
{

}

/** 订单服务
 * Class Order
 */
class OrderService
{

}


/** 账号服务
 * Class Account
 */
class AccountService
{

}

/** 购买入口
 * Class Business
 */
class BusinessInit
{

}


/** 步骤
 * Class DataStep
 */
class DataStep
{
    public function init()
    {
        $this->stepOne();
        $this->stepTwo();
        $this->stepThree();
        $this->stepFive();
        $this->stepSix();
        $this->stepSeven();
    }

    public function stepOne()
    {
        //初始化MySql数据库,Innodb(事物操作)
        //修改对应服务的数据库连接
    }

    public function stepTwo()
    {
        //创建相关表和日志表,每个服务对应的表信息
        //需要保证每个物理库都包含 undo_log 表，此处可使用一个物理库来表示上述三个微服务对应的独立逻辑库
    }

    public function stepThree()
    {
        //引入 Fescar、Dubbo 和 Nacos 相关 POM 依赖
    }

    public function stepFour()
    {
        //微服务 Provider Spring配置
    }

    public function stepFive()
    {
        //事务发起方配置
    }

    public function stepSix()
    {
        //启动 Nacos-Server
    }

    public function stepSeven()
    {
        //启动 Fescar-Server
    }
}
