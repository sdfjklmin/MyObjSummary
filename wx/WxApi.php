<?php


namespace wx;


abstract class WxApi
{
    /**
     * App Id
     * @var string
     */
    protected $app_id     = 'wx06e35b5a3673e99b';

    /**
     * App Secret
     * @var string
     */
    protected $app_secret = '3fb33a2a708ac54bfff9d8e558e6c89a';

    /**
     * 请求返回信息
     * @var
     */
    protected $result;

    abstract public function analyzeRet();

    public function send()
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 40); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            var_dump(curl_error($curl)) ;exit() ;
            #return 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        if($realData) {
            $responseData = json_decode($tmpInfo,true) ;
            if($responseData['code'] == '200') {
                return $responseData['data'] ?? true;
            }
        }
        return $tmpInfo; // 返回数据
    }
}