<?php
class wordClassModel extends Model
{
	public function getfolderid($uid)
	{
		$num = "folder".$uid."";
		$id  = m('option')->getnumtoid($num, ''.$this->adminname.'文件夹目录', false);
		return $id;
	}
	
	public function getfoldrows($uid)
	{
		$pid 	= $this->getfolderid($uid);
		$rows 	= m('option')->gettreedata($pid);
		return $rows;
	}

}