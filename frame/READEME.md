#### 箭头
    ↑ ↓ ← → ↖ ↗ ↙ ↘ ↔ ↕ ➻ ➼ ➽ ➸ ➳ ➺ ➻ ➴ ➵ ➶ ➷ ➹
     
#### 基础说明
* CDN (Content Delivery Network): 内容分发网络,
使用户就近获取所需内容，降低网络拥塞，提高用户访问响应速度和命中率

* DNS (Domain Name System): 域名系统（服务）协议

* 高可用HA(High Availability): 通过设计,减少系统不能提供服务的时间

#### 常见
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
                            
                            
    [服务层 -> 数据库]
                (db pool)
                 |        ↘  write
                 |       (db-master)------
                 |                       |   
                 |--read-->(db-slave) <--|
                read                     |
                 ↓                       | 
                (db-salve)   <-----------|