<?php
//系统配置文件		
return array(
	'url'		=> 'http://ue.com/',		//系统URL
	'localurl'	=> '',			//本地系统URL，用于服务器上浏览地址
	'title'		=> '信呼协同办公系统',	//系统默认标题
	'apptitle'	=> '',			//APP上或PC客户端上的标题
	'weblogo'	=> '',			//PC客户端上的logo图片
	'db_host'	=> 'localhost',		//数据库地址
	'db_user'	=> 'root',		//用户名
	'db_pass'	=> '',		//密码
	'db_base'	=> 'rockxinhu',		//数据库名称
	'perfix'	=> 'xinhu_',	//表名前缀
	'qom'		=> 'xinhu_',		//session、cookie前缀
	'highpass'	=> '',			//超级管理员密码，可用于登录任何帐号
	'db_drive'	=> 'mysql',	//操作数据库驱动
	'randkey'	=> 'rhmypdqglcftusiazwkjoxvneb',		//这是个随机字符串
	'asynkey'	=> '5b48dc7cb1d5fb0e1f19e74d4555ff6a',	//这是异步任务key
	'install'	=> true			//已安装，不要去掉啊
);