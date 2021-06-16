<?php
namespace app\controller;

use app\thinkPhp\Controller;

class TpIndex extends Controller
{
    public function getTree()
    {
        $dirs  = scandir(APP_INIT_ROOT.'/') ;
        $reallyDir = [];
        foreach ($dirs as $dir) {
            $firstStr = substr($dir,0,1);
            if(in_array($firstStr,['.','_'])) {
                continue;
            }
            if(!is_dir(APP_INIT_ROOT.'/'.$dir)) {
                continue;
            }
            $reallyDir[] = $dir;
        }
        return $reallyDir;
    }


    public function getTrees($dirs, $path, $parentDir = '',$pCode = '')
    {
        $label = [];
        foreach ($dirs as $dir) {
            $firstStr = substr($dir,0,1);
            if(in_array($firstStr,['.'])) {
                continue;
            }
            if(is_dir($path.$dir)) {
                $tempDir = scandir($path.$dir.'/');
                $code = $dir.rand();
                $label[] = [
                    'name' => $dir ,
                    'code' => $code ,
                    'icon' => 'icon-th' ,
                    'parentCode' => $pCode  ,
                    'href' => '',
                    'child' => $this->getTrees($tempDir,$path.$dir.'/',$parentDir.'/'.$dir, $code)  ,
                ] ;
            }else{
                $pathInfo = pathinfo($dir);
                $label[] = [
                    'name'=>$dir ,
                    'icon'=>'icon-minus-sign' ,
                    'code'=>$dir ,
                    'parentCode'=>$pCode ,
                    'href' => $parentDir.'/'.$pathInfo['filename'].'?suffix='.($pathInfo['extension'] ?? ''),
                    'child'=>[] ,
                ]  ;
            }
        }
        return $label;
    }

    public function index()
    {
        $tt = $this->getTrees($this->getTree(),APP_INIT_ROOT.'/');
        $link = json_encode($tt);
        return $this->display([
            'title' => '目录结构',
            'link' => $link,
        ]);
    }

	/**
	 * @path /_index/test
	 */
	public function test()
	{
		echo 11;exit();
    }
}