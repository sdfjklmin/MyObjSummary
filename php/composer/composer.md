#### 网站 
[phpComposer](https://www.phpcomposer.com/) [getComposer](https://getcomposer.org/) [packagist](https://packagist.org/) [kancloud](https://www.kancloud.cn/thinkphp/composer/35668) [learnku](https://learnku.com/docs/composer/2018)

#### 软件包安装
    #安装包下载 php　为系统的全局变量，即安装的php的bin地址，可以手动指定地址
    curl -sS https://getcomposer.org/installer | /usr/local/php/bin/php  
    
    #局部下载
    1.  curl -sS https://getcomposer.org/installer | php    
        或者
    2.  php -r "readfile('https://getcomposer.org/installer');" | php
    
    #下载成功后会获得 composer.phar ，这是 composer 的二进制文件。
    #这是一个 PHAR 包（PHP 的归档），这是 PHP 的归档格式可以帮助用户在命令行中执行一些操作
    
    #设置全局
    3.  sudo  mv composer.phar /usr/local/bin/composer 
    
    #设置权限
    4.chmod +x composer
    
    #error
    ##无法写入-Unable to write keys.dev.pub to: /home/panjm/.composer
    sudo su
    curl -sS https://getcomposer.org/installer | /usr/local/php/bin/php
    
    ##命令无法下载时,使用 wget 下载
    wget https://getcomposer.org/composer.phar
    
    ##环境无法满足安装包要求-Your requirements could not be resolved to an installable set of packages.
    #自我更新,然后重试
    composer selfupdate
    
    #如果还是不行,先设置 阿里源 , 在 selfupdate , 然后重试
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer
    
    #还是不行 ? 重装 composer, 不行 ? 重装系统, 不行 ? 删服务器, 跑路 O(∩_∩)O !
      
#### 命令
```
#初始化默认信息(composer.json的信息)
composer init

#安装composer.json中的依赖文件 如果有composer.lock文件则获取它的安装信息
composer install

#secure-http for details 错误
#局部禁用https
composer config secure-http false

#全局禁用https
composer config -g secure-http false

#依赖升级
composer update

#单独升级某几个包
composer update vendor/package vendor/package2 

#批量升级
php composer update vendor/* 

#申明依赖
composer require

#申明phpunit依赖
composer require phpunit/phpunit:1.*

#搜索monolog
composer search monolog

#规范代码
composer cs-fix

#列出所有可用的软件包
composer show 

#显示详细信息
php composer.phar show monolog/monolog

#depends依赖检测,并列出相关依赖
composer.phar depends  monolog/monolog 

#检测composer.json是否有效
php composer.phar validate

#检测依赖包是否有修改
composer.phar status

#依赖包有修改,列出详细
composer.phar status -v 

#自我更新到最新的版本 
composer.phar self-update

#回退到之前的版本,重复操作会一直回退
composer self-update --rollback

#查看,更改配置
composer config --list

#将 Composer 镜像设置为阿里云镜像，加速国内下载速度
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer

#create-project创建项目
composer.phar create-project doctrine/orm path 2.2.*

#诊断问题,dia.g.nose
composer.phar diagnose

#获取帮助
composer.phar help install

#引入composer安装生成的文件,自动加载
require 'vendor/autoload.php';

#命令说明,默认以配置的php和composer的环境变量进行操作
[php] [composer] [commands]

#特殊指定操作
#php7.4的命令        composer安装地址  需要运行的命令   
/bin/php7.4/bin/php /bin/composer   [commands]
```


#### Question
    composer中vendor入库:
    (https://docs.phpcomposer.com/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.html)
    限制自己安装标记版本（无 dev 版本），这样你只会得到 zip 压缩的安装，并避免 git“子模块”出现的问题。
    使用 --prefer-dist 或在 config 选项中设置 preferred-install 为 dist。
    在每一个依赖安装后删除其下的 .git 文件夹，然后你就可以添加它们到你的 git repo 中。
    你可以运行 rm -rf vendor/**/.git 命令快捷的操作，但这意味着你在运行 composer update 命令前需要先删除磁盘中的依赖文件。
    新增一个 .gitignore 规则（vendor/.git）来忽略 vendor 下所有 .git 目录。这种方法不需要你在运行 composer update 命令前删除你磁盘中的依赖文件。
    
    为什么说“比较符”和“通配符”相结合的版本约束是?
    这是人们常犯的一个错误，定义了类似 >=2.* 或 >=1.1.* 的版本约束。
    >=2 表示资源包应该是 2.0.0 或以上版本。
    2.* 表示资源包版本应该介于 2.0.0 （含）和 3.0.0（不含）之间。
    Composer 将抛出一个错误，并告诉你这是无效的。想要确切的表达你意思，最简单的方法就是仅使用“比较符”和“通配符”其中的一种来定义约束。
    eg: "php":">=7.0.0" ,"packet": "~2.1.0"

#### composer.json
    {
        "name": "包名",
        "description": "描述",
        "keywords": "关键字,用于搜索",
        //引入的安装包
        "require": {
            "fzaninotto/faker": "^1.8"
        },
        //自动加载其他文件
        //大体为5中类型: classmap , files , namespaces , psr4 , static
        "autoload": {
            //psr-4文件加载,有命名空间 one
            "psr-4": {
                //文件名\\: 文件夹路径(所有文件,需要命名空间)
                "bookLog\\": "bookLog",
                //lib文件夹下的main文件中的文件
                "lib\\main\\": "lib/main"
            },
            //可以用 classmap 生成,支持自定义加载的不遵循 PSR-0/4 规范的类库
            "classmap": ["src/", "lib/", "Something.php"],
            //文件加载,无命名空间,作为函数库的载入方式（而非类库）
            "files": ["comFunction/function.php"]
        },
        //psr-4 two
        "psr-4": { "Monolog\\": ["src/", "lib/"] },
        //配置信息 (详情 https://docs.phpcomposer.com/04-schema.html )
        "config": {
            "process-timeout": 1800,
            "preferred-install": "dist"
        },
        //使用自定义的包资源库
        "repositories": {
            "packagist": {
                "type": "composer",
                "url": "https://packagist.phpcomposer.com"
            }
        },
        //使用阿里镜像包
        "repositories": {
            "packagist": {
                "type": "composer",
                "url": "https://mirrors.aliyun.com/composer/"
            }
        }
    }
    
#### 常见问题
    1. TP > 5.0 使用 composer:2.0.0 以上，无法下载 thinkphp 主体框架，
    ==>   建议回退到 composer:1.*.* 最新版本。

    2. Failed, trying the next URL (0: The "https://dl.laravel-china.org/guzzlehttp/guzzle/407b0cb880ace85c9b63c5f9551db498cb2d50ba.zip" 
       file could not be downloaded: SSL operation failed with code 1. OpenSSL Error messages:
       error:1416F086:SSL routines:tls_process_server_certificate:certificate verify failed
       Failed to enable crypto
    ==> 根据提示报错，定位为 SSL 证书问题。本地错误概率很小
        点击下载的地址，你会发现此地址为非安全地址(本地项目读取了lock没有更新)
        删除 composer.lock ，再次安装

    3. Could not find a version of package PackageName matching your minimum-stability (stable). Require it   
       with an explicit version constraint allowing its desired stability.                                                                    
    ==> composer require PackageName (composer 没有找到对应 Package 匹配的信息)
    一般来说，在对应的 PackageName 后面加入对应的分支或者版本号，可以按照以下顺序尝试
    ==> composer require PackageName master 
    ==> composer require PackageName dev-master 
    ==> composer require PackageName:1.*
