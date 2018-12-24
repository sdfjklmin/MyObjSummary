1.安装
	yum install samba    
2.启动
	根据直接的系统选择命令
	service smb start  或者 systemctl restart smb.service
3.配置
	/etc/samba 复制示例文件 修改配置
	添加访问用户
	smbpasswd -a user1 #user1要和linux下的用户匹配
4.windows访问
	关闭防火墙,测试ip是否能够ping通,
	条件凭证:控制面板\用户帐户和家庭安全\凭据管理器
	\\192.168.1.xx		
5.如果windows下登录samba服务器后无法访问linux下共享目录，提示没有权限。
	a、确保linux下防火墙关闭或者是开放共享目录权限
	b、确保samba服务器配置文件smb.conf设置没有问题，可网上查阅资料看配置办法  
	c、确保setlinux关闭，可以用setenforce 0命令执行。 默认的，SELinux禁止网络上对Samba服务器上的共享目录进行写操作，即使你在smb.conf中允许了这项操作。       /usr/bin/setenforce 修改SELinux的实时运行模式  
	setenforce 1 设置SELinux 成为enforcing模式
	setenforce 0 设置SELinux 成为permissive模式  
	如果要彻底禁用SELinux 需要在/etc/sysconfig/selinux中设置参数selinux=0 ，或者在/etc/grub.conf中添加这个参数
	  /usr/bin/setstatus -v  			
6.添加网络映射
	windows 计算机 映射网络驱动器	  
7.不允许一个用户使用一个以上用户名与一个服务器或共享资源的多重连接。中断与此服务器或共享资源的连接
	windows cmd输入
		net use * /del /y
		断开所有的连接之后重新登录即可
8.字定义访问
	在 smb.cof中添加
	[minPro] 			#windows访问的地址目录  => \\192.168.1.108\minPro
	comment = minPro	#提示
	path = /minPro		#对应指定的地址
	public = no 		#是否为公用
	writeable = yes		#共享文件夹是否可写
	browseable = yes	 #是否可浏览
	valid users = sjm	#允许登录的用户
	read only = no 		#是否只读
	create mask = 0777	 #创建文件权限定制
	directory mask = 0777	#创建文件夹权限
	force directory mode = 0777
	force create mode = 0777
9.更改文件夹权限
	chmod  -R 777 /minPro	
10.完整启动
    systemctl stop firewalld.service 关闭防火墙
    systemctl start smb.service  开启服务
    setenforce 0  关闭setlinux