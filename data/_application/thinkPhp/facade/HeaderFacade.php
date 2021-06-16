<?php


namespace app\thinkPhp\facade;

use app\thinkPhp\Header;

/**
 * Class HeaderFacade
 * @author sjm
 * @method static get($name='', $value='')
 */
class HeaderFacade extends Facade
{
    protected static function getFacadeClass(): string
    {
        return Header::class;
    }
}