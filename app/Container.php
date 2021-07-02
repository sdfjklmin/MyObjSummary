<?php

namespace App;

class Container
{
    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    protected $servers = [];

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * register service
     */
    public function register()
    {

    }

    /**
     * bind bindings
     */
    public function bind()
    {

    }

    /** instances
     * @param $abstract
     * @param $instance
     * @return mixed
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /** get instance
     * @param $abstract
     * @return mixed
     */
    public function getInstance($abstract)
    {
        return $this->instances[$abstract];
    }
}