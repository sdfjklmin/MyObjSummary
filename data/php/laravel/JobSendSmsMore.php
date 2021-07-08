<?php

//假设有一个队列处理器用来给用户发送手机短信。
//信息发送后，处理器会记录消息日志以便保存给用户发送过的所有消息历史。
//业务分析:
//  队列 -> 给用户 -> 发送短信 -> 记录日志 -> 删除队列
//  给用户 -> 基于用户的外部联系方式 -> 手机号 | 邮箱 | ...
//  发送短信 -> 由运营商提供 -> 短信模板 -> 发送短信
//核心思路:
//  划分责任    =>  易于测试、扩展和修改  ->  通过责任划分，将单一功能抽象成类
//  抽离接口    =>  降低业务耦合度、形成公用组件  ->  一些公用组件应该基于接口编程(日志[File|Db|Redis|...]、消息[MQ|Middle]、短信[腾讯|阿里]、邮件[Email|QQMail|GMail]等)
//  确定依赖    =>  复用可用对象  ->  确定责任之间的依赖，A是否依赖于B，如果是尽可能地通过 依赖注入、服务容器、依赖接口 来实现
//  服务容器    =>  全局性的功能业务，可以抽离到服务容器中进行公有化
//  业务纽带    =>  当A于B相互纠缠，难舍难分时，可以考虑让C、D。。。介入，成为它们之间的纽带

namespace JobSendSmsMore;

/** 队列系统和真正的业务逻辑之间转换层
 * 类比为 Controller
 * Class Job
 * @package JobSendSmsMore
 */
class Job
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var SmsInterface
     */
    protected $smsCourier;

    public function __construct(User $user, SmsInterface $smsCourier)
    {
        $this->user = $user;

        $this->smsCourier = $smsCourier;
    }

    //通过脚本执行的入口
    public function kernel($data)
    {
        //获取用户，发送短信
        $userId = $data['user_id'];
        $user = (new User())->find($userId);
        $user->sendMessage($this->smsCourier, '测试');

        //删除脚本任务
        $this->delete();
    }

    public function delete()
    {

    }
}

interface SmsInterface
{
    public function send($phone, $message = '');

    public function log($params);
}

class Sms implements SmsInterface
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

    /** 发送短信
     * 以用户类的方法为入口: 基于用户类，如果想把 phone 变成 email 则比较容易。
     * 短信接口为二次入口: 其它逻辑也可以使用单一的发送短信功能。
     * @param SmsInterface $sms
     * @param $message
     */
    public function sendMessage(SmsInterface $sms, $message)
    {
        $sms->send($this->phone, $message);
        $sms->log($message);
    }
}