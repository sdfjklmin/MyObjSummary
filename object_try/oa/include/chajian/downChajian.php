<?php
/**
	下载文件类插件
*/

class downChajian extends Chajian{
	
	public function createimage($cont, $ext, $filename, $thumbnail='')
	{
		if(isempt($cont))return false;
		$mkdir 	= 'upload/'.date('Y-m').'';
		if(!is_dir($mkdir))mkdir($mkdir);
		
		$allfilename			= ''.$mkdir.'/'.date('d_His').''.rand(10,99).'.'.$ext.'';
		$upses['oldfilename'] 	= $filename.'.'.$ext;
		$upses['fileext'] 	  	= $ext;
		@file_put_contents($allfilename, $cont);
		if(!file_exists($allfilename))return false;
		
		$fileobj				= getimagesize($allfilename);
		$mime					= strtolower($fileobj['mime']);
		$next 					= 'jpg';
		if(contain($mime,'bmp'))$next = 'bmp';
		if($mime=='image/gif')$next = 'gif';
		if($mime=='image/png')$next = 'png';
		if($ext != $next){
			@unlink($allfilename);
			$ext = $next;
			$allfilename			= ''.$mkdir.'/'.date('d_His').''.rand(10,99).'.'.$ext.'';
			$upses['oldfilename'] 	= $filename.'.'.$ext;
			$upses['fileext'] 	  	= $ext;
			@file_put_contents($allfilename, $cont);
			if(!file_exists($allfilename))return false;	
		}
		
		$filesize 			  	= filesize($allfilename);
		$filesizecn 		  	= c('upfile')->formatsize($filesize);
		$picw					= $fileobj[0];				
		$pich					= $fileobj[1];
		if($picw==0||$pich==0){
			@unlink($allfilename);
			return false;
		}
		$upses['filesize']	 	= $filesize;
		$upses['filesizecn']	= $filesizecn;
		$upses['allfilename']	= $allfilename;
		$upses['picw']	 		= $picw;
		$upses['pich']	 		= $pich;
		$arr 					= $this->uploadback($upses, $thumbnail);
		return $arr;
	}
	
	public function uploadback($upses, $thumbnail='')
	{
		if($thumbnail=='')$thumbnail='150x150';
		$msg 		= '';
		$data 		= array();
		if(is_array($upses)){
			$arrs	= array(
				'adddt'	=> $this->rock->now,
				'valid'	=> 1,
				'filename'	=> $upses['oldfilename'],
				'web'		=> $this->rock->web,
				'ip'		=> $this->rock->ip,
				'fileext'	=> $upses['fileext'],
				'filesize'	=> $upses['filesize'],
				'filesizecn'=> $upses['filesizecn'],
				'filepath'	=> str_replace('../','',$upses['allfilename']),
				'optid'		=> $this->adminid,
				'optname'	=> $this->adminname
			);
			$thumbpath	= $arrs['filepath'];
			$sttua		= explode('x', $thumbnail);
			$lw 		= (int)$sttua[0];
			$lh 		= (int)$sttua[1];
			if($upses['picw']>$lw || $upses['pich']>$lh){
				$imgaa	= c('image', true);
				$imgaa->createimg($thumbpath);
				$thumbpath 	= $imgaa->thumbnail($lw, $lh, 1);
			}
			$arrs['thumbpath'] = $thumbpath;
			
			
			$this->db->record('[Q]file',$arrs);
			$id	= $this->db->insert_id();
			$arrs['id'] = $id;
			$arrs['picw'] = $upses['picw'];
			$arrs['pich'] = $upses['pich'];
			$data= $arrs;
		}else{
			$msg = $upses;
		}
		return $data;
	}
}
