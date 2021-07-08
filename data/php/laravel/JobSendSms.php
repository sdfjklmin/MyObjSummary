<?php

//假设有一个队列处理器用来给用户发送手机短信。
//信息发送后，处理器会记录消息日志以便保存给用户发送过的所有消息历史。

namespace JobSendSms;

class Job
{
    //通过脚本执行的入口
    public function kernel($data)
    {
        //获取用户信息
        $userId = $data['user_id'];
        $user = (new User())->find($userId);

        //发送短信
        $sms = new Sms();
        $sms->send($user->phone, '测试');

        //记录日志
        $sms->log(['test']);

        //删除脚本任务
        $this->delete();
    }

    public function delete()
    {
        //删除队列任务
    }
}


class Sms
{

    public function send($phone, $message = '')
    {

    }

    public function log($params)
    {

    }
}



/**
 * Class User
 * @date 2021/7/7 17:55
 * @author shaojm
 * @package JobSendSms
 * @property string $phone
 */
class User
{
    protected $phone;

    public function find($id): User
    {
        return $this;
    }
}