<?php


namespace app\thinkPhp;

/**
 * Class Header
 * @author sjm
 * @package app\thinkPhp
 */
class Header
{
    /**
     * @var $_SERVER
     */
    protected $server;

    public function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * @param $name
     * @param string $value
     * @return mixed|string
     */
    public function get($name = '', $value = '')
    {
        $header = [];
        if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
            //apache function
            $header = $result;
        } elseif (function_exists('getallheaders') && $result = getallheaders()) {
            //apache function based on apache_request_headers
            $header = $result;
        } else {
            $server = $_SERVER;
            foreach ($server as $key => $val) {
                if (0 === strpos($key, 'HTTP_')) {
                    $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                    $header[$key] = $val;
                }
            }
            if (isset($server['CONTENT_TYPE'])) {
                $header['content-type'] = $server['CONTENT_TYPE'];
            }
            if (isset($server['CONTENT_LENGTH'])) {
                $header['content-length'] = $server['CONTENT_LENGTH'];
            }
        }
        //array_change_key_case 将数组的 key 全部变为大写或小写
        $header = array_change_key_case($header);
        if(empty($name)) {
            return $header;
        }
        $name = str_replace('_','-',$name);
        return isset($header[$name]) ? $header[$name] : $value;
    }
}