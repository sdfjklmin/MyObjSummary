<?php
/**
 * Created by PhpStorm.
 * User: sokminyo
 * Date: 2019/4/1
 * Time: 20:24
 */

/*** 消息事物类
 * Class MessageTr
 */
class MessageTr
{
    /**
     * 消息标签
     */
    const MT_TAG_SEND   = 1 ;  #未发送
    const MT_TAG_SURE   = 2 ;  #确认发送
    const MT_TAG_OK     = 3 ;  #发送成功
    const MT_TAG_CANCEL = 0 ;  #已取消

    /** 请求发送消息
     * @param array ...$params
     * @return int
     */
    public function mtSend(...$params)
    {
        //根据 $params 进行处理
        //只记录消息数据不发送消息,标记消息为1
        //返回消息唯一标识
        return rand(1,10000);
    }

    /** 确认发送消息
     * @param $uniqueId
     */
    public function mtSure($uniqueId)
    {
        //根据 $uniqueId 对消息进行处理
        //发送消息数据,标记消息为2
        //消息发送成功后,标记消息为3
    }

    /** 取消发送消息
     * @param $uniqueId
     */
    public function mtCancel($uniqueId)
    {
        //根据 $uniqueId 对消息进行处理
        //标记消息为0
    }

    /**
     *  定期确认 tag 为 0 和 1 的数据
     */
    public function mtJob()
    {
        //请求消息来源方,确认为废消息后移除数据
    }

    /**
     * 消息补偿,最终一致性
     */
    public function mtRecoup()
    {
        //获取消息 tag 为 2 的数据,保持数据一致性
    }
}


/** 主业务类
 * Class Business
 */
class Business
{
    /** 消息事物类
     * @var MessageTr
     */
    private $messageTr ;

    public function init()
    {
        //初始化
        $this->messageTr = new MessageTr();
    }

    /**
     * 业务处理
     */
    public function deal()
    {
        //初始化
        $this->init();
        /** 发送消息事物请求
         * @var $uniqueId string 消息事物返回的唯一标识
         */
        $uniqueId = $this->messageTr->mtSend();
        //记录本地消息
        $this->message();
        //事物
        $tr = new BusinessDb();
        $tr->trBegin();
        //TCC事物模型 try-commit-cancel
        try{
            //业务处理 do something
            //事物提交
            $tr->trCommit();
            //确认发送消息事物
            $this->messageTr->mtSure($uniqueId);
            //改变本地消息
            $this->message();
        }catch (Exception $exception){
            //事物回滚
            $tr->trRollback();
            //回滚消息事物
            $this->messageTr->mtCancel($uniqueId);
            //改变本地消息
            $this->message();
        }
    }

    public function message()
    {
        //记录消息的发送,确认发送,回滚
    }
}

/** 主业务DB
 * Class BusinessDb
 */
class BusinessDb
{
    public  function trBegin()
    {
        //开启事物
    }

    public  function trCommit()
    {
        //提交事物
    }

    public  function trRollback()
    {
        //事物回滚
    }
}