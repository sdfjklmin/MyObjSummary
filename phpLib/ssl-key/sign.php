<?php

/** md5加密方式,针对非公司系统
 * @return string
 */
function checkMd5($requestData,$signKey)
{
    //签名步骤一：获取参数字符串
    $string = '' ;
    foreach ($requestData as $key => $value) {
        $string .= $key.'='.$value.'&';
    }
    $string = trim($string,'&');
    //签名步骤二：在string后加入KEY
    $string = $string . "&key=".$signKey;
    //签名步骤三：MD5加密
    $string = md5($string);
    //签名步骤四：返回数据
    return $string;
}

class Sign
{
    const SIGN_KEY = 'TRY-SIGN-KEY' ;
    #签名
    public function getSign($arr,$key)
    {
        //签名步骤一：按字典序排序参数
        $string = $this->createSignStr($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;

    }

    /** 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    public  function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /** 生成签名
     * @param $arr
     * @return string
     */
    public function createSignStr($arr)
    {
        ksort($arr);
        $res = urldecode(http_build_query($arr));
        return $res;
    }

}