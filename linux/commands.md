## Linux 常用命令

### 系统命令

- top  **显示当前系统中耗费资源最多的进程**
- date  **显示系统当前时间**

      date "+[params]"      #特定输入要带上+ 
      date "+%d"            #输出当日
      date "+%Y-%m-%d"      #2019-09-06
      date "+现在时间是: %Y-%m-%d %H:%M:%S"
      
      date +%Y%m%d               #显示前天年月日 
      date -d "+1 minute" +%Y%m%d   #显示前一分钟的日期 
      date -d "+1 day" +%Y%m%d      #显示前一天的日期 
      date -d "-1 day" +%Y%m%d      #显示后一天的日期 
      date -d "-1 month" +%Y%m%d    #显示上一月的日期 
      date -d "+1 month" +%Y%m%d    #显示下一月的日期 
      date -d "-1 year" +%Y%m%d     #显示前一年的日期 
      date -d "+1 year" +%Y%m%d     #显示下一年的日期


- 命令替换
 
      $( ) 与 ` `（反引号）都是用来作命令替换的。
      与变量替换差不多，都是用来重组命令行的，先完成引号里的命令行，然后将其结果替换出来，再重组成新的命令行。

- df 显示系统可用空间, df -h 便于可读方式

- du 显示指定目录以及子目录已使用磁盘空间的总和

      du -s     #指定目录的总和,以目录显示。 du -s * , 当前所有
      du -h     #便于可读方式
      du -sh    #结合使用
      
- free 当前内存和交换空间的使用情况
      
- hostname 当前主机名称      

### 操作命令

##### Folder(目录介绍)
    /bin (binaries)二进制可执行命令
    /dev (devices)设备特殊文件
    /etc (etcetera)系统管理和配置文件
    /etc/rc.d 启动的配置文件和脚本
    /home 用户主目录的基点，比如用户user的主目录就是/home/user，可以用~user表示
    /lib (library)标准程序设计库，又叫动态链接共享库，作用类似windows里的.dll文件
    /sbin (super user binaries)存放二进制可执行文件，只有root才能访问,超级管理命令，这里存放的是系统管理员使用的管理程序
    /tmp (temporary)公共的临时文件存储点
    /root 系统管理员的主目录
    /mnt (mount)系统提供这个目录是让用户临时挂载其他的文件系统
    /lost+found这个目录平时是空的，系统非正常关机而留下“无家可归”的文件（windows下叫什么.chk）就在这里
    /proc 虚拟的目录，是系统内存的映射。可直接访问这个目录来获取系统信息。
    /var (variable)用于存放运行时需要改变数据的文件,某些大文件的溢出区，比方说各种服务的日志文件
    /usr (unix shared resources)最庞大的目录，要用到的应用程序和文件几乎都在这个目录
    /usr/local/bin (该目录下的执行命令为全局执行命令,自定义的需要 chmod +x command,在执行)

##### 系统小命令
    whereis  abc   		#abc在哪里
    who i am 			#显示当前登录的人    

##### 关机重启
    halt  				#立刻关机 
    poweroff  			#立刻关机 
    shutdown -h now 	#立刻关机(root用户使用) 
    shutdown -h 10 		#10分钟后自动关机
    reboot 				#重启
    
##### 查看网络
    ip addr #基于ip table
    ifconfig     
    
##### echo
    echo > file             #清空file    
    echo 'test' > file      #覆盖file文件,填入内容test
    echo 'test' >> file     #向file文件中,追加内容test
    echo '32'               #输出32
    
##### cat
    cat file                #查看file内容
    cat -n file             #查看file内容带数字编号
    cat -b file             #查看file内容带数字编号但不对空白行编号
    cat -n file1 > file2    #把file1内容带编号输入到file2中
    cat --help              #查看更多
       
##### more less head tail
    more :
         -d          显示帮助而非响铃
         -f          计算逻辑行数，而非屏幕行数
         -l          屏蔽换页(form feed)后的暂停
         -c          不滚动，显示文本并清理行末
         -p          不滚动，清除屏幕并显示文本
         -s          将多行空行压缩为一行
         -u          屏蔽下划线
         -<数字>     每屏的行数
         +<数字>     从指定行开始显示文件
         +/<字符串>  从匹配搜索字符串的位置开始显示文件
    more +100 abc.php 从100行开始显示  逐行显示
    
    less +100 abc.php 从100行开始显示  支持上下滚动查看
    
    head    file           #查看文本开头部分,默认10行
        -[num]          #指定行数
         
    tail    file           #查看文本结尾部分,默认10行,
        -[num]          #指定行数
        -f              #循环滚动读取文件并动态显示在屏幕上,根据文件属性跟踪
        -F              #循环滚动读取文件并动态显示在屏幕上,文件文件名跟踪
        
    tail -f log.log | nl  #带行号
    
    tail -f index.php  | grep -e error -e ERROR -n
    #显示error和ERROR并带上行号
   
    tail -f  $(date +%d).log | grep -n error
    #显示当前日期的.log文件中的 error

##### 文本统计
    wc      file        #统计文本的 行数、字数、字符数
       -m               #字符数    
       -w               #文本字数
       -l               #文本行数
    
##### 移动删除复制
    mv file /home/ 		#移动file到/home下 
    mv file file2       #重命名
    rm  				#删除 rm -r 删除文件和文件夹   
    rm -rf              #删除文件和文件夹不用提示
    cp  				#复制
    cp -r dir dir       #复制文件夹 
    
##### 创建文件夹
    mkdir dir #创建文件夹dir
    mkdir -p ~/nginx/www ~/nginx/logs ~/nginx/conf  #批量创建目录      

##### 创建文件
    touch file  #创建file文件

##### 包管理    

###### yum(centos)    
    yum --help          #查看yum相关的帮助
    yum clean all       #清除所有安装记录
    yum update          #更新yum
    yum remove php      #移除php
    yum install php
    yum install -y epel-relase #自动安装依赖
    yum search php
###### dpkg(ubuntu)

###### apt|apt-get(deb包管理式)

###### rpm(软件包管理器)

##### 解压
    tar 主要用于创建归档文件,和解压归档文件,其本身是没有压缩功能的,但可以调用 gzip bzip2 进行压缩处理.
		参数解释：
			-c 创建归档
			-x 解压归档
			-v 显示处理过程
			-f 目标文件，其后必须紧跟 目标文件
			-j 调用 bzip2 进行解压缩
			-z 调用 gzip 进行解压缩
			-t 列出归档中的文件

    eg:
    $ tar -cvf filename.tar .       ### 将当前目录所有文件归档,不压缩,后面有个.代表当前目录的意思 
    $ tar -xvf filename.tar         ### 解压 filename.tar 到当前文件夹
    $ tar -cvjf filename.tar.bz2 .  ### 使用 bzip2 压缩
    $ tar -xvjf  filename.tar.bz2   ### 解压 filename.tar.bz2 到当前文件夹
    $ tar -cvzf filename.tar.gz     ### 使用 gzip  压缩
    $ tar -xvzf filename.tar.gz     ### 解压 filename.tar.gz 到当前文件夹
    $ tar -tf   filename            ### 只查看 filename 归档中的文件，不解压
    
##### 分组,权限
    chown 用于改变一个文件的所有者及所在的组。
    chown user filename        ### 改变 filename 的所有者为 user
    chown user:group filename  ### 改变 filename 的所有者为 user，组为 group
    chown -R root folder       ### 改变 folder 文件夹及其子文件的所有者为 root
    
    chmod 永远更改一个文件的权限,主要有读取,写入,执行其中 所有者,用户组,其他 各占三个,因此 ls -l以看到如下的信息
    -rwxr--r-- 1 locez users   154 Aug 30 18:09 filename
    其中 r=read ， w=write ， x=execute
        4		  2			1		
    chmod +x filename        ### 为 user ，group ，others 添加执行权限
    chmod -x filename        ### 取消 user ， group ，others 的执行权限
    chmod +w filename        ### 为 user 添加写入权限
    chmod ugo=rwx filename   ### 设置 user ，group ，others 具有 读取、写入、执行权限
    chmod ug=rw filename     ### 设置 user ，group 添加 读取、写入权限
    chmod ugo=--- filename   ### 取消所有权限
    chmod -R 777 /minPro	 ### 把minPro下的所有文件赋予权限
    drwxr-xr-x =>d rwx r-x r-x  ### d:文件类型 rwx:文件拥有者的权限 r-x:与文件拥有者同用户组的其它用户 r-x:其它用户组用户权限

##### 查找
    find:
        find path -option parm [ -print ] [ -exec -ok command ] {} /;
        find /(查找范围) -name (查找内容) [-type d|-print]
        find /home -name test -type d  查找home下名字为test的文件目录
        find /home -name test -print   查找home下名字为test的文件
        find /home -name test		  查找home下名为test
        find /home -name *test* | test*	
                  -name filename              #查找名为filename的文件
                  -perm                       #按执行权限来查找
                  -user   username            #按文件属主来查找
                  -group groupname            #按组来查找
                  -mtime -n +n                #按文件更改时间来查找文件，-n指n天以内，+n指n天以前
                  -atime   -n +n              #按文件访问时间来查
        
        find   / -amin   -10       # 查找在系统中最后10分钟访问的文件
        find   / -atime -2         # 查找在系统中最后48小时访问的文件
        find   / -empty              # 查找在系统中为空的文件或者文件夹
        find   / -group cat        # 查找在系统中属于 groupcat的文件
        find   / -mmin -5         # 查找在系统中最后5分钟里修改过的文件
        find   / -mtime -1        #查找在系统中最后24小时里修改过的文件
        find   / -nouser             #查找在系统中属于作废用户的文件
        find   / -user   fred       #查找在系统中属于FRED这个用户的文件
        
##### grep   
    在文本中查找
    grep a1 /home/test/t1.php  /home/test/t2.php  在t1.php,t2.php中查找a1
    grep a1 /home/test/t1.php    在/home/test/t1.php中查找a1
    grep -n a1 /home/test/t1.php    在/home/test/t1.php中查找a1
    grep -l  -option	path 		 	列出文件名(扯淡)
         -n  -option	path 			显示行号
         -v  -option	path			输出不是a1的
         ^a1 			path 		 	^符号视作特殊字符,用于指定一行或者一个单词的开始
         a1$ 			path 		 	以a1结尾的 $符号视作特殊字符,用于指定一行或者一个单词的结尾。
         -r  -option	path      		递归去查找(文件夹)
         ^ $   	path      				查找所有空行
         -i  -option	path 			忽略大小写
         -e a1 -e a1 -option	path    多个查找
         -B 4 -option	path 			匹配行的前4行
         -A 4 -option	path 			匹配行的后4行
         -C 4 -option	path 			匹配行的前后4行
    
    ls	| gerp a1    					组合使用,在当前目录下查找 
    ls  | find a1	
    grep -option parm path ;    操作参数 路径
    find path -option parm ;    路径 	 操作参数 
    
##### Vi
    i   #在光标前插入
    A   #在光标当前行末尾插入
    o   #在光标当前行的下一行插入
    O   #在光标当前行的上一行插入
    wq! #强制保存退出
    :/a #文本中搜索a   n下一个  N上一个
    :nu #显示行号
    :set nu #全文显示行号 	
    :set nonu #取消行号显示
    gg  #跳到首行
    G   #跳到末行
    :n  #跳到值定行 :4
    r   #替换光标当前的字符
    R   #从光标开始处替换,ESC结束
    u #撤回命令
    yy #复制一行
    P  #粘贴一行
    dd #删除一行
    dG #删除当前光标所在行到末尾的内容
    :5,7d #删除指定行的内容
    shift + zz #保存退出等同于 :wq
    
##### 1.添加用户和密码
	adduser  test  
	useradd -m -g users -G audio -s /usr/bin/bash newuser     
     -m 创建home目录 -g 所属的主组 -G 指定该用户在哪些附加组 -s 设定默认的 shell,newuser 为新的用户名
	passwd  test
	
##### 2.赋予root权限
	修改 etc/sudoers 
	方法一：找到下面一行，把前面的注释（#）去掉
	## Allows people in group wheel to run all commands
	%wheel    ALL=(ALL)    ALL
	然后修改用户，使其属于root组（wheel），命令如下：
	#usermod -g root tommy
	修改完毕，现在可以用tommy帐号登录，然后用命令 su - ，即可获得root权限进行操作。

	方法二：找到下面一行，在root下面添加一行，如下所示：
	## Allow root to run any commands anywhere
	root    ALL=(ALL)     ALL
	tommy   ALL=(ALL)     ALL
	修改完毕，现在可以用tommy帐号登录，然后用命令 su - ，即可获得root权限进行操作。
	
##### 3.禁止root登陆
	vi /etc/ssh/sshd_config	
	PermitRootLogin no

##### 4.登录提示信息
	编辑 vi /etc/motd 即可

##### 5.自定义命令
    a.直接修改系统命令
        vi /etc/bashrc
        在最后一行添加
        alias test="cd /minCmd"	
        source /etc/bashrc  # 使更改配置生效
    b.建立shell脚本
    c.linux自定义service
        tt.service
        [Unit]
        Description=Yii Queue Worker1 %I
    
        [Service]
        User=www
        Group=www
        ExecStart=/usr/local/php/bin/php /var/www/passport/yii queue/listen
        Restart=on-failure
    
        [Install]
        WantedBy=multi-user.target
        
    c.b linux自定义service说明
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
    

##### 6.修改主机信息
    a.使用hostname
        hostname 主机名称
        su #使修改生效
    b. vi /etc/hosts	  	
        新增:
        192.169.124.130 hostname

##### 7.将安装命令加入到系统环境中
	a.永久生效
        vi /etc/profile
        添加
            PATH=$PATH:/(对应php的安装运行目录[bin])
            PATH=$PATH:/(对应mongo的安装运行目录[bin])
            export PATH
        source /etc/profile  配置生效
        echo $PATH 查看

	b.  当前用户生效
        cd ~
        vi .bash_profile 修改文件中PATH一行，将路径加入到PATH=$PATH:$HOME/bin一行之后

    c.临时生效
		export PATH=$PATH:/(对应php的安装运行目录)


##### 8.ctrl + 回撤键	 #命令行模式输入删除

##### 9.往文件中追加信息
	echo '追加信息' >> addinfo.php

##### 10.覆盖文件中的信息
	echo '' > addinfo.php

##### 11.如果命令太长可以使用反斜杠Enter来输出  \[Enter] 
    cd /usr/local/\
    php

##### 12.env查看所有环境变量的信息

##### 13.用set可以查看所有的变量

##### 14.alias查看所有的别名

##### 15.type cd #查看cd命令的说明

##### 16.杀死进程
	a.查看进程
		ps -aux 或者 ps -ef
	b.查看某个进程 [管道]
		ps -aux | grep php-fpm	 或者 pgrep php-fpm
	c.查看xx的PID	
		pidof php-fpm   
	
	1.pidof php-fpm | xargs kill #杀死php-fpm
	2.pgrep和kill！pkill＝pgrep+kill。
	  pkill php-fpm
	  pkill -9 php-fpm
	3.kill -s 9 [pid]  
	  -s 9 制定了传递给进程的信号是９，即强制、尽快终止进程。各个终止信号及其作用见附录

##### 17.查看用户的历史命令记录
	cat ~user.bash_history > cat ~root.bash_history

##### 18.telnet
	yum install telnet
	yum install telnet-server	
	/usr/bin/telnet ip prot

##### 19.开机自启
	/etc/rc.local 加入你需要启动的命令
	/etc/rc.d/init.d/	加入你需要启动的脚本服务

##### 20.开启端口(centos7)
	firewall-cmd --zone=public --add-port=80/tcp --permanent
	重启 systemctl restart firewalld.service

##### 21.查看本机外网IP
    curl ifconfig.me

##### 22.修改root密码(CentOS7)
    选择系统,看下面的提示,然后按e,
    编辑修改两处：ro改为rw,在LANG=en_US.UFT-8后面添加init=/bin/sh
    按Ctrl+X重启，并修改密码
        echo '123456' |passwd --stdin root
    由于selinux开启着的需要执行以下命令更新系统信息,否则重启之后密码未生效
        touch /.autorelabel
    重启系统
        exec /sbin/init
        
##### 23.网络连接
    进入  /etc/sysconfig/network-scripts 编辑第一个网卡	将 ONBOOT改为yes(激活网卡)
    vi ifcfg-eno..
    
    BOOTPROTO=static[静态ip]|dhcp[动态ip]
    IPADDR=192.168.124.129
    NETMASK=255.255.255.0
    
    eg:最简单的配置
    将网络设置桥接服务物理地址
    BOOTPROTO=dhcp
    ONBOOT=yes
    
    eg:固定ip
    DEVICE="eth2"	#网卡名称
    NETMASK="255.255.255.0"   #网关
    IPADDR="192.168.1.48"		#IP地址
    GATEWAY="192.168.1.1"		#默认网关
    DNS='192.168.1.1'			#dns地址 ipconfig /all windows上的对应
    DNS1=61.139.2.69			#dns地址
    
    eg:
    DIVICE=eth7
    TYPE=Ethernet
    BOOTPROTO=static
    DEFROUTE=yes
    PEERDNS=yes
    PEERROUTES=yes
    IPV4_FAILURE_FATAL=no
    IPV6INIT=yes
    IPV6_AUTOCONF=yes
    IPV6_DEFROUTE=yes
    IPV6_PEERDNS=yes
    IPV6_PEERROUTES=yes
    IPV6_FAILURE_FATAL=no
    UUID=ab975ea9-2c4f-4df1-9601-b8a68385b1fe
    ONBOOT=yes
    NETMASK="255.255.255.0"
    IPADDR="192.168.1.108"
    GATEWAY="192.168.1.1"
    DNS1=192.168.1.1
    
    service network restart (重启网卡)