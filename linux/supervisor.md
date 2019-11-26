#### 安装 supervisord
    sudo apt-get install supervisord

#### 默认路径
    /etc/supervisor

#### supervisor.sock 地址
    /run/supervisor.sock
    
#### 创建一个conf，　demo.conf
    具体的放置地址，请查看　supervisord.conf
###### 内容
```
[program:MGToastServer] ;程序名称，终端控制时需要的标识
command=dotnet MGToastServer.dll ; 运行程序的命令
directory=/root/文档/toastServer/ ; 命令执行的目录
autorestart=true ; 程序意外退出是否自动重启
stderr_logfile=/var/log/MGToastServer.err.log ; 错误日志文件
stdout_logfile=/var/log/MGToastServer.out.log ; 输出日志文件
environment=ASPNETCORE_ENVIRONMENT=Production ; 进程环境变量
user=root ; 进程执行的用户身份
stopsignal=INT
```

###### nginx实例
```
[program:Home_Nginx] ;程序名称，终端控制时需要的标识
command=/home/sjm/php-lib/version/nginx-1.16/sbin/nginx    ;运行程序的命令
autorestart=true ; 程序意外退出是否自动重启
stderr_logfile=/var/log/Home_Nginx.err.log ; 错误日志文件
stdout_logfile=/var/log/Home_Nginx.out.log ; 输出日志文件
environment=ASPNETCORE_ENVIRONMENT=Production ; 进程环境变量
user=root ; 进程执行的用户身份
stopsignal=INT
```        

####　运行
    #多次运行可能会重复，提前删除之前的sock
    sudo rm /run/supervisor.sock 
    
    #运行配置
    supervisord -c /etc/supervisor/supervisord.conf
    
    #查看
    ps -ef | grep MGToastServer
    
    #如果服务已启动，修改配置文件可用此命令来使其生效
    supervisorctl reload
    
    #进入控制台
    sudo supervisorctl
    #获取帮助信息
    supervisor> help
    
    #其它
    supervisord : 启动supervisor
    supervisorctl reload :修改完配置文件后重新启动supervisor
    supervisorctl status :查看supervisor监管的进程状态
    supervisorctl start 进程名 ：启动XXX进程
    supervisorctl stop 进程名 ：停止XXX进程
    supervisorctl stop all：停止全部进程，注：start、restart、stop都不会载入最新的配置文件。
    supervisorctl update：根据最新的配置文件，启动新配置或有改动的进程，配置没有改动的进程不会受影响而重启
    
#### 开机脚本(/usr/lib/systemd/system/supervisord.service)
```
# dservice for systemd (CentOS 7.0+)
# by ET-CS (https://github.com/ET-CS)
[Unit]
Description=Supervisor daemon

[Service]
Type=forking
ExecStart=/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
ExecStop=/usr/bin/supervisorctl shutdown
ExecReload=/usr/bin/supervisorctl reload
KillMode=process
Restart=on-failure
RestartSec=42s

[Install]
WantedBy=multi-user.target

```    

#### 开机自启
    systemctl enable supervisord

#### 是否已开机自启动
    systemctl is-enabled supervisord

