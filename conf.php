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
							'title' => 'composer',
							'url'   => '/php/composer/composer',
						],
						[
							'title' => 'hyperf',
							'url'   => '/php/frame/hf',
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
					'title' => 'shell',
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
				'title' => 'composer',
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
				'title' => 'docker',
				'url'   => '/git/git_command',
				'menu'  => [
				]
			],
		]
]
];