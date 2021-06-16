<?php
//消费者 C
namespace MyObjSummary\rabbitMQ;
class Consumer2MQ
{
    private $conf ;
    private  $e_name = 'word'; //交换机名
    private  $q_name = 'word'; //队列名
    private  $route = 'word'; //路由key

    public function __construct()
    {
        $conf =  require 'config.php' ;
        if(!$conf) exit('配置信息错误!');
        $this->conf = $conf['host'] ;
    }

    /** 接受消息 如果终止 重连时会有消息
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function run()
    {
        //创建连接和channel
        $conn = new \AMQPConnection($this->conf);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        $channel = new \AMQPChannel($conn);

        //创建交换机
        $ex = new \AMQPExchange($channel);
        $ex->setName($this->e_name);
        $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
        $ex->setFlags(AMQP_DURABLE); //持久化
        //echo "Exchange Status:".$ex->declare()."\n";

        //创建队列
        $q = new \AMQPQueue($channel);
        //var_dump($q->declare());exit();
        $q->setName($this->q_name);
        $q->setFlags(AMQP_DURABLE); //持久化
        //echo "Message Total:".$q->declareQueue()."\n";

        //绑定交换机与队列，并指定路由键
        echo 'Queue Bind: '.$q->bind($this->e_name, $this->route)."\n";

        //阻塞模式接收消息
        echo "Message:\n";
        while(True){
            $q->consume(function ($envelope,$queue){
                $msg = $envelope->getBody();
                echo $msg."\n"; //处理消息
                $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
            });
            //$q->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
        }
        $conn->disconnect();
    }
}
(new Consumer2MQ)->run();