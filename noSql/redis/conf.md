#### 安装
    安装必须包:
        yum install gcc
    下载:	
        wget http://download.redis.io/releases/redis-3.0.0.tar.gz
        tar zxvf redis-3.0.0.tar.gz
        cd redis-3.0.0
    编译:	
        #如果不加参数,linux下会报错
        make MALLOC=libc
        
        #简单编译
        make
    简单命令:
        #启动redis
            src/redis-server &
            
        #关闭redis
            src/redis-cli shutdown	
    测试:
            src/redis-cli
            127.0.0.1:6379> set foo bar
            OK
            127.0.0.1:6379> get foo
            "bar"	
            
#### 主从模式
    Redis早期用于保证数据可靠性的一种简单方式。具体来说，Master可用于写、读，而Slave一般只用于读。
    其实在配置上相当简单，只需要在Slave节点配置下Master的IP、PORT、密码即可。
###### Master
```
daemonize yes　　 #在后台启动
port 6379　　     #端口
bind 127.0.0.1　　#绑定IP
loglevel notice　                #记录日记的级别
logfile /data0/redis/redis.log　 #日志文件
dir /data0/redis　　  #数据存储目录
requirepass 123456　　#设置密码
```
###### Slave
```
daemonize yes   #在后台启动
port 6380       #端口
bind 127.0.0.1      #绑定IP
loglevel notice     #记录日记的级别
logfile /data0/redis/redis.log      #日志文件
dir /data0/redis    #数据存储目录
requirepass 123456  #设置密码
#Master信息
slaveof 172.16.59.180 6379  #主服务器IP和地址,这里没有 : 
masterauth 123456   #主服务器配置的密码	
```
###### 复制步骤和原理
~~~
建立连接,数据同步,命令传播
slaveof 127.0.0.1 6333 -> slae 6333 -> (
保存主节点信息,建立socket连接,发送ping命令,权限验证,同步数据集,命令持续复制
) -> master 6379
~~~
###### Error
* WARNING overcommit_memory is set to 0! Background save may fail under low memory condition. To fix this issue add 'vm.overcommit_memory = 1' to /etc/sysctl.conf and then reboot or run the command 'sysctl vm.overcommit_memory=1' for this to take effect.    
~~~
内核参数 overcommit_memory 可选值 0 ,1 ,2
    0, 表示内核将检查是否有足够的可用内存供应用进程使用；如果有足够的可用内存，内存申请允许；否则，内存申请失败，并把错误返回给应用进程。
    1, 表示内核允许分配所有的物理内存，而不管当前的内存状态如何。
    2, 表示内核允许分配超过所有物理内存和交换空间总和的内存
将 vm.overcommit_memory 设为1 即可
way1 : 编辑/etc/sysctl.conf ，改vm.overcommit_memory=1，然后sysctl -p 使配置文件生效
way2 : ysctl vm.overcommit_memory=1
way3 : echo 1 > /proc/sys/vm/overcommit_memory
~~~