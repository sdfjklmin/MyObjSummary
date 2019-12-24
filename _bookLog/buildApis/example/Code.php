<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019/1/25
 * Time: 16:13
 */

namespace bookLog\buildApis\example;


abstract class Code
{
    /**
     * @remark 常见错误码
     */
    const ERROR_ARGS = 400 ;
    const ERROR_FORBIDDEN = 403 ;
    const ERROR_NOT_FIND = 404 ;
    const ERROR_SERVER = 500 ;

    /**
     * @var CommonApi
     */
    public  $codeBefore ;

    /**
     * Code constructor.
     * @param $codeBefore
     */
    public function __construct($codeBefore)
    {
        $this->codeBefore = $codeBefore ;
    }

    /** 403
     * @param string $msg
     * @return string
     */
    public function errorForbidden($msg = "Forbidden !")
    {
        $this->codeBefore->code    = self::ERROR_FORBIDDEN ;
        $this->codeBefore->message = $msg ;
        return $this->codeBefore->respondData([]);
    }

    /** 404
     * @param string $msg
     * @return string
     */
    public function errorNotFind($msg = 'Not Find !')
    {
        $this->codeBefore->code    = self::ERROR_NOT_FIND ;
        $this->codeBefore->message = $msg ;
        return $this->codeBefore->respondData([]);
    }

    /** 400
     * @param string $msg
     * @return string
     */
    public function errorWrongArgs($msg = 'Wrong Args !')
    {
        $this->codeBefore->code    = self::ERROR_ARGS ;
        $this->codeBefore->message = $msg ;
        return $this->codeBefore->respondData([]);
    }

    /** 500
     * @param string $msg
     * @return string
     */
    public function errorServer($msg = 'Server Error !')
    {
        $this->codeBefore->code    = self::ERROR_SERVER ;
        $this->codeBefore->message = $msg ;
        return $this->codeBefore->respondData([]);
    }
}