<?php
//配置菜单
/*[
	//父类
	[
		'intro' => '这里是顶级父类名称|可省',
		'menu'  => [
			//一级
			[
				'title' => 'PHP',
				'url'   => '#|一级地址',
				'menu'  => [
					//二级
					[
						'title' => '名称',
						'url'   => '地址?ext=扩展后缀名称|{ text/index?ext=php}|默认后缀 md',
					],
				]
			]
		]
	]
];*/
return [
	[
		'intro' => '',
		'menu'  =>
			[
				[
					'title' => 'PHP',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '安装',
							'url'   => '/php/php_install',
						],
						[
							'title' => '常规总结',
							'url'   => '/php/knowledge?ext=php',
						],
						[
							'title' => 'Composer',
							'url'   => '/php/composer/composer',
						],
						[
							'title' => 'FPM',
							'url'   => '/php/php-fpm?ext=conf',
						],
						[
							'title' => 'PhpStorm',
							'url'   => '/php/PhpStorm',
						],
						[
							'title' => 'Consul',
							'url'   => '/php/consul',
						],
						[
							'title' => 'DI/IOC',
							'url'   => '/php/di_and_ioc/README',
						],
					]
				],
				[
					'title' => 'Swoole',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '介绍',
							'url'   => '/swoole/README',
						],
						[
							'title' => 'wiki',
							'url'   => '/swoole/wiki/README',
						],
						[
							'title' => 'Client',
							'url'   => '/swoole/wiki/Client?ext=php',
						],

					]
				],
				[
					'title' => 'Shell',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '介绍',
							'url'   => '/shell/index',
						],
					]
				],
				[
					'title' => 'Linux',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '常用命令',
							'url'   => '/linux/commands',
						],
						[
							'title' => 'Crontab',
							'url'   => '/linux/crontab',
						],
						[
							'title' => 'FTP',
							'url'   => '/linux/ftp',
						],
						[
							'title' => 'Supervisor',
							'url'   => '/linux/supervisor',
						],
						[
							'title' => 'Samba',
							'url'   => '/linux/samba',
						],
						[
							'title' => 'Server',
							'url'   => '/linux/server',
						],
						[
							'title' => 'Vi',
							'url'   => '/linux/vi',
						],
						[
							'title' => '压测',
							'url'   => '/linux/pressure_test',
						],
					]
				],
				[
					'title' => 'MySQL',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '基础',
							'url'   => '/mysql/mysql',
						],
						[
							'title' => '常见问题',
							'url'   => '/mysql/problem',
						],
						[
							'title' => '数据类型',
							'url'   => '/mysql/data_type',
						],
						[
							'title' => 'SQL分析',
							'url'   => '/mysql/sqlAnalysis',
						],
						[
							'title' => '规则',
							'url'   => '/mysql/rule',
						],
						[
							'title' => '索引使用',
							'url'   => '/mysql/sql',
						],
						[
							'title' => '使用',
							'url'   => '/mysql/sqlUse',
						],
						[
							'title' => '事物',
							'url'   => '/mysql/transaction',
						],
						[
							'title' => '树',
							'url'   => '/mysql/tree',
						],
						[
							'title' => '锁',
							'url'   => '/mysql/lock',
						],
						[
							'title' => 'json',
							'url'   => '/mysql/json',
						],
						[
							'title' => '5.5.conf',
							'url'   => '/mysql/temp?ext=conf',
						]
					]
				],
				[
					'title' => 'NoSQL',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '介绍',
							'url'   => '/noSql/README',
						],
						[
							'title' => 'Redis',
							'url'   => '/noSql/redis/redis',
						],
						[
							'title' => 'Mongodb',
							'url'   => '/noSql/mongodb/index',
						],
					]
				],
				[
					'title' => 'Nginx',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '安装',
							'url'   => '/nginx/install',
						],
						[
							'title' => '基础配置',
							'url'   => '/nginx/nginx?ext=conf',
						],
					]
				],
				[
					'title' => 'Node',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '介绍',
							'url'   => '/node/README',
						]
					]
				],
				/*[
					'title' => 'Code',
					'url'   => '#',
					'menu'  => [
						[
							'title' => '介绍',
							'url'   => '/node/README',
						]
					]
				],*/
				[
					'title' => 'Other',
					'url'   => '#',
					'menu'  => [
						[
							'title' => 'GIT',
							'url'   => '/git/git_command',
						],
						[
							'title' => '基础知识',
							'url'   => '/learn/learnInfo',
						],
						[
							'title' => 'markdown',
							'url'   => '/md/index',
						],
						[
							'title' => 'Zh-cn',
							'url'   => '/other/index',
						],
						[
							'title' => '思维模式',
							'url'   => '/other/thinkModel',
						],
						[
							'title' => '系统架构',
							'url'   => '/designFrame/frame',
						],
						[
							'title' => 'English Word',
							'url'   => '/try/tt?ext=html',
						],
						/*[
							'title' => '知识英语',
							'url'   => '/frame/README',
						],
						[
							'title' => '鸡汤灌溉',
							'url'   => '/frame/README',
						],*/
					]
				],
			]
	]
];