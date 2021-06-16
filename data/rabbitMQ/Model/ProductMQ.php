<?php
//生产者 P
namespace MyObjSummary\rabbitMQ;
require 'BaseMQ.php';
class ProductMQ extends BaseMQ
{
    private $routes = ['hello','word']; //路由key

    /**
     * ProductMQ constructor.
     * @throws \AMQPConnectionException
     */
    public function __construct()
    {
       parent::__construct();
    }

    /** 只控制发送成功 不接受消费者是否收到
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function run()
    {
        //频道
        $channel = $this->channel();
        //创建交换机对象
        $ex = $this->exchange();
        //消息内容
        $message = 'product message '.rand(1,99999);
        //开始事务
        $channel->startTransaction();
        $sendEd = true ;
        foreach ($this->routes as $route) {
            $sendEd = $ex->publish($message, $route) ;
            echo "Send Message:".$sendEd."\n";
        }
        if(!$sendEd) {
            $channel->rollbackTransaction();
        }
        $channel->commitTransaction(); //提交事务
        $this->close();
        die ;
    }
}
try{
    (new ProductMQ())->run();
}catch (\Exception $exception){
    var_dump($exception->getMessage()) ;
}