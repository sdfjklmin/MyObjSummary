<?php
$twoLevel = [
    [
        'name'  => 'PHP',
        'icon'  => 'typcn typcn-device-desktop hor-icon',
        'menus' => [
            ['name' => 'install', 'url' => '/php/php_install/md'],
            ['name' => 'phpstorm', 'url' => '/php/PhpStorm/md'],
            ['name' => 'fpm-conf', 'url' => '/php/php-fpm/conf'],
            ['name' => 'php the right way', 'url' => '/php/theWay/php'],
            ['name' => 'composer', 'url' => '/php/composer/composer/md'],
            ['name' => 'design mode - singleton', 'url' => '/php/designMode/mode/Singleton/php'],
            ['name' => 'design mode - strategy', 'url' => '/php/designMode/mode/Strategy/php'],
        ]
    ],
    [
        'name'  => 'MySql',
        'icon'  => 'mdi mdi-airplay',
        'menus' => [
            ['name' => 'Install', 'url' => ''],
            ['name' => 'phpStorm', 'url' => ''],
        ]
    ],
];
$moreLevel = [
    [
        'name' => 'Other',
        'icon' => 'typcn typcn-spanner-outline',
        'menus' => [
            [
                ['name' => '框架设计', 'url' => '/designFrame/frame/md'],
                ['name' => 'A2', 'url' => '/A2'],
            ],
            [
                ['name' => 'B1', 'url' => '/B1'],
                ['name' => 'B2', 'url' => '/B2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
            [
                ['name' => 'C1', 'url' => '/C1'],
                ['name' => 'C2', 'url' => '/C2'],
            ],
        ]
    ]
];
return ['two_level' => $twoLevel, 'more_level' => $moreLevel];