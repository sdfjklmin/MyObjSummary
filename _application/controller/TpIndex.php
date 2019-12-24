<?php
namespace app\controller;

use app\thinkPhp\Controller;

class TpIndex extends Controller
{
    public function index()
    {
        //目录数据缓存
        $link = getDirTree(APP_INIT_ROOT.'/');
        $link = json_encode($link);
        return $this->display([
            'title' => '目录结构',
            'link' => $link,
        ]);
    }
}