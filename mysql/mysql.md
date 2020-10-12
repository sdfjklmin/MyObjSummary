#### 
[官网](https://www.mysql.com/) 
[中文官网](https://www.mysql.com/cn/) 
[社区下载](https://dev.mysql.com/downloads/)

#### 安装与启动
    #如果对应的源没有安装包,可以尝试更新 yum update, apt update, apt-get update
    
    #yum
    yum -y install mysql-server 
    yum install mysql mysql-server mysql-devl -y
    
    #apt
    sudo apt install mysql-server
    
    #安装mariadb
    yum install mariadb-server mariadb  -y
    
    #服务
    systemctl start [mariadb|mysql]     #启动
    systemctl stop [mariadb|mysql]      #停止
    systemctl restart [mariadb|mysql]   #重启
    systemctl enable [mariadb|mysql]    #设置开机启动 
    
    #安裝最新
    1.在 `社区链接`(https://dev.mysql.com/downloads/) 下载 对应[Yum|APT|SuSE|...]的资源软件包.
    2.安装下载好的软件包 
        sudo dpkg -i mysql-apt-config_0.8.10-1_all.deb (apt)
        yum localinstall  mysql-apt-config_0.8.10-1_all.rpm (yum)
    3.更新包信息 
        suo apt update
        yum update
    4.安装 
        yum -y install mysql-server
        sudo apt install mysql-server 
        
    #其它错误
    1.如果一直无法启动并且报错
        查看配置 /etc/my.cnf(具体看安装时的配置) 对应写入的文件夹是否有权限.
        查看对应 log 是否有文件或者权限设置,一般为 /var/log/mysql/error.log
        对应写入的地方是否空间不足,磁盘是否满了等
        
#### 默认配置
    #debian(安装,配置,虚拟机文件)
    #默认安装的配置,初始化账号和密码都在此文件中
    sudo cat /etc/mysql/debian.cnf

#### 密码与用户
    0.常规
        #刷新权限
        flush privileges;
        
        #user表的主键
        "username"@"host"
        
    1.修改密码
        use mysql;
        #密码字段: authentication_string(5.7) 或者 Password(5.6),具体看版本,可以先使用 show columns from mysql.user;
        update mysql.user set authentication_string=password('yourpassword') where user='root' and Host ='localhost';
        
    2.新建用户
        create user "username"@"host" identified by "password";
        create user "test"@"localhost" identified by "1234567";
         
    3.更改用户名
        rename user 'test'@'localhost' to 'jim'@'localhost';    
        
    4.删除用户
        drop user 'username'@'host';
        drop user 'test'@'localhost';
            
    5.授权
        grant privileges on databasename.tablename to 'username'@'host' [identified by 'password'] [with grant option];    
        grant all privileges on *.* to 'test'@'localhost' identified by '123';
        
        privileges: 
                        权限列表。all privileges(所有权限)、SELECT、CREATE、DROP等。
        on: 
                        表示这些权限对哪些数据库和表生效，格式：数据库名.表名，这里写“*”表示所有数据库，所有表。
                        如果要指定将权限应用到test库的user表中，可以这么写：test.user
        to: 
                        将权限授予哪个用户。格式：”用户名”@”登录IP或域名”。
                        %表示没有限制，在任何主机都可以登录。
                        比如：”test”@”192.168.0.%”，表示 test 这个用户只能在192.168.0IP段登录
        identified by :
                        指定用户的登录密码,可以省略
        with grant option :
                        表示允许用户将自己的权限授权给其它用户,可以省略
                        
        可以使用GRANT给用户添加权限， 权限会自动叠加，不会覆盖之前授予的权限，
        比如你先给用户添加一个SELECT权限，后来又给用户添加了一个INSERT权限，那么该用户就同时拥有了SELECT和INSERT权限。
        
    6.显示权限
        #如果记不住授权命令,可以使用它先查看,再模仿    
        show grants;
        
        #单个用户权限
        show grants for 'test'@'localhost';
        
    7.删除权限
        revoke privileges on databasename.tablename from 'username'@'host';
        revoke all privileges on *.* from 'test'@'localhost';
        
    8.重启
        
        
#### 主从配置

##### 注意事项
    主从设备的MySQL版本、硬件配置要一致，否则可能会导致一系列问题，
    如从库来不及消费主库的日志，会引起从库日志文件堆积等

##### 操作思路:
      配置 log-bin 和 server-id；
      建立同步帐号（master），并授权；
      根据 master 的状态，配置 slave 同步；
      启动 slave 的复制功能；
      查看主从复制状态slave IO和SQL的running都为yes，即为配置成功；
      测试。
      
 ###### A. 主服务器操作:192.168.5.245
     1.服务器的mysql配置文件
        my.ini(安装版本不同配置文件有可能不同 /etc/my.cnf)
        添加如下配置
          [mysqld]
          log-bin=mysql-bin    //[不是必须]启用二进制日志
          server-id=245        //[必须]服务器唯一ID，默认是1，一般取IP最后一段   
           
     2.重启服务器的mysql
        /etc/init.d/mysql restart     
        systemctl restart mariadb (CentOS7)
         
     3.在主服务器上建立帐户并授权slave  
        mysql> GRANT REPLICATION SLAVE ON *.* to 'mysync'@'%' identified by 'q123456';
              #GRANT REPLICATION SLAVE ON *.* to 'mysync'@'192.168.5.345' identified by 'q123456';
              #指定IP
        mysql> FLUSH PRIVILEGES;
        注意：
            同步帐号一般不用root；
            在实际工作中，只能授权单个IP，不能是通配符的形式授权；
            如果有多个ip，就每个ip单独执行一遍授权语句
            
     4.在主服务器上查看mysql状态
        执行完此步骤后不要再操作主服务器MYSQL
        防止主服务器状态值变化
        mysql> show master status;
        +------------------+----------+--------------+------------------+
        | File        | Position | Binlog_Do_DB | Binlog_Ignore_DB |
        +------------------+----------+--------------+------------------+
        | mysql-bin.000001 |  476 |          |          |
        +------------------+----------+--------------+------------------+
        1 row in set (0.00 sec)  
        
###### B. 从服务器操作:192.168.5.47    
      1.服务器的mysql配置文件
        my.ini(安装版本不同配置文件有可能不同 /etc/my.cnf)
        添加如下配置
            [mysqld]
            log-bin=mysql-bin    //[不是必须]启用二进制日志
            server-id=47        //[必须]服务器唯一ID，默认是1，一般取IP最后一段    
            
      2.配置从服务器 //对应的log信息要和主服务器的master相对应
            mysql> change master to master_host='192.168.5.245',master_user='mysync',master_password='q123456', master_log_file='mysql-bin.000001',master_log_pos=476;      
            mysql> start slave;  //启动复制功能  stop slave
            
      3.查看主从服务器复制功能状态
          mysql> show slave status\G
          *************************** 1. row ***************************
                Slave_IO_State: Waiting for master to send event
                Master_Host: 10.86.87.254        //主服务器地址
                Master_User: mysync        //授权帐户名，尽量避免使用root
                Master_Port: 3306          //数据库端口，部分版本没有此行
                Connect_Retry: 60            
              Master_Log_File: mysql-bin.000001
            Read_Master_Log_Pos: 476          //同步读取二进制日志的位置，大于等于Exec_Master_Log_Pos
                Relay_Log_File: mysqld-relay-bin.000002
                Relay_Log_Pos: 251
            Relay_Master_Log_File: mysql-bin.000001
              Slave_IO_Running: Yes          //此状态必须YES
              Slave_SQL_Running: Yes          //此状态必须YES
          ...

          注：Slave_IO及Slave_SQL进程必须正常运行，即YES状态，否则都是错误的状态(如：其中一个NO均属错误)。
          主从服务器配置完成。 
          
###### C. 测试主服务器Mysql  #进行数据库操作  
      mysql> create database test_db;

###### D. 删除主从配置
        #从库
        mysql>slave stop;
        mysql>reset slave;
        mysql>change master to master_user='', master_host='', master_password='';
        结果报错如下：
        ERROR 1210 (HY000): Incorrect arguments to MASTER_HOST
         
        解决办法如下：
        mysql>change master to master_host=' ';
        即可成功删除同步用户信息。
        注意：上面的命令报错的原因，为master_host=' ' 里面必须有内容，即使为空，也应该用空格代替，而不能什么都不写。
        
###### E. 其它
      可能出错的原因,用户权限,对应日志记录
      show master status\G;  主服务
      show slave status\G;  从服务
         如果主服务删除了从服务中没有的表,slave就会出错,如下解决
         主:show master status\G; 
             *************************** 1. row ***************************
              File: mysql-log.000007
              Position: 289
              Binlog_Do_DB:
              Binlog_Ignore_DB:
          从:
            change master to master_host='192.168.5.245',master_user='root',master_password='123456ss', master_log_file='mysql-log.000007',master_log_pos=289;     
            master_log_file 对应master的File   master_log_pos 对应master的Position
               
###### mysqld相关配置
    [mysqld] 
    # 服务器的ID,必须唯一，一般设置自己的IP
    server_id=205
    # 复制过滤：不需要备份的数据库（MySQL库一般不同步）
    binlog-ignore-db=mysql
    # 开启二进制日志功能，名字可以随便取，最好有含义（比如项目名）
    log-bin=edu-mysql-bin
    # 为每个 session 分配的内存,在事务过程中用来存储二进制日志的缓存
    binlog_cache_size=1M
    # 主从复制的格式(mixed,statement,row,默认格式是 statement)
    binlog_format=mixed
    # 二进制日志自动删除/过期的天数。默认值为 0,表示不自动删除。
    expire_logs_days=7
    ## 跳过主从复制中遇到的所有错误或指定类型的错误,避免 slave 端复制中断。 
    ## 如:1062 错误是指一些主键重复,1032 错误是因为主从数据库数据不一致
    slave_skip_errors=1062
    # 作为从服务器时的中继日志
    relay_log=edu-mysql-relay-bin
    # log_slave_updates 表示 slave 将复制事件写进自己的二进制日志
    log_slave_updates=1
    # 主键自增规则，避免主从同步ID重复的问题
    auto_increment_increment=2  # 自增因子（每次加2）
    auto_increment_offset=1     # 自增偏移（从1开始），单数


###### Other:
    show variables ; #查看所有状态  ->  show variables like '%t%' ;
    show grants ; #查看所有权限
    show grants for root@localhost ; #查看root的权限
    insert into mysql.user(Host,User,Password) values("localhost","test",password("1234")); #新增用户
    flush privileges ; #刷新权限
    grant select on passport.*  to 'test'@'localhost' ; # 赋予select权限在passport数据库的所有表给 test 用户
    grant select on *.* to 'admin'@'%' ;
    revoke select on *.*  from test@localhost ; #删除test对应所有数据库的select权限
    
    #命令帮助
    help profile ;
    
    #查看进程列表
    show processlist ;   
    
    #是否开启profile
    show variables like '%pro%' ;
    
    #设置参数
    set profiling = 1 ;
    
    #sql分析 
    show profile ;
    
    #sql分析
    show profiles ;
    
    #根据上面的查询ID 分析详细内容
    show profile all for query 44 ;
    
#### 主主配置
	#大体和主从配置差不多
	
	1.双方数据库配置文件
        在[mysqld]中添加
        log-bin = my-log
        server-id = 236
        systemctl restart mariadb ;		#重启

	2.设置
        这里直接用的root进行同步,不建议这样	(创建个新用户赋予权限即可)
        对应的 log_file 和 log_pos 根据主机的 master 状态来
        show master status;
        主1(192.168.5.235)	
            change master to master_host='192.168.5.236',master_user='root',master_password='123456', master_log_file='mysql-bin.000001',master_log_pos=476;    
        主2(192.168.5.236)
            change master to master_host='192.168.5.235',master_user='root',master_password='123456', master_log_file='mysql-bin.000001',master_log_pos=476;    
        双方的id要设置对应的权限
            start slave;
            show slave status\G;
        状态如下:
            Slave_IO_Running: Yes
            Slave_SQL_Running: Yes

