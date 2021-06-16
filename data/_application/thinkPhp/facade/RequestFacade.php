<?php


namespace app\thinkPhp\facade;


use app\thinkPhp\Request;

/**
 * Class RequestFacade
 * @author sjm
 * @package app\thinkPhp\facade
 * @method get($name='', $value ='') static
 * @method post($name='', $value ='') static
 */
class RequestFacade extends Facade
{
    protected static function getFacadeClass(): string
    {
        return Request::class;
    }
}