<?php
//配置菜单
// php - composer - intro
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
							'title' => 'install',
							'url'   => '/php/php_install',
						],
						[
							'title' => 'knowledge',
							'url'   => '/php/knowledge?ext=php',
						],
						[
							'title' => 'magicFunc',
							'url'   => '/php/magicFunc?ext=php',
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
]
];