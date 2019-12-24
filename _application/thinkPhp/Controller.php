<?php


namespace app\thinkPhp;


class Controller
{
    public function display($data = [])
    {
        if($data) {
            extract($data);
        }
        /*$content = file_get_contents('/home/wwwroot/php-map/_application/tt.html');
        echo $content;*/
        require APP_CURRENT_ROOT.'/index.html';
        return true;
    }
}