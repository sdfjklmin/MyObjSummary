<?php

namespace App;

/**
 * Class Config
 * @package App
 */
class Config
{
    /**
     * @var string
     */
    protected $config_path;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Config constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app         = $app;

        $this->config_path = $this->app->getBasePath() . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = ''): array
    {
        $dirs  = scandir($this->config_path) ;
        $config = [];
        foreach ($dirs as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $base = rtrim($file,'.php');
            $index = str_replace('.','_', $base);
            $config[$index] = require $this->config_path.DIRECTORY_SEPARATOR.$file;
        }
        if($key && isset($config[$key])) {
            return $config[$key];
        }
        return $config;
    }
}