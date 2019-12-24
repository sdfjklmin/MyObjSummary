<?php


namespace app\thinkPhp;


class Request
{
    protected $post_data;

    protected $get_data;

    public function __construct()
    {
        $this->post_data = $_POST;
        $this->get_data  = $_GET;
    }

    public function get($name = '', $value = '')
    {
        if(empty($name)) {
            return $this->get_data;
        }
        if(isset($this->get_data[$name])) {
            $value = $this->get_data[$name];
        }
        return $value;
    }

    public function post($name = '', $value = '')
    {
        if(empty($name)) {
            return $this->post_data;
        }
        if(isset($this->post_data[$name])) {
            $value = $this->post_data[$name];
        }
        return $value;
    }
}