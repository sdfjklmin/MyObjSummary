<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019/1/25
 * Time: 16:15
 */

namespace bookLog\buildApis\example;


abstract class CommonApi extends Code
{
    /** 状态码
     * @var
     */
    public $code = 200;

    /** 响应方式
     * @var
     */
    public $respondWay = 'json' ;

    /** 提示信息
     * @var
     */
    public $message = '操作成功!'  ;

    /** 错误集合
     * @var
     */
    public $error = [] ;

    /**
     * CommonApi constructor.
     */
    public function __construct()
    {
        parent::__construct($this);
    }

    /** 手动改变状态码
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code ;
        return $this ;

    }

    /** 手动改变消息
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message ;
        return $this ;
    }

    /** 手动设置响应数据格式
     * @param $way
     * @return $this
     */
    public function setRespondWay($way)
    {
        $this->respondWay = $way ;
        return $this ;
    }

    /** 设置错误信息
     * @param $code
     * @param $error
     * @return $this
     */
    public function setError($code,$error)
    {
        $this->error[] = [
            'code'  => $code,
            'error' => $error ,
        ];
        return $this ;
    }

    /** 数据响应
     * @param $data
     * @return string
     */
    public function respondData($data)
    {
        //多错误提示
        if($this->error){
           $firstError =  current($this->error);
           $this->code = $firstError['code'];
           $this->message = $firstError['error'];
        }
        //数据
        $dat = [
            'code' => $this->code ,
            'data' => $data ,
            'msg'  => $this->message ,
            'errors' => $this->error
        ] ;
        //响应格式
        switch ($this->respondWay)
        {
            case 'json' :
                return json_encode($dat,true);
                break;
            default :
                echo '错误的响应格式!';
                die ;
        }
    }
}