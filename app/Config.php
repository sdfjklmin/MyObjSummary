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
     * @var array
     */
    protected $configs = [];

    /**
     * Config constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app         = $app;

        $this->config_path = $this->app->getBasePath() . DIRECTORY_SEPARATOR . 'config';

        $this->setConfig();
    }

    /**
     * set config
     */
    protected function setConfig()
    {
        $dirs  = scandir($this->config_path) ;
        $config = [];
        foreach ($dirs as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $pathInfo = pathinfo($file);
            $filename = $pathInfo['filename'];
            $config[$filename] = require $this->config_path.DIRECTORY_SEPARATOR.$file;
        }
        $this->configs = $config;
    }

    /**
     * @param string $key
     * @return array|mixed
     */
    public function getConfig($key = ''): array
    {
        if ($key) {
            return $this->configs[$key] ?? [];
        }

        return $this->configs;
    }
}