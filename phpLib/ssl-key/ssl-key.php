<?php
namespace MyObjSummary;
/**
 * 使用openssl实现非对称加密
 * 
 * @since 2015-11-10
 */
class Rsa
{
    /**
     * 私钥
     * 
     */
    private $_privKey;

    /**
     * 公钥
     * 
     */
    private $_pubKey;

    /** 保存文件地址
     * @var
     */
    private $_keyPath;

    /** 公钥
     * @var string
     */
    private $_pubKeyLink = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCF4sz1eu4XgLeIK9Aiu4+rfglt
k1gmNhUytOtk3kbzPoy2XoR5sQIRXBYnIagwBVOLPWDacVJoqjfeK6xGvL17745u
wNSw3eKLl1qm+w2z5KhNEnpgLWxKxSPMfekt1Aj3Te0Ct652Scr42Coca/ld2mGk
Z7RubcZIW62ocgX3swIDAQAB
-----END PUBLIC KEY-----";
    /**私钥
     * @var string
     */
    private $_priKeyLink = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCF4sz1eu4XgLeIK9Aiu4+rfgltk1gmNhUytOtk3kbzPoy2XoR5
sQIRXBYnIagwBVOLPWDacVJoqjfeK6xGvL17745uwNSw3eKLl1qm+w2z5KhNEnpg
LWxKxSPMfekt1Aj3Te0Ct652Scr42Coca/ld2mGkZ7RubcZIW62ocgX3swIDAQAB
AoGAHinbvU6Fx5vDPZWJXdnd42gQ3bP9fxZeLj9ebSo61+B2uTuQIw6DBcA2aXiG
uNLqYItif7RaOaRn09EJDiLFmYwRBXAGnEdSnxWRy/IMrtKATV+dLnyFDVrIzsn+
/9l3HQXKhlSqTc4v7o1sWAM9GW2vjB3X432BjzbgqCyplOECQQC7UnvQUZYT+sum
PStREJt85krUKgeFwyQdji+BdAXhv9xz3PiSWsAvw87zFrpBKcWbTimSH38onKGa
htuYE08xAkEAtvjx7t05TiVusPcsgABxoABKRKZpcY5QQIXTT3oigvCMuz41nBDm
EXeot+TXBGwG0QNS7p5BwkrXfCFJJONkIwJAUbcItfZxPqQAJLO4arOQ8KpRaD4x
a+OVpKL7DEC9tB4LICv773RRNET5yUdX1sdPIZG2Rr0grmmtgYhk0PFTcQJBAI8I
zRgNmF6epIlysDbgIfMSRvyjmopK1jGabDYVJCV4Jou5de8qxq+g+yPGoM+0IyAB
mTMwQ/e+yfUfkC5mFEECQF+5uyZZh/hCDLNJQPHNC7nb0/cWbSXh8kJ6YcVdXMYz
uv2VL3fMBI4SGWWN/LPSeZkUdPbh0GmRCSo4nPOfxK8=
-----END RSA PRIVATE KEY-----";
    /**
     * Rsa constructor.
     * @param string $path
     */
    public function __construct($path='')
    {
        if (!empty($path)) {
            $this->_keyPath = $path;
        }
    }

    /**
     * 创建公钥和私钥
     * 
     */
    public function createKey()
    {
        $config = [
            "config" => 'D:\Min\Install\wamp\wamp64\bin\php\php5.6.25\extras\ssl\openssl.cnf',
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,

        ];
        // 生成私钥
        $rsa = openssl_pkey_new($config);
        openssl_pkey_export($rsa, $privKey, NULL, $config);
        file_put_contents($this->_keyPath . DIRECTORY_SEPARATOR . 'priv.key', $privKey);
        $this->_privKey = openssl_pkey_get_public($privKey);
        // 生成公钥
        $rsaPri = openssl_pkey_get_details($rsa);
        $pubKey = $rsaPri['key'];
        file_put_contents($this->_keyPath . DIRECTORY_SEPARATOR . 'pub.key', $pubKey);
        $this->_pubKey = openssl_pkey_get_public($pubKey);
    }

    /** 设置私钥
     * @return bool
     */
    public function setupPrivKey()
    {
        if (is_resource($this->_privKey)) {
            return true;
        }
        //从文件中获取
        /*$file = $this->_keyPath . DIRECTORY_SEPARATOR . 'priv.key';
        $privKey = file_get_contents($file);*/
        $privKey = $this->_priKeyLink;
        $this->_privKey = openssl_pkey_get_private($privKey);
        return true;
    }

    /** 设置公钥
     * @return bool
     */
    public function setupPubKey()
    {
        //从文件中获取
        /*$file = $this->_keyPath . DIRECTORY_SEPARATOR . 'pub.key';
        $pubKey = file_get_contents($file);*/
        //数据源
        $pubKey = $this->_pubKeyLink;
        $this->_pubKey = openssl_pkey_get_public($pubKey);
        return true;
    }

    /** 用私钥加密
     * @param $data
     * @return null|string
     */
    public function privEncrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }
        $this->setupPrivKey();
        $result = openssl_private_encrypt($data, $encrypted, $this->_privKey);
        if ($result) {
            return base64_encode($encrypted);
        }
        return null;
    }

    /** 私钥解密
     * @param $encrypted
     * @return null
     */
    public function privDecrypt($encrypted)
    {
        if (!is_string($encrypted)) {
            return null;
        }
        $this->setupPrivKey();
        $encrypted = base64_decode($encrypted);
        $result = openssl_private_decrypt($encrypted, $decrypted, $this->_privKey);
        if ($result) {
            return $decrypted;
        }
        return null;
    }

    /** 公钥加密
     * @param $data
     * @return null|string
     */
    public function pubEncrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }
        $this->setupPubKey();
        $result = openssl_public_encrypt($data, $encrypted, $this->_pubKey);
        if ($result) {
            return base64_encode($encrypted);
        }
        return null;
    }

    /** 公钥解密
     * @param $crypted
     * @return null
     */
    public function pubDecrypt($crypted)
    {
        if (!is_string($crypted)) {
            return null;
        }
        $this->setupPubKey();
        $crypted = base64_decode($crypted);
        $result = openssl_public_decrypt($crypted, $decrypted, $this->_pubKey);
        if ($result) {
            return $decrypted;
        }
        return null;
    }

    /** 私钥签名
     * @param $data
     * @return string
     */
    public function priKeySign($data)
    {
        if(!is_string($data)) return null;
        $private_key=openssl_get_privatekey($this->_priKeyLink);
        $original_str= $data ;//原数据
        openssl_sign($original_str,$sign,$private_key);
        openssl_free_key($private_key);
        $sign=base64_encode($sign);//最终的签名
        return $sign ;
    }

    /** 公钥验签
     * @param $sign
     * @param $data
     * @return bool
     */
    public  function pubKeyCheck($sign,$data)
    {
        if(!is_string($sign) || !is_string($data)) return null;
        $public_key=openssl_get_publickey($this->_pubKeyLink);
        $sign=base64_decode($sign);//得到的签名
        $original_str=$data;
        $result=(bool)openssl_verify($original_str,$sign,$public_key);
        openssl_free_key($public_key);
        return $result ;
    }

    /**
     * __destruct
     * 
     */
    public function __destruct() {
        @fclose($this->_privKey);
        @fclose($this->_pubKey);
    }
}
$rsa = new Rsa();

echo "openssl_private_encrypt,openssl_public_decrypt","<br />";
//私钥加密，公钥解密
echo "私钥加密,公钥验签","<br />";
echo "待加密数据：testInfo","<br />";
$pre = $rsa->privEncrypt("testInfo");
echo "加密后的密文:<br />" . $pre . "<br />";
$pud = $rsa->pubDecrypt($pre);
echo "解密后数据:" . $pud . "<br />";
echo "<hr>";


//公钥加密，私钥解密
echo "openssl_public_encrypt,openssl_private_decrypt","<br />";
echo "公钥加密，私钥验签","<br />";
echo "待加密数据：ssh-test","<br />";
$pue = $rsa->pubEncrypt("ssh-test");
echo "加密后的密文:","<br />" . $pue . "<br />";
$prd = $rsa->privDecrypt($pue);
echo "解密后数据:" . $prd;

echo "<hr>";echo "<hr>";

echo "openssl_sign,openssl_verify","<br />";
echo "私钥签名,公钥验签","<br />";
echo "待加密数据：test=32","<br />";
$pre = $rsa->priKeySign('test=32');
echo "加密后的密文:","<br />" . $pre . "<br />";
$pud = $rsa->pubKeyCheck($pre,'test=32');
echo "是否解密成功:" . $pud . "<br />";
echo "<hr>";