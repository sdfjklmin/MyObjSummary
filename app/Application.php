<?php

namespace App;

/**
 * Class Application
 * @package App
 */
class Application
{

    /**
     * The base path for the application.
     *
     * @var string
     */
    protected $basePath;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FileStorage
     */
    protected $file;

    /**
     * Application constructor.
     * @param null $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;

        $this->registerRequest();

        $this->registerConfig();

        $this->registerFile();
    }

    /**
     * Register Request
     */
    protected function registerRequest()
    {
        $this->request = new Request($this);
    }

    /**
     * Register Config
     */
    protected function registerConfig()
    {
        $this->config = new Config($this);
    }

    /**
     * Register File
     */
    protected function registerFile()
    {
        $this->file = new FileStorage($this);
    }

    /**
     * @return string|null
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return FileStorage
     */
    public function getFile(): FileStorage
    {
        return $this->file;
    }
}