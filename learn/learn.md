#### A Company
	API网关设计:
		1.对客户端的身份认证
		2.通信会话的秘钥协议
		3.报文的加密解密(数据加密)
		4.日常的流控和紧急屏蔽
		5.支持集群框架
		6.路由控制
		7.客户端优先超时的机制
	分布式(不同业务模块分布到对应的服务器通过API相互访问)[模块]
	集群(同一个系统分布到不同的服务器中)[系统]
	kafka 发布订阅消息系统(MQ)
	Kerberos 安全网络认证系统
	Kerberos 是Windows活动目录中使用的客户/服务器认证协议，为通信双方提供双向身份认证。
	相互认证或请求服务的实体被称为委托人（principal）。参与的中央服务器被称为密钥分发中心（简称KDC）。
	KDC有两个服务组成：身份验证服务（Authentication Server，简称AS）和票据授予服务（Ticket Granting Server，简称TGS）
							用户的登录信息对应KDC中有数据
		用户登录   -------------------------------------------------->  KDC(kerberos center)  (princiacal{委托人}名称[用户名])
				  <---------------------------------------------------
				  	    返回KDC的ticket,用户和KDC都有同一份数据
	单点登录  针对服务器集群一台服务器登录,其他服务器共享
	分布式系统  把系统拆分成模块,把模块分布到不同的服务器上,之间用API请求,公共部分用RPC
	服务中转站  通过统一的服务入口分发到不同服务器
	密钥服务器(密钥分发中心)  用统一的密钥服务器发送密钥
	微信活码	微信防封措施,二维码永久生效
	线路跳转方式(短连接优化)
	前端h5打包App(HBuilder)
	二维码(芝麻,草料,新浪)
	CDN
	OSS
	docker
	GuzzleHttp (请求插件)

#### B Company
	windos通过映射网络驱动去修改linux的文件信息
	MetInfo  / wordpress 网站模板
	啊D   注入工具
	Bash Shell
	swagger 文档生成工具
	mixphp 基于swoole运行的开发的Api框架
	Validator 验证集成插件
	laravle Carbon 后台获取时间插件
	yaf

	后台API启动 beauty_api  ./start-guz.sh
	C端前端启动 frontend-customer npm run dev

#### C Company
	(单点登录)passport.qaq.com : php-cgi -b 127.0.0.1:9001 php.ini  (QQ登录)
	(前台登录)trade.qaq.com		(QQ登录)
	(后台登录)steam.admin.com

	#批量删除文件夹中名字为.gitignore
	find  .  -name  '.gitignore'  -type  f  -print  -exec  rm  -rf  {} \;

	前台登录[认证信息] -> 单点登录[信息处理] -> 共享cookie或session -> 返回登录成功标识

	#可以插件
	Ucenter 	简单的单点登录
	Kerboers	单点登录服务器
	#简单操作
	session操作(db,nosql)
	cookie操作

	#使用
	MyCat

    创建软链接 :
	    mklink /J D:\Obj\passport\common\core[项目core,common中不用创建core文件夹] D:\Obj\core[真实的core]
        为 D:\Obj\passport\common\core <<===>> D:\Obj\core 创建的联接
    删除软链接 :
        rmdir D:\Obj\passport\common\core
        rmdir D:\Obj\core
        rmdir删除虚拟目录     del实际删除

    Capistrano(ruby编写) 发包工具

    rabbitMQ(消息系统)

    serviceMesh(阿里中间件)

    passport(单点登录系统)

   (各系统登录使用的是 Yii2 user 组件登录, cache 为 redis 和 cookie )
   A(访问) -> 发送jsonp请求(前端) ->  passport(login,缓存登录信息)
              已登陆(请求A登录->单点登录并记录公用缓存信息然后回调传参SESSION_KEY到A系统通过缓存共用一个SESSION_KEY,获取用户信息,实行本地登录
              未登录(无操作)

#### D Company (ubuntu)
    php时间类:
    DateInterval
    DateTime
    Carbon

    php开启错误信息:
    php.ini
    display_errors = On (开启错误信息) => 代码 ini_set('display_errors','yes');

    修改hosts:
    sudo vim /etc/hosts
    sudo /etc/init.d/dns-clean start
    sudo /etc/init.d/networking restart

    .user.ini无法删除:
    chattr +i /home/wwwroot/yoursite/.user.ini
    sudo chattr -i .user.ini rm -rf .user.ini

    rar解压:
    sudo apt-get install rar
    sudo apt-get install unrar
    rar x test.rar

    zip解压:
    sudo apt-get install unar //安装unar解压工具
    unar document.zip //解压相应的zip文件
    //瞬间就能解决zip解压中文乱码的问题。

    git缓慢:
    1、在hosts文件里追加以下内容（IP需要替换掉），以下5个域名一个都不要少，有些文章只写了一部分，我一开始就少了个github.com，结果速度就还是很慢。
    151.101.109.194 github.global.ssl.fastly.net
    185.199.110.153 assets-cdn.github.com
    151.101.108.133 avatars0.githubusercontent.com
    151.101.76.133 avatars1.githubusercontent.com
    192.30.253.112 github.com
    2、IP替换方法，打开 http://tool.chinaz.com/dns ,查询域名IP映射，把以上5个域名挨个查询一下，找一个TTL值比较小的IP替换掉。注意替换前要把IP先Ping一下，确保是通的才替换，否则是无效的。

    ngiux proxy_pass:
    //将 www.vd.com 转发至指定地址
    server {
    server_name www.vd.com;

    location / {
    proxy_pass http://localhost/obj/ppsns-tp51/public/index.php?s=;
    }
    }

    navicat乱码:
    选择工具-首选项，常规页签下，字体选择Noto Sans Mono CJK TC Regular
    编辑器页签下编辑器字体选择Noto Sans CJK SC Regular
    记录页签下网格字体选择Noto Sans Mono CJK TC Regular
    修改完成后，重启软件后，编码问题即可解决

    ubuntu U盘
    sudo apt-get install exfat-utils
    重启生效

    wine        兼容层,可兼容CentOs/Ubuntu/Linux/Mac
    衍生软件:
        winetricks  安装windows程序
        crossover   安装windows程序
        playonlinux 安装windows程序


    //视频时长
    include_once '../extend/getidmaster/getidthree/getid3.php';
    $t = new \getID3();
    $ThisFileInfo = @$t->analyze($path); //分析文件，$path为音频文件的地址（文件绝对路径）
    $ThisFileInfo['playtime_seconds']; //时长

#### E Company(Mac)

    服务划分、SQL优化、领域驱动、微服务