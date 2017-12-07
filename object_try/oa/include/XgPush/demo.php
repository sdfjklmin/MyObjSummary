<?php
require_once ('XingeApp.php');

var_dump(DemoPushSingleDeviceNotification());
// var_dump(DemoPushSingleDeviceMessage());
// var_dump(DemoPushSingleDeviceIOS());
// var_dump(DemoPushSingleAccount());
// var_dump(DemoPushAccountList());
// var_dump(DemoPushSingleAccountIOS());
// var_dump(DemoPushAllDevices());
// var_dump(DemoPushTags());
// var_dump(DemoQueryPushStatus());
// var_dump(DemoQueryDeviceCount());
// var_dump(DemoQueryTags());
// var_dump(DemoQueryTagTokenNum());
// var_dump(DemoQueryTokenTags());
// var_dump(DemoCancelTimingPush());
// var_dump(DemoBatchDelTag());
// var_dump(DemoBatchSetTag());
// var_dump(DemoPushAccountListMultipleNotification());
// var_dump(DemoPushDeviceListMultipleNotification());
// var_dump(DemoQueryInfoOfToken());
// var_dump(DemoQueryTokensOfAccount());
// var_dump(DemoDeleteTokenOfAccount());
// var_dump(DemoDeleteAllTokensOfAccount());


//单个设备下发通知消息
function DemoPushSingleDeviceNotification()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setType(Message::TYPE_NOTIFICATION);
	$mess->setTitle("title");
	$mess->setContent("中午");
	$mess->setExpireTime(86400);
	//$style = new Style(0);
	#含义：样式编号0，响铃，震动，不可从通知栏清除，不影响先前通知
	$style = new Style(0,1,1,0,0);
	$action = new ClickAction();
	$action->setActionType(ClickAction::TYPE_URL);
	$action->setUrl("http://xg.qq.com");
	#打开url需要用户确认
	$action->setComfirmOnUrl(1);
	$custom = array('key1'=>'value1', 'key2'=>'value2');
	$mess->setStyle($style);
	$mess->setAction($action);
	$mess->setCustom($custom);
	$acceptTime1 = new TimeInterval(0, 0, 23, 59);
	$mess->addAcceptTime($acceptTime1);
	$ret = $push->PushSingleDevice('token', $mess);
	return($ret);
}

//单个设备下发透传消息       注：透传消息默认不展示
function DemoPushSingleDeviceMessage()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_MESSAGE);
	$ret = $push->PushSingleDevice('token', $mess);
	return $ret;
}

//下发IOS设备消息
function DemoPushSingleDeviceIOS()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new MessageIOS();
	$mess->setExpireTime(86400);
	//$mess->setSendTime("2014-03-13 16:00:00");
	$mess->setAlert("ios test");
	//$mess->setAlert(array('key1'=>'value1'));
	$mess->setBadge(1);
	$mess->setSound("beep.wav");
	$custom = array('key1'=>'value1', 'key2'=>'value2');
	$mess->setCustom($custom);
	$acceptTime = new TimeInterval(0, 0, 23, 59);
	$mess->addAcceptTime($acceptTime);
	$raw = '{"xg_max_payload":1,"accept_time":[{"start":{"hour":"20","min":"0"},"end":{"hour":"23","min":"59"}}],"aps":{"alert":"="}}';
	$mess->setRaw($raw);
	$ret = $push->PushSingleDevice('token', $mess, XingeApp::IOSENV_DEV);
	return $ret;
}

//单个设备下发通知Intent
//setIntent()的内容需要使用intent.toUri(Intent.URI_INTENT_SCHEME)方法来得到序列化后的Intent(自定义参数也包含在Intent内）
//终端收到后通过intent.parseUri()来反序列化得到Intent
function DemoPushSingleDeviceNotificationIntent()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setType(Message::TYPE_NOTIFICATION);
	$mess->setTitle("title");
	$mess->setContent("通知点击执行Intent测试");
	$style = new Style(0);
	$style = new Style(0,1,1,0);
	$action = new ClickAction();
	$action->setActionType(ClickAction::TYPE_INTENT);
	$action->setIntent('intent:10086#Intent;scheme=tel;action=android.intent.action.DIAL;S.key=value;end');
	$mess->setStyle($style);
	$mess->setAction($action);
	$ret = $push->PushSingleDevice('token', $mess);
	return($ret);
}


//下发单个账号
function DemoPushSingleAccount()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_MESSAGE);
	$ret = $push->PushSingleAccount(0, 'joelliu', $mess);
	return ($ret);
}

//下发多个账号， IOS下发多个账号参考DemoPushSingleAccountIOS进行相应修改
function DemoPushAccountList()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_MESSAGE);
	$accountList = array('joelliu');
	$ret = $push->PushAccountList(0, $accountList, $mess);
	return ($ret);
}

//下发IOS账号消息
function DemoPushSingleAccountIOS()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new MessageIOS();
	$mess->setExpireTime(86400);
	$mess->setAlert("ios test");
	//$mess->setAlert(array('key1'=>'value1'));
	$mess->setBadge(1);
	$mess->setSound("beep.wav");
	$custom = array('key1'=>'value1', 'key2'=>'value2');
	$mess->setCustom($custom);
	$acceptTime1 = new TimeInterval(0, 0, 23, 59);
	$mess->addAcceptTime($acceptTime1);
	$ret = $push->PushSingleAccount(0, 'joelliu', $mess, XingeApp::IOSENV_DEV);
	return $ret;
}

//下发所有设备
function DemoPushAllDevices()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setType(Message::TYPE_NOTIFICATION);
	$mess->setTitle("title");
	$mess->setContent("中午");
	$mess->setExpireTime(86400);
	$style = new Style(0);
	#含义：样式编号0，响铃，震动，不可从通知栏清除，不影响先前通知
	$style = new Style(0,1,1,0,0);
	$action = new ClickAction();
	$action->setActionType(ClickAction::TYPE_URL);
	$action->setUrl("http://xg.qq.com");
	#打开url需要用户确认
	$action->setComfirmOnUrl(1);
	$mess->setStyle($style);
	$mess->setAction($action);
	
	$ret = $push->PushAllDevices(0, $mess);
	return ($ret);
}

//下发标签选中设备
function DemoPushTags()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_MESSAGE);
	$tagList = array('Demo3');
	$ret = $push->PushTags(0, $tagList, 'OR', $mess);
	return ($ret);
}

//查询消息推送状态
function DemoQueryPushStatus()
{
	$push = new XingeApp(000, 'secret_key');
	$pushIdList = array('31','32');
	$ret = $push->QueryPushStatus($pushIdList);
	return ($ret);
}

//查询设备数量
function DemoQueryDeviceCount()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryDeviceCount();
	return ($ret);
}

//查询标签
function DemoQueryTags()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryTags(0,100);
	return ($ret);
}

//查询某个tag下token的数量
function DemoQueryTagTokenNum()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryTagTokenNum("tag");
	return ($ret);
}

//查询某个token的标签
function DemoQueryTokenTags()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryTokenTags("token");
	return ($ret);
}

//取消定时任务
function DemoCancelTimingPush()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->CancelTimingPush("32");
	return ($ret);
}

// 设置标签
function DemoBatchSetTag() {
	// 切记把这里的示例tag和示例token修改为你的真实tag和真实token
    $pairs = array();
    array_push($pairs, new TagTokenPair("tag1","token00000000000000000000000000000000001"));
    array_push($pairs, new TagTokenPair("tag1","token00000000000000000000000000000000001"));

	$push = new XingeApp(000, 'secret_key');
    $ret = $push->BatchSetTag($pairs);
    return $ret;
}

// 删除标签
function DemoBatchDelTag() {
	// 切记把这里的示例tag和示例token修改为你的真实tag和真实token
    $pairs = array();
    array_push($pairs, new TagTokenPair("tag1","token00000000000000000000000000000000001"));
    array_push($pairs, new TagTokenPair("tag1","token00000000000000000000000000000000001"));

    $push = new XingeApp(000, 'secret_key');
    $ret = $push->BatchDelTag($pairs);
    return $ret;
}
    
//大批量下发给账号 android
//iOS 请构建MessageIOS 消息
function DemoPushAccountListMultipleNotification()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_NOTIFICATION);
	$ret = $push->CreateMultipush($mess, XingeApp::IOSENV_DEV);
	if (!($ret['ret_code'] === 0))
		return $ret;
	else
	{
		$result=array();
		$accountList1 = array('joelliu', 'joelliu2', 'joelliu3');
		array_push($result, $push->PushAccountListMultiple($ret['result']['push_id'], $accountList1));
		$accountList2 = array('joelliu4', 'joelliu5', 'joelliu6');
		array_push($result, $push->PushAccountListMultiple($ret['result']['push_id'], $accountList2));
		return ($result);
	}
}

//大批量下发给设备 android
//iOS 请构建MessageIOS 消息
function DemoPushDeviceListMultipleNotification()
{
	$push = new XingeApp(000, 'secret_key');
	$mess = new Message();
	$mess->setExpireTime(86400);
	$mess->setTitle('title');
	$mess->setContent('content');
	$mess->setType(Message::TYPE_NOTIFICATION);
	$ret = $push->CreateMultipush($mess, XingeApp::IOSENV_DEV);
	if (!($ret['ret_code'] === 0))
		return $ret;
	else
	{
		$result=array();
		$deviceList1 = array('token1', 'token2', 'token3');
		array_push($result, $push->PushDeviceListMultiple($ret['result']['push_id'], $deviceList1));
		$deviceList2 = array('token4', 'token5', 'token6');
		array_push($result, $push->PushDeviceListMultiple($ret['result']['push_id'], $deviceList2));
		return ($result);
	}
}

//查询某个token的信息
function DemoQueryInfoOfToken()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryInfoOfToken("token");
	return ($ret);
}

//查询某个account绑定的token
function DemoQueryTokensOfAccount()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->QueryTokensOfAccount("nickName");
	return ($ret);
}

//删除某个account绑定的token
function DemoDeleteTokenOfAccount()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->DeleteTokenOfAccount("nickName", "token");
	return ($ret);
}

//删除某个account绑定的所有token
function DemoDeleteAllTokensOfAccount()
{
	$push = new XingeApp(000, 'secret_key');
	$ret = $push->DeleteAllTokensOfAccount("nickName");
	return ($ret);
}