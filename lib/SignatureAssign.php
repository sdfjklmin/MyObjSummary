<?php

/** build sign and assign sign.
 * Class SignatureAssign
 */
class SignatureAssign
{
    /** 签名后缀key
     * @var string
     */
    protected $sign_suffix_key = '_signSuffixKey';

    /** code
     * @var int
     */
    protected $code = 200;

    /** message
     * @var string
     */
    protected $message = 'success';

    /** md5签名
     * @param $signData array 数据
     * @param string $signPrefix 前缀
     * @param string $signSuffix 后缀
     * @return mixed
     */
    public function signMd5($signData, $signPrefix='pre', $signSuffix='suf')
    {
        //签名步骤一：按字典序排序参数
        $string = $this->signSort($signData);
        //签名步骤二：在string后加入KEY
        $string = $this->signFix($string,$signPrefix,$signSuffix);
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /** 签名固定串
     * @param $string
     * @param $signPrefix
     * @param $signSuffix
     * @return string
     */
    protected function signFix($string,$signPrefix,$signSuffix)
    {
        //签名步骤三：前缀拼接
        if($signPrefix) {
            $string = $signPrefix.$string;
        }
        //签名步骤四：后缀拼接,在string后加入_signKey
        if($signSuffix) {
            $string = $string."&{$this->sign_suffix_key}=".$signSuffix;
        }
        return $string;
    }

    /** 生成签名数据字符串
     * @param $arr
     * @return string
     */
    protected function signSort($arr)
    {
        ksort($arr);
        $res = urldecode(http_build_query($arr));
        //http_build_query,parse_str
        return $res;
    }

    /** 设置签名后缀key
     * @param $key
     */
    public function setSignSuffixKey($key)
    {
        $this->sign_suffix_key = '_'.$key;
    }


    /** 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    public function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789!@#%^&*;:-=_+,.";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /** 获取处理code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /** 获取处理message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /** 生成sha1的secret
     * @other 绑定生成的 内容，secret_key，secret_id
     * @return string
     */
    public function signSha1Create()
    {
        $nonce = $this->getNonceStr();
        $rand  = rand();
        $time  = time();
        $content = "The signature : nonce={$nonce}; rand={$rand}; time={$time}, by sok yo!";
        //生成签名内容
        file_put_contents('signature.txt', $content);
        $signature = hash_hmac_file('SHA1','signature.txt','secret');
        unlink('signature.txt');
        return $signature;
    }

    /** sha1数据加密
     * @param $secret_id
     * @param $secret_key
     * @return string
     */
    public function signSha1Encrypt($secret_id,$secret_key)
    {
        $time = time();
        // 向参数列表填入参数
        $arg_list = array(
            "secretId" => $secret_id,
            "currentTimeStamp" => $time,
            "expireTime" => $time+86400,
            "random" =>rand(),
        );
        $init = http_build_query($arg_list);
        // 计算签名
        // raw_output = false ; 53e55273e57c3cf6b7c16e5840479566be3aa0d2,16进制小写字符串格式（40个字符）
        // raw_output = true  ; �� Ej���۔����̠��,原始二进制数据（20个字符）
        // base64_encode 设计此种编码是为了使二进制数据可以通过非纯 8-bit 的传输层传输(防止特殊字符在传输过程中被转义)。
        // base64_encode 数据要比原始数据多占用 33% 左右的空间。
        $signature = base64_encode(hash_hmac('SHA1', $init, $secret_key, true).$init);
        // JS CryptoJS
        return $signature ;
    }

    /** sha1数据解密
     * @param $initSign
     * @param $secretId
     * @param $secretKey
     * @return bool
     */
    public function signSha1Decrypt($initSign,$secretId,$secretKey)
    {
        //先做 base64 解码，拿到数据
        $sign     = base64_decode($initSign);
        //前 5 个单元是 sha1 ，这里的代码单元默认为:编码+16进制（0041），5个单元 = 5 * 4(字节) = 20(字节)
        $signStr  = substr($sign,20);
        //数组形式
        parse_str($signStr,$signArr);
        //逻辑判断
        $mustKey = ['secretId','currentTimeStamp','expireTime','random'];
        foreach ($mustKey as $k) {
            if(!isset($signArr[$k])) {
                $this->code = 400;
                $this->message = 'miss '.$k;
                return false;
            }
        }
        //secretId
        if((string)$signArr['secretId'] !== (string)$secretId) {
            $this->code = 400;
            $this->message = 'error secretId';
            return false;
        }
        //时间
        if($signArr['currentTimeStamp'] > $signArr['expireTime']) {
            $this->code = 400;
            $this->message = 'error currentTimeStamp';
            return false;
        }
        //过期
        if($signArr['expireTime'] < time()) {
            $this->code = 400;
            $this->message = 'expireTime expired';
            return false;
        }
        // 计算签名
        $signature = base64_encode(hash_hmac('SHA1', $signStr, $secretKey, true).$signStr);
        if($signature === $initSign) {
            $this->code = 200;
            $this->message = 'success';
            return true;
        }else{
            $this->code = 400;
            $this->message = 'error signature';
            return false;
        }
    }
}
$model = new SignatureAssign();
$secretKey = '82c7100ce6adbe9bdf253fd8b6f915cfd51c5423';
$secretId  = 'abc1234Def453';
$checkSign = $model->signSha1Encrypt($secretId,$secretKey);
var_dump($checkSign);