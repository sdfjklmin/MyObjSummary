
API网关设计:
	1.对客户端的身份认证
	2.通信会话的秘钥协议
	3.报文的加密解密(数据加密)
	4.日常的流控和紧急屏蔽
	5.支持集群框架
	6.路由控制
	7.客户端优先超时的机制
kafka 发布订阅消息系统(MQ)	
Kerberos 安全网络认证系统
Kerberos 是Windows活动目录中使用的客户/服务器认证协议，为通信双方提供双向身份认证。
相互认证或请求服务的实体被称为委托人（principal）。参与的中央服务器被称为密钥分发中心（简称KDC）。
KDC有两个服务组成：身份验证服务（Authentication Server，简称AS）和票据授予服务（Ticket Granting Server，简称TGS）
						用户的登录信息对应KDC中有数据	♘⚚关羽♘⚚关羽♘⚚关羽♘⚚关羽♘⚚关羽♘⚚关羽
	用户登录   -------------------------------------------------->  KDC(kerberos center)  (princiacal{委托人}名称[用户名])
			  <---------------------------------------------------
			  	    返回KDC的ticket,用户和KDC都有同一份数据				
单点登录
分布式系统 
服务中转站
密钥服务器(密钥分发中心)
