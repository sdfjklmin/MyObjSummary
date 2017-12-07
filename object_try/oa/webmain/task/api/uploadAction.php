<?php 
set_time_limit(0);
header('Access-Control-Allow-Origin: *');
class uploadClassAction extends apiAction
{
	public function upfileAction()
	{
		if(!$_FILES)exit('sorry!');
		$upimg	= c('upfile');
		$maxsize= (int)$this->get('maxsize', 50);
		$uptypes= '|jpg|png|gif|jpeg|bmp|docx|doc|zip|rar|xls|xlsx|ppt|pptx|pdf|mp3|';
		$uptypes= '*';
		$upimg->initupfile($uptypes, 'upload|'.date('Y-m').'', $maxsize);
		$upses	= $upimg->up('file');
		$arr 	= c('down')->uploadback($upses);
		$this->returnjson($arr);
	}
	
	public function upcontAction()
	{
		$cont = $this->post('content');
		if(isempt($cont))exit('sorry not cont');
		$cont 	= str_replace(' ','', $cont);
		$cont	= $this->rock->jm->base64decode($cont);
		$arr 	= c('down')->createimage($cont,'png','截图');
		$this->returnjson($arr);
	}
	
	
	public function getfileAjax()
	{
		$cont = '';
		$path = 'upload/uptxt'.$this->adminid.'.txt';
		if(file_exists($path)){
			@$cont = file_get_contents($path);
		}
		$data = array();
		if($cont!=''){
			$arr = json_decode($cont, true);
			$msg 	= $arr['msg'];
			$data 	= $arr['data'];
			@unlink($path);
		}else{
			$msg = 'sorry,not infor!';
		}
		$this->showreturn($data, $msg);
	}
	
	public function getfileAction()
	{
		$fileid = (int)$this->post('fileid',0);
		$rs 	= m('file')->getone($fileid);
		$this->showreturn($rs);
	}
}