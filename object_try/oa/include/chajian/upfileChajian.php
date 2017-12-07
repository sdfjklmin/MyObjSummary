<?php
/**
	上传文件类upfileChajian
	create：chenxihu
	createdt：2013-11-15
	explain：上传文件类，可上传任何文件类型
*/

class upfileChajian extends Chajian{
	
	public $ext;     //上传类型
	public $maxsize; //上传大小(MB)
	public $path;    //文件夹
	
	private $jpgallext		= '|jpg|png|gif|bmp|jpeg|';	//图片格式
	
	/**
		初始化
		@param	$ext string 上传类型
		@param	$path string 上传目录 如：upload|e|ee
		@param	$maxsize ing 上传大小(MB)
	*/
	public function initupfile($ext,$path,$maxsize=1)
	{
		if($ext=='image')$ext = $this->jpgallext;
		$this->ext		= $ext;
		$this->maxsize	= $maxsize;
		$this->path		= $path;
	}
	
	/**
		上传
		@param	$name	string	对应文本框名称
		@param	$cfile	string	文件名心的文件名，不带扩展名的
		@return	string/array
	*/
	public function up($name,$cfile='')
	{
		if(!$_FILES)return 'sorry!';
		$file_name		= $_FILES[$name]['name'];
		$file_size		= $_FILES[$name]['size'];//字节
		$file_type		= $_FILES[$name]['type'];
		$file_error		= $_FILES[$name]['error'];
		$file_tmp_name	= $_FILES[$name]['tmp_name'];
		$file_sizecn	= $this->formatsize($file_size);
		$file_ext		= strtolower(substr($file_name,strrpos($file_name,'.')+1));	//文件扩展名
		
		$file_img		= false;
		$file_kup		= false;
		$jpgallext		= $this->jpgallext;
		$upallfile		= $jpgallext.'doc|docx|xls|xlsx|ppt|pptx|pdf|swf|rar|zip|txt|gz|wav|mp3|wma|chm|apk|';
		
		if($this->contain($jpgallext, '|'.$file_ext.'|'))$file_img = true;	
		if($this->contain($upallfile, '|'.$file_ext.'|'))$file_kup = true;	
		
		if($file_size<=0){
			return '文件大小0字节，不能上传';
		}
		
		if($file_error>0){
			$rrs = $this->geterrmsg($file_error);
			return $rrs;
		}
			
		if(!$this->contain('|'.$this->ext.'|', '|'.$file_ext.'|') && $this->ext != '*'){
			return '禁止上传文件类型['.$file_ext.']';
		}
		
		if($file_size>$this->maxsize*1024*1024){
			return '上传文件过大，限制在：'.$this->formatsize($this->maxsize*1024*1024).'内，当前文件大小是：'.$file_sizecn.'';
		}
		
		//创建目录
		$zpath=explode('|',$this->path);
		$mkdir='';
		for($i=0;$i<count($zpath);$i++){
			$mkdir.=''.$zpath[$i].'/';
			if(!is_dir($mkdir))mkdir($mkdir);
		}
		
		//新的文件名
		$file_newname	=$file_name;
		if(!$cfile==''){
			$file_newname=''.$cfile.'.'.$file_ext.'';
		}else{
			$randname	= ''.date('d_His').''.rand(10,99).'';
			$file_newname=''.$randname.'.'.$file_ext.'';
		}
		
		$save_path	= ''.str_replace('|','/',$this->path);
		$allfilename= $save_path.'/'.$file_newname.'';
		$uptempname	= $save_path.'/'.$randname.'.uptemp';

		$upbool	 	= true;
		if(!$file_kup){
			$fp	= fopen($file_tmp_name,'r');
			$filebase64	= base64_encode(fread($fp,$file_size));
			fclose($fp);
			
			$fh 	= fopen($uptempname, 'a');
			$upbool = fwrite($fh, $filebase64);
			fclose($fh);
			$allfilename	= $uptempname;
			unlink($file_tmp_name);
		}else{
			$upbool	= move_uploaded_file($file_tmp_name,$allfilename);
		}
		
		if($upbool){
			$picw=0;$pich=0;
			if($file_img){
				list($picw,$pich)	= getimagesize($allfilename);
				if($picw==0||$pich==0){
					unlink($allfilename);
					return 'error:非法图片文件';
				}
			}
			return array(
				'newfilename' => $file_newname,
				'oldfilename' => $file_name,
				'filesize'    => $file_size,
				'filesizecn'  => $file_sizecn,
				'filetype'    => $file_type,
				'filepath'    => $save_path,
				'fileext'     => $file_ext,
				'allfilename' => $allfilename,
				'picw'        => $picw,
				'pich'        => $pich
			);
		}else{
			return '上传失败：'.$this->geterrmsg($file_error).'';
		}
	}
	
	private function geterrmsg($code)
	{
		$arrs[1] = '上传文件大小超过服务器允许上传的最大值';
		$arrs[2] = '上传文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值';
		$arrs[6] = '没有找不到临时文件夹';
		$arrs[7] = '文件写入失败';
		$arrs[8] = 'php文件上传扩展没有打开';
		$arrs[3] = '文件只有部分被上传';
		$rrs 	 = '上传失败，可能是服务器内部出错，请重试';
		if(isset($arrs[$code]))$rrs=$arrs[$code];
		return $rrs;
	}
	
	//返回文件大小
	public function formatsize($size)
	{
		$arr = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
		$e = floor(log($size)/log(1024));
		return number_format(($size/pow(1024,floor($e))),2,'.','').' '.$arr[$e];
	}
}
