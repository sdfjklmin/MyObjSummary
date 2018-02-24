巨鲸互娱:
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

米兰网:
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
