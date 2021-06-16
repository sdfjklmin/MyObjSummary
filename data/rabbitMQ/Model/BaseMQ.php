<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/12/13
 * Time: 14:11
 */

namespace MyObjSummary\rabbitMQ;

/** Member
 *      AMQPChannel
 *      AMQPConnection
 *      AMQPEnvelope
 *      AMQPExchange
 *      AMQPQueue
 * Class BaseMQ
 * @package MyObjSummary\rabbitMQ
 */
class BaseMQ
{
    /** MQ Channel
     * @var \AMQPChannel
     */
    public $AMQPChannel ;

    /** MQ Link
     * @var \AMQPConnection
     */
    public $AMQPConnection ;

    /** MQ Envelope
     * @var \AMQPEnvelope
     */
    public $AMQPEnvelope ;

    /** MQ Exchange
     * @var \AMQPExchange
     */
    public $AMQPExchange ;

    /** MQ Queue
     * @var \AMQPQueue
     */
    public $AMQPQueue ;

    /** conf
     * @var
     */
    public $conf ;

    /** exchange
     * @var
     */
    public $exchange ;

    /** link
     * BaseMQ constructor.
     * @throws \AMQPConnectionException
     */
    public function __construct()
    {
        $conf =  require 'config.php' ;
        if(!$conf)
            throw new \AMQPConnectionException('config error!');
        $this->conf     = $conf['host'] ;
        $this->exchange = $conf['exchange'] ;
        $this->AMQPConnection = new \AMQPConnection($this->conf);
        if (!$this->AMQPConnection->connect())
            throw new \AMQPConnectionException("Cannot connect to the broker!\n");
    }

    /**
     * close link
     */
    public function close()
    {
        $this->AMQPConnection->disconnect();
    }

    /** Channel
     * @return \AMQPChannel
     * @throws \AMQPConnectionException
     */
    public function channel()
    {
        if(!$this->AMQPChannel) {
            $this->AMQPChannel =  new \AMQPChannel($this->AMQPConnection);
        }
        return $this->AMQPChannel;
    }

    /** Exchange
     * @return \AMQPExchange
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function exchange()
    {
        if(!$this->AMQPExchange) {
            $this->AMQPExchange = new \AMQPExchange($this->channel());
            $this->AMQPExchange->setName($this->exchange);
        }
        return $this->AMQPExchange ;
    }

    /** queue
     * @return \AMQPQueue
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function queue()
    {
        if(!$this->AMQPQueue) {
            $this->AMQPQueue = new \AMQPQueue($this->channel());
        }
        return $this->AMQPQueue ;
    }

    /** Envelope
     * @return \AMQPEnvelope
     */
    public function envelope()
    {
        if(!$this->AMQPEnvelope) {
            $this->AMQPEnvelope = new \AMQPEnvelope();
        }
        return $this->AMQPEnvelope;
    }
}