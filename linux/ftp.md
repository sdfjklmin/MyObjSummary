#### 安装服务 `vsftpd`
    #apt
    sudo apt install vsftpd

    #yum
    yum install vsftpd
    
    #其它方式

#### 启动
    #服务
    vsftpd.service
    
    service vsftpd start
    
    service vsftpd stop
    
    service vsftpd restart
     
    systemctl start vsftpd.service
    
#### 添加用户
    #手动创建对应的用户目录
    #用户 ftptest
    mkdir /home/ftptest
    chmod -R 777 /home/ftptest

    #新增用户 指定对应的 home 文件
    sudo useradd -d /home/ftptest  -s /bin/bash ftptest
    sudo passwd ftptest
    # 输入密码并确认
    
#### 测试 `ftp://ip:21`

#### 配置
    #默认配置
	vi /etc/vsftpd/vsftpd.conf  
	
	#查看
	whereis vsftpd