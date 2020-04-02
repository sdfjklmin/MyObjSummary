#### 箭头
    ↑ ↓ ← → ↖ ↗ ↙ ↘ ↔ ↕
    ↞ ↟ ↠ ↡ ↢ ↣ ↤ ↥ ↦ ↧ ↨
    ↰ ↱ ↲ ↳ ↴ ↵
    ↶ ↷ ↺ ↻     
    ↼ ↽ ↾ ↿ ⇀ ⇁ ⇂ ⇃
     
#### 基础说明
* CDN (Content Delivery Network): 内容分发网络,
使用户就近获取所需内容，降低网络拥塞，提高用户访问响应速度和命中率

* DNS (Domain Name System): 域名系统（服务）协议

* 高可用HA(High Availability): 通过设计,减少系统不能提供服务的时间

* 高并发HC(High Concurrency): 是互联网分布式系统架构设计中必须考虑的因素之一,
通过设计保证系统能够同时并行处理很多请求。
    * 响应时间：系统对请求做出响应的时间。
        例如系统处理一个HTTP请求需要200ms，这个200ms就是系统的响应时间。
    * 吞吐量(TPS)： Transactions Per-Second 单位时间内处理的请求数量。
    * QPS：Query Per-Second 每秒响应请求数。在互联网领域，这个指标和吞吐量区分的没有这么明显。
    * 并发用户数：同时承载正常使用系统功能的用户数量。
        例如一个即时通讯系统，同时在线量一定程度上代表了系统的并发用户数。
        
        
#### 高并发
* 垂直扩展: 提升单机处理能力
    * 增强单机硬件性能，例如：增加CPU核数如32核，升级更好的网卡如万兆，升级更好的硬盘如SSD，扩充硬盘容量如2T，扩充系统内存如128G；
    * 提升单机架构性能，例如：使用Cache来减少IO次数，使用异步来增加单服务吞吐量，使用无锁数据结构来减少响应时间；
* 水平扩展: 只要增加服务器数量，就能线性扩充系统性能
    * 参见 `高可用`,多个 `nginx,cache,service,db`
#### 高可用
    [普通应用]   
                        test.com
             (client) <----------> (dns-server)             客户端层:典型调用方是浏览器browser或者手机应用APP
                |   ← 123.45.6.78
                ↓      
             (nginx)  反向代理,负载均衡                       反向代理:系统入口，反向代理 
                ↓
           (web server) -> (前端资源静态化) -> (cdn)          web应用:实现核心应用逻辑，返回html或者json
                ↓
            (service)  → (cache)                            服务:实了服务
              /  ↘ write  
        read /     (db-master)       
             ↓      ↙ binlog                               数据库:缓存+数据库
            (db-slave)     
        
    [client -> nginx]     
                (client)
                   |
                ip ↓
                (nginx) ← keepalived+virtual IP →  (nginx)  (shadow-nginx)   
                        keepalived存活探测，相同virtual IP提供服务   
                   nginx挂了的时候，keepalived能够探测到，会自动的进行故障转移，将流量自动迁移到shadow-nginx，
                       由于使用的是相同的virtual IP，这个切换过程对调用方是透明的。
                       
                   
    [反向代理]
                    (nginx) -> nginx.conf
                    ↙     ↘ 
             (server-1) (server-2)  
              1.1.1.2    1.1.1.3
                 当 server-1 挂了后,会将流量转移至 server-2
                 
    [web应用 -> 服务层]  
                        (web-server)
                       (connect pool)
                        ↙     ↘     rpc或其它方式 
                 (server-1)  (server-2)  
                192.186.0.1  192.186.0.2
             当 server-1 挂了后,会转移至 server-2     
             
    [服务层 -> cache] 
          [一般]        
                    (service)
                     ↙    ↘ 
                  (cache) (cache)
                            ↓miss
                          (db) 
                           
          [redis-主从]
                            (server)
                         (redis client)
                               ↓ 
                            (redis-m) <--监控---(redis-sentinel集群,检测存活状态)
                                同步↘        ↙监控
                                    (redis-s)
                                
                            (server)
                         (redis client)
             直接访问redis-s \       ↖通知client访问redis-s
                (redis-m挂了) <--监控---(redis-sentinel集群,检测存活状态)
                              \         /
                               ↘     ↙监控
                              (redis-s)
                            
                            
    [服务层 -> 数据库-分库]
                (db pool)
                 |        ↘  write
                 |       (db-master)------
                 |                       |   
                 |--read-->(db-slave) <--|
                read                     |
                 ↓                       | 
                (db-salve)   <-----------|
                
    [数据库-分表]
                    (db)
                ↙    ↓    ↘ 
         (user-1) (user-2)  (user-3)
                
#### [Yii2](https://www.yiichina.com/doc/guide/2.0/start-workflow)
##### 静态结构
                (入口脚本)
                    ↑
                (应用主体) ←  (应用组件)
         ↶    ↗
       (模块)       ↑ 
             ↖
                (控制器)   ←  (过滤器)
                ↗      ↖ 
             (视图) ←  (模型)
           ↗      ↖
      (小部件)  ← (前端资源包)
      
##### 生命周期
![Yii生命周期](yii-request-lifecycle.png)
1.用户向入口脚本 web/index.php 发起请求。

2.入口脚本加载应用配置并创建一个应用 实例去处理请求。

3.应用通过请求组件解析请求的 路由。

4.应用创建一个控制器实例去处理请求。

5.控制器创建一个动作实例并针对操作执行过滤器。

6.如果任何一个过滤器返回失败，则动作取消。

7.如果所有过滤器都通过，动作将被执行。

8.动作会加载一个数据模型，或许是来自数据库。

9.动作会渲染一个视图，把数据模型提供给它。

10.渲染结果返回给响应组件。

11.响应组件发送渲染结果给用户浏览器。