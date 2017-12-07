<?php 
class uploadClassAction extends Action{
	
	/**
	*	上传文件页面
	*/
	public function defaultAction()
	{
		$params['callback'] 	= $this->get('callback');
		$params['maxup'] 		= $this->get('maxup','0');
		$params['thumbnail'] 	= $this->get('thumbnail');
		$params['maxwidth'] 	= $this->get('maxwidth','0');
		$params['showid'] 		= $this->get('showid');
		$params['upkey'] 		= $this->get('upkey');
		$params['uptype'] 		= $this->get('uptype','*');
		$params['thumbtype'] 	= $this->get('thumbtype','0');
		$params['maxsize'] 		= (int)$this->get('maxsize', ini_get('upload_max_filesize'));
		$this->title 			= $this->get('title','文件上传');
		$this->assign('params', $params);
		$this->assign('callback', $params['callback']);
	}
	
	public function upfileAjax()
	{
		if(!$_FILES)exit('sorry!');
		$upimg	= c('upfile');
		$maxsize= (int)$this->get('maxsize', 5);
		$uptype	= $this->get('uptype', '*');
		$thumbnail	= $this->get('thumbnail');
		$upimg->initupfile($uptype, 'upload|'.date('Y-m').'', $maxsize);
		$upses	= $upimg->up('file');
		$arr 	= c('down')->uploadback($upses, $thumbnail);
		$this->returnjson($arr);
	}
	
	
	
	public function getfileAjax()
	{
		$mtype		= $this->request('mtype');
		$mid		= $this->request('mid');
		$rows 		= m('file')->getfiles($mtype, $mid);
		echo json_encode($rows);
	}
	
	public function delfileAjax()
	{
		$id		= $this->request('id');
		m('file')->delfile($id);
	}
	
	public function showAction()
	{
		$id		= (int)$this->get('id','0');
		$this->display = false;
		m('file')->show($id);
	}
	
	/**
	*	编辑器上传文件
	*/
	public function upimgAction()
	{
		$this->display = false;
		$upfile = c('upfile');
		$upfile->initupfile('jpg|png|gif|jpeg','upload|'.date('Y-m').'', 5);
		$upses	= $upfile->up('imgFile');
		if(is_array($upses)){
			$url = $upses['allfilename'];
			$url = str_replace('../' , '', $url);
			$arr = array('error' => 0, 'url' => $url);
		}else{
			$arr = array('error' => 1, 'message' => $upses);
		}
		$this->returnjson($arr);
	}
}