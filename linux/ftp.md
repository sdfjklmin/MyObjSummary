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

#### 上传 put
    格式：put local-file [remote-file]
    将本地文件上传到服务器home目录下面/home,并改名为2.htm
    ftp> put 1.htm /home/2.htm

#### 下载 get
    格式：get [remote-file] [local-file]
    将服务器文件/home/1.htm下载到本地
    ftp> get /usr/your/1.htm 1.htm，回车


#### FTP 测试
```
ftp 12.34.56.78
Name (000:test): 
    (input you ftp username)
Password:
    (input you ftp username passwd)

put

get
```