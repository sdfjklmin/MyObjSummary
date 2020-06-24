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
							'title' => 'composer',
							'url'   => '/php/composer/composer',
						],
						[
							'title' => 'hyperf',
							'url'   => '/php/frame/hf',
						],
						[
							'title' => '安装',
							'url'   => '/php/php_install',
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