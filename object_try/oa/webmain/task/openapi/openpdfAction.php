<?php
/**
*	office的转pdf接口
*/
class openpdfClassAction extends openapiAction
{
	
	
	public function initAction()
	{
		$this->display= false;
		$openkey = $this->post('openkey');
		if($openkey != md5(getconfig('pdfopenkey')))$this->showreturn('', 'pdfopenkey not access', 201);
	}
	
	//获取需要转化内容
	public function getfileAction()
	{
		$fileid = $this->get('fileid','0');
		$frs 	= m('file')->getone($fileid);
		if(!$frs)$this->showreturn('', 'file db record not found', 202);
		
		$filepath	= $frs['filepath'];
		if(!file_exists($filepath))$this->showreturn('', 'file not found', 203);
		$filesize 	= floatval($frs['filesize']);
		if($filesize >5 * 1024 * 1024)$this->showreturn('', 'filesize to loog', 204);
		$data 	= base64_encode(file_get_contents($filepath));
		$bdata['data'] 		= $data;
		$bdata['filename'] 	= $frs['filename'];
		$bdata['fileext'] 	= $frs['fileext'];
		$this->showreturn($bdata);
	}
	
	//接收已换好的pdf文件
	public function recefileAction()
	{
		$fileid = $this->get('fileid','0');
		$zhzt 	= $this->get('zhzt');
		$data 	= $this->post('data');
		$bo 	= false;
		if($data != ''){
			$dir 	= 'upload/'.date('Y-m').'';
			if(!is_dir($dir))mkdir($dir);
			$path 	= $dir.'/'.$fileid.'.pdf';
			@$bo	= file_put_contents($path, base64_decode($data));
			if($bo){
				m('file')->update(array(
					'pdfpath' => $path
				), $fileid);
			}
		}
		if(!$bo)$this->showreturn('', 'receerror', 205);
		$now = $this->rock->now();
		$this->showreturn(array(
			'now' => $now
		));
	}
}