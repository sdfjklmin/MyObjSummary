<?php

/**
 * Interface SignatureInterface
 */
interface SignatureInterface
{

	/** 加密
	 * @param $password
	 * @return mixed
	 */
	public function encrypt($init, $signId='');


	/** 解密
	 * @param $init
	 * @param string $signHash
	 * @param string $signId
	 * @param string $signKey
	 * @return bool
	 */
	public function decrypt($init, $signHash = '', $signId = '', $signKey = '');

}

/**
 * Class SignatureBase
 */
abstract class SignatureBase implements SignatureInterface
{
    /** 签名后缀key
     * @var string
     */
    protected $sign_suffix_key = '_signSuffixKey';

    /** 随机数
     * @var
     */
    protected $random;

    /** 时间戳
     * @var
     */
    protected $cur_time;

    /** code
     * @var int
     */
    protected $code = 200;

    /** message
     * @var string
     */
    protected $message = 'success';

    /**
     * SignatureBase constructor.
     */
    public function __construct()
    {
        $this->random    = rand();
        $this->cur_time = time();
    }

    /** 设置签名后缀key
     * @param $key
     */
    public function setSignSuffixKey($key)
    {
        $this->sign_suffix_key = '_'.$key;
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

    /** 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    protected function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789!@#%^&*;:-=_+,.";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /** 获取签名内容
     * @return string
     */
    protected function getSignatureContent()
    {
		$nonce   = $this->getNonceStr();
		$rand    = rand();
		$time    = microtime();
		$content = "The signature : nonce={$nonce}; rand={$rand}; time={$time}, by sok yo!";
		return $content;
    }
}

/**
 * Class SignPasswordHash
 * @author sjm
 */
class SignPasswordHash extends SignatureBase
{

	/** 获取信息
	 * @param $passwordHash
	 * @return array
	 */
	public function getInfo($passwordHash)
	{
		/* returns:
		Array (
			[algo] => 1  //算法常量
			[algoName] => bcrypt  // 默认算法.
			[options] => Array ( [cost] => 11 ) //password_hash的操作选项
		)
		*/
		return password_get_info($passwordHash);
	}

	/**
	 * @inheritDoc
	 */
	public function encrypt($init, $signId = '')
	{
				//#操作参数(可省)
		//$options = [
		//    'cost' => 11, # 对应的cost值算法默认为10,可以根据硬件情况进行设置
		//    //'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM), # 加盐设置(建议省掉,函数中已经自动加盐)
		//];
		//可以根据自己的规则,将 $password 先进行 md5,随机字符串,时间戳,json,序列化等操作
		return password_hash( $init, PASSWORD_DEFAULT);
	}

	/**
	 * @inheritDoc
	 */
	public function decrypt($init, $signHash = '', $signId = '', $signKey = '')
	{
		return password_verify($init,$signHash);
	}
}

/**
 * Class SignMd5
 * @author sjm
 */
class SignMd5 extends SignatureBase
{

	/** md5签名
	 * @param $signData array 数据
	 * @param string $signPrefix 前缀
	 * @param string $signSuffix 后缀
	 * @return mixed
	 */
	public function defineMd5($signData, $signPrefix='pre', $signSuffix='suf')
	{
		//$signData 一般来说会包含随机数和时间戳或者随机字符串
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

	/**
	 * @inheritDoc
	 */
	public function encrypt($init, $signId = '')
	{
		return $this->defineMd5($init);
	}

	/**
	 * @inheritDoc
	 */
	public function decrypt($init, $signHash = '', $signId = '', $signKey = '')
	{
		return $this->defineMd5($init) == $signHash;
	}
}

/**
 * Class SignSha1
 * @author sjm
 */
class SignSha1 extends SignatureBase
{

	/** 简单的sha1签名
	 *  一般会通过参数或者请求头信息的方式来传递secret_id,nonce,cur_time
	 * @example
	 *   $headers = [
	 *          'headers' => [
	 *          'AppKey' => $secret_id,
	 *          'Nonce' => $nonce ,
	 *          'CurTime' => $cur_time ,
	 *          'CheckSum' => $this->simple($secretKey,$nonce,$curTime) ,
	 *          //'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
	 *          ],
	 *     ];
	 * @param $secretKey
	 * @param $nonce
	 * @param $curTime
	 * @return string
	 */
	public function simpleHeader($secretKey,$nonce,$curTime)
	{
		return sha1($secretKey.$nonce.$curTime);
	}

	/** 生成sha1的secret
	 * @other 绑定生成的 内容，secret_key，secret_id
	 * @return string
	 */
	public function create()
	{
		$content = $this->getSignatureContent();
		//生成签名内容
		file_put_contents('signature.txt', $content);
		$signature = hash_hmac_file('SHA1','signature.txt','secret');
		unlink('signature.txt');
		return $signature;
	}

	/** sha1数据加密
	 * @deprecated 使用 encrypt
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
	 * @deprecated  使用 decrypt
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
		$signStr  = substr($sign,20);//截取掉加密的字符串,获取明文参数: key=value&key2=name2
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

	/**
	 * @param $secret_id string
	 * @param string $secret_key
	 * @return mixed|string
	 * @inheritDoc
	 */
	public function encrypt($secret_id, $secret_key = '')
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

	/**
	 * @inheritDoc
	 */
	public function decrypt($initSign, $signHash = '', $secretId = '', $secretKey = '')
	{
		//先做 base64 解码，拿到数据
		$sign     = base64_decode($initSign);
		//前 5 个单元是 sha1 ，这里的代码单元默认为:编码+16进制（0041），5个单元 = 5 * 4(字节) = 20(字节)
		$signStr  = substr($sign,20);//截取掉加密的字符串,获取明文参数: key=value&key2=name2
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

/**
 * Class SignatureFactory
 * @author sjm
 */
class SignatureFactory
{
	//签名类常量
	const SIGN_MD5       = 'SignMd5';
	const SIGN_SHA1      = 'SignSha1';
	const SIGN_PASS_HASH = 'SignPasswordHash';

	/** 当前签名类
	 * @var SignatureInterface
	 */
	private $sign_model;

	/**
	 * SignatureFactory constructor.
	 * @param $way
	 */
	public function __construct($way)
	{
		if(!in_array($way,[self::SIGN_MD5,self::SIGN_SHA1,self::SIGN_PASS_HASH])) {
			echo "please input you sign way","\n";exit();
		}
		if(!class_exists($way)) {
			echo "the {$way}  Class not exists";exit();
		}
		$temp = new $way;
		if(!$temp instanceof SignatureInterface) {
			echo "the {$way} must be  instanceof SignatureInterface";exit();
		}
		$this->sign_model = $temp;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->sign_model,$name],$arguments);
	}

}
//----------------------------
// 使用示例					 |
//----------------------------

// md5
/** @var SignMd5 $md5Model */
/*$md5Model = new SignatureFactory('SignMd5');
$md5Encrypt = $md5Model->encrypt(['a'=>1,'b=2']);//内部使用 http_build_query(), 这里要使用数组
var_dump($md5Encrypt,$md5Model->decrypt(['a'=>1,'b =2'],$md5Encrypt));
exit();*/

// password_hash
/** @var SignPasswordHash $passModel */
/*$passModel = new SignatureFactory(SignatureFactory::SIGN_PASS_HASH);
$passEncrypt = $passModel->encrypt('ab123456');
var_dump($passEncrypt,$passModel->decrypt('ab123456',$passEncrypt));
exit();*/

// sha1
/** @var SignSha1 $sha1Model */
/*$sha1Model   = new SignatureFactory(SignatureFactory::SIGN_SHA1);
$secretId    = 123456;
$secretKey   = 'f76f58e3ba30ec46f5b3265c2f3989851c5c582a';
$sha1Encrypt = $sha1Model->encrypt($secretId, $secretKey);
var_dump($sha1Encrypt, $sha1Model->decrypt($sha1Encrypt, '', $secretId, $secretKey));
exit();*/


//----------------------------
// 异常处理					 |
//----------------------------
//php5中无法捕捉异常,注册关闭函数来进行错误监控
//注册一个会在php终止时执行的函数
/*register_shutdown_function(function () {
	//获取最后发生的错误
	$error = error_get_last();
	if (!empty($error)) {
		print_r($error);
	}
});*/

//我们还可以通过 set_error_handler() 把一些Deprecated、Notice、Waning等错误包装成异常，让 try {} catch 能够捕获到。
/*error_reporting(E_ALL);
ini_set('display_errors', 'on');
//捕获Deprecated、Notice、Waning级别错误
set_error_handler(function ($errno, $errstr, $errfile) {
	throw new \Exception($errno . ' : ' . $errstr . ' : ' . $errfile);
	//返回true，表示错误处理不会继续调用
});

try {
	$data = [];
	echo $data['index'];
} catch (\Exception $e) {
	//捕获Notice: Undefined index
	echo $e->getMessage();
}
exit();*/
