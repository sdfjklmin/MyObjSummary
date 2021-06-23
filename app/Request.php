<?php

namespace App;

/**
 * Class Request
 * @package App
 */
class Request
{
    /**
     * @var array
     */
    protected $server;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Request constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->server = $_SERVER;
    }

    public function getUri(): string
    {
        if (isset($this->server['REQUEST_URI'])) {
            return $this->server['REQUEST_URI'];
        }
        return '/';
    }
}