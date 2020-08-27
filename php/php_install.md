#### 必备软件
    #error:no acceptable C compiler found in $PATH (没有gcc)
    yum install gcc
    
    yum install libxml2-devel
    
    yum install openssl openssl-devel
    
    yum install bzip2 bzip2-devel
    
    yum -y install curl-devel
    
    yum install libmcrypt libmcrypt-devel mcrypt mhash
    
    yum install readline-devel
    
    #静态指针前置包
    yum install systemtap-sdt-devel
    
    #configure: error: Cannot find OpenSSL's <evp.h>
    yum install libssl-dev 
    
    如果没有找到对应的源码需要扩展yum源
    yum install epel-release
    #yum  install epel-release
    yum update
    
    Ubuntu版本需要apt-get update,apt-get install,若还是不能安装依赖[应用程序 -> 软件和更新 -> 更新 -> 勾选 重要安全更新 和 推荐更新]
    
#### 下载 [官网地址](https://www.php.net/downloads.php)
    官方下载 php.tar.gz
    
    #解压进入
    tar -zxvf php-7.1.gz 

    cd php-7.1

#### 目录和文件说明
    ./buildconf         #运行 autoconf 生成 ./configure 脚本和 autoheader 生成 main/php_config.h.in 模板, autoconf 需要安装 (yum install autoconf)
    ./configure         #配置信息
    build/              #顾名思义，这里主要放置一些和源码编译相关的一些文件，比如开始构建之前的buildconf脚本等文件，还有一些检查环境的脚本等。
    ext/                #官方扩展目录，包括了绝大多数PHP的函数的定义和实现，如array系列，pdo系列，spl系列等函数的实现，都在这个目录中。个人写的扩展在测试时也可以放到这个目录，方便测试和调试。
    main/               #这里存放的就是PHP最为核心的文件了，主要实现PHP的基本设施，这里和Zend引擎不一样，Zend引擎主要实现语言最核心的语言运行环境。
    Zend/               #Zend引擎的实现目录，比如脚本的词法语法解析，opcode的执行以及扩展机制的实现等等。
    pear/               #“PHP 扩展与应用仓库”，包含PEAR的核心文件。
    sapi/               #包含了各种服务器抽象层的代码，例如apache的mod_php，cgi，fastcgi以及fpm等等接口。
    TSRM/               #PHP的线程安全是构建在TSRM库之上的，PHP实现中常见的*G宏通常是对TSRM的封装，TSRM(Thread Safe Resource Manager)线程安全资源管理器。
    tests/              #PHP的测试脚本集合，包含PHP各项功能的测试文件
    win32/              #这个目录主要包括Windows平台相关的一些实现，比如sokcet的实现在Windows下和*Nix平台就不太一样，同时也包括了Windows下编译PHP相关的脚本。

#### 配置说明
    ./buildconf                       #没有configure则生成configure文件
    ./buildconf --force               #强制生成configure
    
    ./configure --help                #查看配置命令
    ./configure --help | grep enable  #查看可选配置

	./configure 
	--prefix=/usr/local/php           #安装地址
	--with-config-file-path=/etc      #配置文件
	--enable-inline-optimization 	  #开启功能	
	--disable-debug                   #关闭debug	
	--disable-rpath 
	--enable-shared
	--enable-opcache 
	--enable-fpm                      #运行php-fpm,如果没有配置,则不会生成 sbin/php-fpm
	--with-fpm-user=www               #运行用户
	--with-fpm-group=www 
	--with-mysql=mysqlnd 
	--with-mysqli=mysqlnd 
	--with-pdo-mysql=mysqlnd 
	--with-gettext 
	--enable-mbstring 
	--with-iconv 
	--with-mcrypt 
	--with-mhash 
	--with-openssl 
	--enable-bcmath 
	--enable-soap
	--with-libxml-dir 
	--enable-pcntl 
	--enable-shmop 
	--enable-sysvmsg 
	--enable-sysvsem 
	--enable-sysvshm 
	--enable-sockets 
	--with-curl 
	--with-zlib 
	--enable-zip 
	--with-bz2 
	--with-readline 
	--without-sqlite3 
	--without-pdo-sqlite 
	--with-pear	
	--enable-maintainer-zts	# pthreads的前置包	
	--enable-dtrace #静态探针
	--with-png-dir --with-freetype-dir --with-jpeg-dir --with-gd #gd库安装

    #完整示例
	./configure --prefix=/usr/local/php --with-config-file-path=/etc --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-opcache --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gettext --enable-mbstring --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-sockets --with-curl --with-zlib --enable-zip --enable-dtrace --enable-maintainer-zts --with-bz2 --with-readline --without-sqlite3 --without-pdo-sqlite --with-pear
	#--with-png-dir --with-freetype-dir --with-jpeg-dir --with-gd 暂时有问题
	#./configure –enable-pcntl 编译pcntl,多线程
	
	#Ubuntu安装
	#前置软件
	sudo apt-get install gcc g++ libacl1-dev libxml2-dev libssl-dev openssl libssl-dev pkg-config libbz2-dev libcurl4-openssl-dev libpng12-dev libgd-dev libgmp-dev libedit-dev -y
    ./configure --prefix=/usr/local/php7.3 --htmldir=/usr/local/php7.3/doc --with-config-file-path=/usr/local/php7.3 --enable-fpm --with-fpm-user=www-data --with-fpm-group=www-data --with-fpm-acl --with-litespeed --enable-phpdbg --enable-phpdbg-webhelper --enable-phpdbg-debug --enable-debug --enable-sigchild --disable-short-tags --with-openssl --with-libxml-dir --with-system-ciphers --with-pcre-regex --with-pcre-jit --with-zlib --enable-bcmath --with-bz2 --enable-calendar --with-curl --enable-exif --with-gd --enable-ftp --with-freetype-dir --enable-gd-jis-conv --with-gettext --with-gmp --with-mhash --enable-intl --enable-mbstring --enable-pcntl --with-pdo-mysql --with-libedit --with-readline --enable-shmop --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-zip --enable-mysqlnd --with-pear
	
#### 配置解释
    #查看当前配置说明
    ./configure --help | less
    
    #通用的 autoconf 选项,还有很多这里只列举一些
    --prefix=/usr/local/php    
   
    # PHP 特定的设置
    #1.开关来选择编译的扩展和 SAPI
    --enable-NAME
    --disable-NAME
    
    #2.如果扩展或 SAPI 有外部依赖，你必须使用
    --with-NAME
    --without-NAME
    
    #3.如果 NAME 所需要的库不在默认位置（例如，因为你自己编译），你可以指定使用位置。 
    -with-NAME=DIR 

    #最简版本
    ./configure --disable-all (编译最简单版本的php)
    
    #错误处理
    make clean              #清楚编译缓存
    ./buildconf --force     #强制生成 config
    ./config.nice           #最后一次 ./configure 调用
    make -jN                # N 是内核的数量
    make distclean          # 达到更强效的清理目标。它除了运行正常的清理，还会回滚所有 ./configure 命令调用带来的的文件。它会删除配置缓存、make 文件、配置头文件和其他各种文件。顾名思义，该目标是 “分布清理”，所以通常由发行管理者使用。
    
#### 编译
    make && make install

#### 配置
* 配置 php

    
    安装完后 会提示对应的安装地址
    对应的php安装地址
    /usr/loacl/php/bin 配置系统变量
    
* 配置 php-fpm    


    配置php-fpm
    cd /usr/local/php/etc
    cp php-fpm.conf.default php-fpm.conf #生成配置文件

* 配置 www.conf


    cd /usr/local/php/etc/php-fpm.d
    
    #文件中的用户和组都是www最好新建一个www用户
    cp www.conf.default www.conf   
    
* 配置 php.ini


    如果安装过后没有对应的php.ini
    /usr/local/php/bin/php --ini //查看ini的对应目录
    
    在 php.tar.gz 的解压文件中(对应的安装源码) 复制 php.ini-development
    到对应的php ini目录 --with-config-file-path=/etc (这里指定的目录是etc)    

* 启动

    
    /usr/local/php/sbin/php-fpm #可能会报php-fpm.d的错误
    
    #cannot get gid for group ‘nobody’(新增用户组)
    sudo groupadd nobody
    
#### bin目录说明
    cd /usr/local/php/bin && ls
    
    ./pear          #pear安装扩展,是PHP的扩展代码包,Pear是PHP的上层扩展
    ./peardev
    ./pecl          #pecl安装扩展,是PHP的标准扩展,Pecl是PHP的底层扩展。
    ./phar
    ./php
    ./php-cgi
    ./php-config    #提供有关于 PHP 构建的配置的信息
    ./phpdbg
    ./phpize        #为扩展生成 configure 文件, 相当于 ./buildconf 的扩展。它会从 lib/php/build 复制各种文件，并调用 autoconf/autoheader.


#### 扩展安装:(pecl、pear)
    建议在php对应的安装目录运行安装
    /usr/local/php/bin
    
    eg: pecl install msgpack
    
    #具体报错可以根据提示进行解决
    PHP Startup: Unable to load dynamic library '/usr/local/php/lib/php/extensions/no-debug-non-zts-20160303/msgpack.so' - /usr/local/php/lib/php/extensions/no-debug-non-zts-20160303/msgpack.so: cannot open shared object file: No such file or directory in Unknown on line 0
    对应安装的扩展没有在php.ini的扩展目录中
    find / -name swoole.so  //查找已有的扩展目录(对应的有两个地址)
    find / -name msgpack.so //新安装的扩展(复制到对应php扩展目录) 
    
    /usr/local/lib/php/extensions/no-debug-non-zts-20160303/msgpack.so  //系统pecl安装的默认扩展目录
    /usr/local/php/lib/php/extensions/no-debug-non-zts-20160303/msgpack.so //php.ini系统扩展目录

#### 扩展安装:(phpize手动编译扩展)
	wget http://youExtension.tgz
	tar -zxvf
	cd youExtension
	
	#使用 phpize (绝对地址), 生成 ./configure 文件
	/usr/local/php7/bin/phpize
	
	#配置 php-config (shell文件,可执行,这里不是配置文件的地址哦) 地址,一般来说这里的 php-config 都在 bin 中
	./configure --with-php-config=/usr/local/php7/bin/php-config
	make && make install

	echo "extension=pthreads.so" >> /etc/php.ini #添加配置
	
#### 扩展安装:(源码树)
     将 `扩展源码` 下载并解压至, php 源码包的 ext/ 目录中
     再次运行编译 --enable-Name
     rm configure && ./buildconf --force 	    #删除之前的配置并且强制生成配置
    
     ./config.nice --enable-name                #使用最后一次的调用
     # or
     ./configure --enable-name --other-options  #重新编译
      
#### 开启扩展 (--enable-expand)
     #这里以 bcmath 为例,在最开始的时候没有开启扩展(--enable-bcmath),但后期想使用某个扩展
     #这里需要有源码的安装包,如果没有可以重新下载一个,这里建议保留源码包
     
     #进入源码包地址
     cd /home/demo/source/php-7.3.10/
     
     #进入扩展包,列出已有的包
     cd ext/  && ll
     
     #进入 bcmath 包
     cd bcmath/
     
     #使用 phpize (绝对地址), 生成 ./configue 文件
     /usr/local/php7/bin/phpize
     
     #配置 php-config (shell文件,可执行,这里不是配置文件的地址哦) 地址,一般来说这里的 php-config 都在 bin 中
     ./configure --with-php-config=/usr/local/php7/bin/php-config
     
     #编译并且编译安装
     make && make install
     
     #安装完成后添加对应的配置,也可以查看扩展被安装到哪里了
     #一般来说在 php目录下的 main/lib/php/extensions,具体可以产考安装输出信息
     Installing shared extensions:     /usr/local/php7/main/lib/php/extensions/no-debug-non-zts-20180731/

     #添加配置
     echo "extension=bcmath.so" >> /etc/php.ini #添加配置
     
     #当持续报错,非PHP库
     PHP Startup: Invalid library (maybe not a PHP library)
     #检测当前是否有多个PHP版本
     #安装的时候是否使用得到 libtool 和对应的 libdir
     #手动设置扩展地址 php.ini  => extension_dir(扩展地址) ,仅用于调试,不推荐
     #还是不行的话,重新下载安装,重复步骤,具体看安装的提示信息,shell脚本等
     

#### 扩展依赖:
	 undefined reference to `libiconv_open 无法编译PHP libiconv

	 #wget http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.13.1.tar.gz
     #tar -zxvf libiconv-1.13.1.tar.gz
     #cd libiconv-1.13.1
     # ./configure --prefix=/usr/local/libiconv
     # make
     # make install
     再检查php，指定 iconv的位置 --with-iconv=/usr/local/libiconv
     #./configure --with-iconv=/usr/local/libiconv
     #make
     #make install
     或者
     --without-iconv #不安装:-D

#### CLI命令
    #查看 expandName 的版本信息, reflection ini
    php --ri expandName 
    
    #查看 expandName 的扩展信息(源码,参数等), reflection extension
    php --re expandName 
    
    #查看 php 模块信息
    php -m
    
#### 安装配置nginx
#### hosts设置
    #windows hosts配置
    192.168.124.129  mint.test  #如果nginx没有min.test站点那么对应的是默认站点
    192.168.124.129  mint.1     #对应nginx的站点min.1

    #Linux
    sudo vim /etc/hosts
    sudo /etc/init.d/dns-clean start
    sudo /etc/init.d/networking restart
    
#### 权限修改
	搭建完成之后有些操作可能无法执行(权限问题)
	#这里根据对应运行的用户进行设置
	#把www文件夹下的所以文件归属于www用户/组  用户 文件夹
	chown -R www www/	 

#### 其它安装
    Please check your autoconf installation and the
	$PHP_AUTOCONF environment variable. Then, rerun this script.
	错误提示:没有autoconf.autoconf依赖于m4
	yum install m4
	yum install autoconf
	pecl install swoole

	当安装对应扩展始终无法动态加载扩展库时,删除安装的php从新安装.
	(可能安装之前已经有php的环境变量,导致系统变量污染)

	安装telnet: 
	yum -y install xinetd telnet telnet-server
	service xinetd restart // 重启服务
	# systemctl status telnet.socket
	如果显示inactive则表示没有打开请执行
	# systemctl enable telnet.socket 加入开机启动
	# systemctl start telnet.socket 启动Telnet服务
	# systemctl status telnet.cocket 再次查看服务状态

	安装gd库
	 ./configure --with-php-config=/usr/local/php/bin/php-config --with-png-dir --with-freetype-dir --with-jpeg-dir --with-gd
	yum -y install libjpeg-devel  # jpeglib.h not found
	yum -y install libpng-devel   # png.h not found

	安装mongo:
	pecl install mongodb ;(如果无法连接或者报版本限制,解决如下)
	下载mongodb的压缩包
	pecl install mongodb-1.13.14.tgz

#### 重启
    php重启1:
	/usr/local/php/sbin/php-fpm (start|stop|reload) #比较老的版本
	ps aux | grep php-fpm 
	kill 15891 # 对应的master进程ID 
	killall -9 php-fpm #全部杀死
	
	php重启2:
	ps aux | grep php-fpm  #查看对应的php-fpm.conf文件地址
	取消pid=run/php-fpm.pid的注释
	kill `cat php-fpm.pid的地址`  #这里是反引号

    通过ps显示进程id,然后杀死
    ps aux | grep php-fpm | xargs kill

    php版本信息不一致[浏览器版本信息和CLI模式的版本信息]
    $PATH 查看php命令是否在环境变量中
    php -v  #查看环境变量中的版本信息
    /usr/local/php/bin/php -v #查看php安装目录的版本信息
    type php #查看php的类型目录
    把最新版本的php复制到 type php的目录中

    开机自启
    vi /lib/systemd/system/php-fpm.service
    内容如下：
    [Unit]
    Description=php-fpm
    After=network.target
    [Service]
    Type=forking
    ExecStart=/usr/local/php/sbin/php-fpm #php-fpm地址
    PrivateTmp=true
    [Install]
    WantedBy=multi-user.target
    #开机自启
    systemctl enable php-fpm.service


    vi /lib/systemd/system/nginx.service
    内容：
    [Unit]
    Description=nginx
    After=network.target
    [Service]
    Type=forking
    ExecStart=/usr/local/nginx/sbin/nginx
    ExecReload=/usr/local/nginx/sbin/nginx -s reload
    ExecStop=/usr/local/nginx/sbin/nginx -s stop
    PrivateTmp=true
    [Install]
    WantedBy=multi-user.target

    解释
    Description:描述服务
    After:描述服务类别
    [Service]服务运行参数的设置
    Type=forking是后台运行的形式
    ExecStart为服务的具体运行命令
    ExecReload为重启命令
    ExecStop为停止命令
    PrivateTmp=True表示给服务分配独立的临时空间
    注意：[Service]的启动、重启、停止命令全部要求使用绝对路径
    [Install]运行级别下服务安装的相关设置，可设置为多用户，即系统运行级别为3
    
    
#### 应用程序开发 [PHP-GTK](http://gtk.php.net/)    
    通过 PHP-GTK 可以开发应用程序，客户端等

#### PHP-FPM 相关

##### WARNING: [pool www] seems busy (you may need to increase pm.start_servers, or pm.min/max_spare_servers), spawning 8 children, there are 42 idle, and 93 total children
    pool 繁忙,建议提示对应的配置.产考 php-fpm.conf 
    如果配置过后, 任然有此报错, 可能是某些请求触发时间较长, 导致线程无法回收, 可以从 服务器,数据库,连接占用等多方面排查!
    可以配置对应的慢日志! php-fpm: slowlog, nginx: slowlog, mysql: log
    
#### 其它

##### 查看对应 `php-fpm` 内存使用
    ps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\n", sum/NR/1024,"M") }'
    
##### 查看 `php-fpm` 个数
    ps -ylC php-fpm --sort:rss | wc -l