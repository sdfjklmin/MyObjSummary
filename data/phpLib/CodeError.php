<?php

/**
 * Interface BaseCode
 */
interface BaseCode
{
    public function initCode():array ;
}

/**
 * Class MessageCode
 * @author sjm
 */
class MessageCode implements BaseCode
{

    public function initCode(): array
    {
        return  [
            'tip'=>'1xx:消息响应,服务器收到请求,需要请求者继续执行操作',
            '100'=>'Continue : 继续',
            '101'=>'Switching Protocols : 交换协议',
            '102'=>'Processing : 处理中',
        ];
    }
}

/**
 * Class SuccessCode
 * @author sjm
 */
class SuccessCode implements BaseCode
{

    public function initCode(): array
    {
        return [
            "tip"=>"2xx:操作被成功接收并处理,但有些(202)只表示接受了一个请求并正在异步处理,而不代表真正的成功",
            "200"=>"OK : 成功",
            "201"=>"Created : 已创建,通常是 PUT 得到的响应码",
            "202"=>"Accepted : 已接受请求，但尚未处理",
            "203"=>"Non-Authoritative Information : 非权威信息|未授权信息",
            "204"=>"No Content : 没有内容",
            "205"=>"Reset Content : 重置内容",
            "206"=>"Partial Content : 部分内容",
            "207"=>"Multi-Status : 多状态",
            "208"=>"Already Reported : 已经报告",
            "226"=>"IM Used : IM已使用"
        ];
    }
}

/**
 * Class RedirectCode
 * @author sjm
 */
class RedirectCode implements BaseCode
{

    public function initCode(): array
    {
        return [
            "tip"=>"3xx:重定向,需要进一步的操作以完成请求",
            "300"=>"Multiple Choices : 多种选择",
            "301"=>"Moved Permanently : 永久移动",
            "302"=>"Found : 发现",
            "303"=>"See Other : 见其他",
            "304"=>"Not Modified : 未修改",
            "305"=>"Use Proxy : 使用代理",
            "306"=>"Switch Proxy : 后续请求应使用指定的代理",
            "307"=>"Temporary Redirect : 临时重定向",
            "308"=>"Permanent Redirect : 永久重定向"
        ];
    }
}

/**
 * Class ClientCode
 * @author sjm
 */
class ClientCode implements BaseCode
{

    public function initCode(): array
    {
        return  [
            "tip"=>"4xx:客户端错误|请求错误,包含语法错误或无法完成请求",
            "400"=>"Bad Request : 错误请求",
            "401"=>"Unauthorized : 未经授权|未认证,即用户没有必要的凭据",
            "402"=>"Payment Required : 需要付款,该状态码是为了将来可能的需求而预留的",
            "403"=>"Forbidden : 禁止,服务器已经接受请求,但是拒绝执行它",
            "404"=>"Not Found : 请求失败,请求所希望得到的资源未被在服务器上发现,但允许用户的后续请求",
            "405"=>"Method Not Allowed : 方法不允许,请求行中指定的请求方法不能被用于请求相应的资源",
            "406"=>"Not Acceptable : 不可接受,请求的资源的内容特性无法满足请求头中的条件,因而无法生成响应实体,该请求不可接受",
            "407"=>"Proxy Authentication Required : 需要代理验证,与401响应类似,只不过客户端必须在代理服务器上进行身份验证",
            "408"=>"Request Timeout : 请求超时",
            "409"=>"Conflict : 冲突,表示因为请求存在冲突无法处理该请求,例如多个同步更新之间的编辑冲突",
            "410"=>"Gone : 请求的资源不再可用",
            "411"=>"Length Required : 拒绝在没有定义Content-Length头的情况下接受请求",
            "412"=>"Precondition Failed : 前提条件失败,验证失败",
            "413"=>"Request Entity Too Large : 有效载荷过大",
            "414"=>"Request-URI Too Long : 表示请求的URI长度超过了服务器能够解释的长度,因此服务器拒绝对该请求提供服务",
            "415"=>"Unsupported Media Type : 不支持的媒体类型",
            "416"=>"Requested Range Not Satisfiable : 请求的范围不满意",
            "417"=>"Expectation Failed : 期望失败,在请求头Expect中指定的预期内容无法被服务器满足,或者这个服务器是一个代理服显的证据证明在当前路由的下一个节点上,Expect的内容无法被满足",
            "418"=>"I‘m a teapot : 我是一个茶壶",
            "420"=>"Enhance Your Caim : 客户端被限速的情况下返回",
            "421"=>"Misdirected Request : 该请求针对的是无法产生响应的服务器（例如因为连接重用）",
            "422"=>"Unprocessable Entity : 不可处理的实体,请求格式正确，但是由于含有语义错误，无法响应",
            "423"=>"Locked : 已锁定,当前资源被锁定",
            "424"=>"Failed Dependency : 依赖关系失败",
            "426"=>"Upgrade Required : 需要升级",
            "428"=>"必备前提条件",
            "429"=>"Too Many Requests : 请求太多,用户在给定的时间内发送了太多的请求,旨在用于网络限速",
            "431"=>" Request Header Fields Too Large : 请求标头字段太大,服务器不愿处理请求,因为一个或多个头字段过大",
            "444"=>"No Response : 连接已关闭但没有响应",
            "451"=>"因法律原因不可用",
            "499"=>"客户关闭请求"
        ];
    }
}

/**
 * Class ServerCode
 * @author sjm
 */
class ServerCode implements BaseCode
{

    public function initCode(): array
    {
        return [
            "tip"=>"5xx:服务器错误,服务器在处理请求的过程中发生了错误",
            "500"=>"Internal Server Error : 内部服务器错误",
            "501"=>"Not Implemented : 未实施,服务器不支持当前请求所需要的某个功能",
            "502"=>"BadGateway : 作为网关或者代理工作的服务器尝试执行请求时,从上游服务器接收到无效的响应",
            "503"=>"Service Unavailable : 服务不可用,由于临时的服务器维护或者过载,服务器当前无法处理请求",
            "504"=>"Gateway Timeout : 网关超时,未能及时从上游服务器（URI标识出的服务器，例如HTTP、FTP、LDAP）或者辅助服务器（例如DNS）收到响应",
            "505"=>"HTTP Version Not Supported : 不支持HTTP版本",
            "506"=>"Variant Also Negotiates : Variant也谈判",
            "507"=>"Insufficient Storage : 存储空间不足",
            "508"=>"Loop Detected : 检测到环路,死循环",
            "510"=>"Not Extended : 未扩展",
            "511"=>"Network Authentication Required : 需要网络验证",
            "599"=>"网络连接超时错误"
        ];
    }
}

/**
 * Class UndefinedCode
 * @author sjm
 */
class UndefinedCode implements BaseCode
{
    public function initCode(): array
    {
        return  [
            'tip' => '未知的错误码'
        ];
    }
}

/**
 * Class CodeError
 * @author sjm
 */
class CodeError
{
    const UNDEFINED_INDEX  = '-1';

    const CODE_FIXED_INDEX = 'tip';

    /** map of code class
     * @var array
     */
    private $code_map = [
        '1'   => MessageCode::class,
        '2'   => SuccessCode::class,
        '3'   => RedirectCode::class,
        '4'   => ClientCode::class,
        '5'   => ServerCode::class,
        self::UNDEFINED_INDEX  => UndefinedCode::class,
    ];

    /**
     * @var
     */
    protected static $instance;

    /** init
     * @return CodeError
     */
    public static function instance()
    {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $code
     * @return BaseCode|mixed|string
     */
    public static function get($code)
    {
        return self::instance()->main($code);
    }

    /**
     * @param $code
     * @return array
     */
    public function parsing($code)
    {
        $firstCode = substr($code,0,1);
        $classMap = $this->code_map[$firstCode] ?? $this->code_map[self::UNDEFINED_INDEX];
        $class = new $classMap();
        if($class instanceof BaseCode) {
            return [true,$class];
        }else{
            return [false,'class not instanceof '.BaseCode::class];
        }
    }

    /**
     * @param $code
     * @return BaseCode|mixed|string
     */
    public function main($code)
    {
        /** @var BaseCode|string $class */
        list($result,$class) = $this->parsing($code);
        if(!$result) {
            return $class;
        }
        $initCode = $class->initCode();
        if(isset($initCode[$code])) {
            return $initCode[$code];
        }else{
            return isset($initCode[self::CODE_FIXED_INDEX]) ? $initCode[self::CODE_FIXED_INDEX] : '暂无结果';
        }
    }
}
if(PHP_SAPI === 'cli' && $code = ($argv[1] ?? '')) {
    echo CodeError::get($code),"\n";exit();
}else{
    if(isset($_GET['error_code'])) {
    echo CodeError::get($_GET['error_code']);exit();
    }
}
