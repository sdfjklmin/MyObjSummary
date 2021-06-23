<?php

namespace App;

class FileStorage
{
    /**
     * @var string[]
     */
    protected $suffix = [
        'md'  => '\n', 'html' => '', 'conf' => '<br />',
        'php' => '<br />'
    ];

    /**
     * @var string[]
     */
    protected $pre_suffix = ['php', 'conf'];

    /**
     * @var string
     */
    protected $base_path;

    /**
     * @var Application
     */
    protected $app;

    /**
     * FileStorage constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->base_path = $app->getBasePath() . DIRECTORY_SEPARATOR . 'data';

        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $uri = $this->app->getRequest()->getUri();
        if ($uri == '/') {
            return '';
        }
        $pathInfo = pathinfo($uri);
        $file     = $this->base_path . $pathInfo['dirname'] . '.' . $pathInfo['filename'];
        if (!isset($this->suffix[$pathInfo['filename']])) {
            return "# 400 => Oops! The File suffix not allowed...";
        }
        if (file_exists($file)) {
            $content = file_get_contents($file);
            //添加反斜杠
            $content = addslashes($content);
            //替换
            $search  = array(
                '
', '../webIndex'
            );
            $replace = array($this->suffix[$pathInfo['filename']], '');
            $content = str_replace($search, $replace, $content);
            return $this->preSuffixCode($content, $pathInfo['filename']);
        } else {
            return "# 400 => Oops! The Page you were looking for doesn't exits...";
        }
    }

    /**
     * @param $content
     * @param $suffix
     * @return string
     */
    protected function preSuffixCode($content, $suffix): string
    {
        if (in_array($suffix, $this->pre_suffix)) {
            $content = "<pre style='font-size: 16px'>{$content}</pre>";
        }
        return $content;
    }
}