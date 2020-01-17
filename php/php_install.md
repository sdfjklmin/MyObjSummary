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
    
    tar -zxvf php-7.1.gz 解压进入

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
	--enable-soap -
	-with-libxml-dir 
	--enable-pcntl 
	--enable-shmop 
	--enable-sysvmsg -
	-enable-sysvsem 
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
    


#### PHP扩展安装
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

#### 手动编译扩展
	wget http://youExtension.tgz
	tar -zxvf
	cd youExtension
	
	#生成./configue文件
	/usr/local/php7/bin/phpize
	
	#配置 php-config 地址 
	./configure --with-php-config=/usr/local/php7/bin/php-config
	make && make install

	echo "extension=pthreads.so" >> /etc/php.ini #添加配置

#### 扩展依赖:
	 undefined reference to `libiconv_open 无法编译PHP libiconv

	 #wget http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.13.1.tar.gz
     #tar -zxvf libiconv-1.13.1.tar.gz
     #cd libiconv-1.13.1
     # ./configure --prefix=/usr/local/libiconv
     # make
     # make install
     再检查php，指定 iconv的位置  --with-iconv=/usr/local/libiconv
     #./configure --with-iconv=/usr/local/libiconv
     #make
     #make installx
     或者
     --without-iconv #不安装:-D

#### CLI命令
    #查看yar的版本信息
    php --ri yar 

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