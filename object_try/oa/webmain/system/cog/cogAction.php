<?php
class cogClassAction extends Action
{
	public function sysinfoAjax()
	{
		$fields = array(
			'title'	=> '系统名称',
			'url'	=> '系统URL地址',
			'localurl'	=> '系统本地地址',
			'taskurl'	=> '任务地址',
			'db_drive'	=> '数据驱动',
			'version'	=> '版本',
			'phpos'		=> '服务器',
			'phpver'	=> 'PHP版本',
			'mysqlver'	=> 'mysql版本',
			'SERVER_SOFTWARE'	=> 'web服务器',
			'upload_max_filesize'	=> '最大上传大小',
			'post_max_size'	=> 'POST最大',
			'memory_limit'	=> '使用最大内存',
			
		);
		
		$data = array(
			'title'	=> getconfig('title'),
			'url'	=> getconfig('url'),
			'localurl'	=> getconfig('localurl'),
			'taskurl'	=> getconfig('taskurl'),
			'db_drive'	=> getconfig('db_drive'),
			'version'	=> '信呼V'.VERSION.'',
			'phpos'	=> PHP_OS,
			'phpver'	=> PHP_VERSION,
			'mysqlver'	=> $this->db->getsyscount('version'),
			'SERVER_SOFTWARE'	=> $_SERVER['SERVER_SOFTWARE'],
			'upload_max_filesize'	=> ini_get('upload_max_filesize'),
			'post_max_size'			=> ini_get('post_max_size'),
			'memory_limit'			=> ini_get('memory_limit'),
			
		);
		
		
		
		$this->returnjson(array(
			'fields' => $fields,
			'data' 	=> $data,
		));
	}
}