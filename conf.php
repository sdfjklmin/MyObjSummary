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
							'title' => 'composer',
							'url'   => '/php/composer/composer',
						],
						[
							'title' => 'fpm',
							'url'   => '/php/php-fpm?ext=conf',
						],
						[
							'title' => 'phpStorm',
							'url'   => '/php/PhpStorm',
						],
						[
							'title' => 'consul',
							'url'   => '/php/consul',
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
			]
	],
	[
	'intro' => '',
	'menu'  =>
		[
			[
				'title' => 'Composer',
				'url'   => '#',
				'menu'  => [
					[
						'title' => '介绍',
						'url'   => 'a',
					],
					[
						'title' => '使用',
						'url'   => 'b',
					],
				]
			],
			[
				'title' => 'Docker',
				'url'   => '/git/git_command',
				'menu'  => [
				]
			],
		]
	],
	[
		'intro' => '',
		'menu'  => [
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
						'title' => '服务器',
						'url'   => '/linux/server',
					],
					[
						'title' => 'Supervisor',
						'url'   => '/linux/supervisor',
					],
					[
						'title' => 'Samba',
						'url'   => '/samba/index',
					],
				]
			]
		]
	],
	[
		'intro' => '',
		'menu'  => [
			[
				'title' => 'MySQL',
				'url'   => '#',
				'menu'  => [
					[
						'title' => '常见问题',
						'url'   => '/mysql/problem',
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
			]
		]
	],
	[
		'intro' => '',
		'menu'  => [
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
			]
		]
	],
	[
		'intro' => '',
		'menu'  => [
			[
				'title' => '杂项',
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
						'title' => '谷歌扩展',
						'url'   => '#',
					],
				]
			]
		]
	]
];