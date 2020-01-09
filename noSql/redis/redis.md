## 网站
[官网](https://redis.io/commands)
[中文网](http://www.redis.cn/) 
[中文网2](https://www.redis.net.cn/) 
[redisdoc文档](http://redisdoc.com) 
[redisfans文档](http://doc.redisfans.com/)
 
#### Redis为什么这么快
###### 纯内存操作
    避免大量访问数据库,减少直接读取磁盘数据,
    redis 将数据储存在内存里面,读写数据的时候都不会受到硬盘 I/O 速度的限制,所以速度快.
    
###### 单线程操作
    避免了不必要的上下文切换和竞争条件,也不存在多进程或者多线程导致的切换而消耗 CPU,
    不用去考虑各种锁的问题,不存在加锁释放锁操作,没有因为可能出现死锁而导致的性能消耗；
    
###### 采用了非阻塞I/O多路复用机制
    I/O多路复用 : 每个进程/线程同时处理 多个连接(I/O多路复用)
    redis利用epoll来实现IO多路复用,将连接信息和事件放到队列中,依次放到文件事件分派器,事件分派器将事件分发给事件处理器.
    Nginx也是采用IO多路复用原理解决C10K问题.
    
    c10k：https://www.jianshu.com/p/ba7fa25d3590
    
    用户A-Z   ->   I/O多路复用(s0,s1,s2,s3)  -依次放到-> 文件事件分派器 -> 事件处理器(连接应答处理器1,连接应答处理器1...)
    
* 简单说下epoll(基于Linux)
~~~
只对发生变化的文件句柄感兴趣,工作机制类似于"事件"
通过 epoll_ctl 注册文件描述符fd,一旦该fd就绪,
内核就会采用类似 callback 的回调机制来激活该fd, 
epoll_wait 便可以收到通知, 并通知应用程序.
~~~
###### 灵活多样的数据结构
    见详情

###### 持久化
    RDB(快照) ： 将redis在内存中的数据库记录定时 dump到磁盘上的RDB持久化,如果redis宕机会丢失未满足条件之前的数据.
        配置: save 60 1000  #60秒内有至少有1000个键被改动时,自动保存一次数据集
        RDB 恢复数据集的速度也要比 AOF 恢复的速度要快
    AOF(Append-only file) : 将redis的操作日志以追加的方式写入文件
        配置: appendonly yes #每次操作后,都会追加到文件中
    Redis 4.0 混合持久化: 为了解决 RDB数据不全,AOF恢复慢(RDB+AOF)
    
###### 缓存淘汰策略(解决数据热点问题)
    当 Redis 内存超出物理内存限制时,内存的数据会开始和磁盘产生频繁的交换 (swap).交换会让 Redis 的性能急剧下降,对于访问量比较频繁的 Redis 来说,
    这样龟速的存取效率基本上等于不可用.
    在生产环境中我们是不允许 Redis 出现交换行为的,为了限制最大使用内存,Redis 提供了配置参数 maxmemory 来限制内存超出期望大小.
    当实际内存超出 maxmemory 时,Redis 提供了几种可选策略 (maxmemory-policy) 来让用户自己决定该如何腾出新的空间以继续提供读写服务

* noeviction：
~~~
不会继续服务写请求 (DEL 请求可以继续服务),读请求可以继续进行。这样可以保证不会丢失数据,但是会让线上的业务不能持续进行。这是默认的淘汰策略。
~~~
    
* volatile-lru：
~~~
尝试淘汰设置了过期时间的 key,最少使用的 key 优先被淘汰。没有设置过期时间的 key 不会被淘汰,这样可以保证需要持久化的数据不会突然丢失。
~~~
    
* volatile-ttl：
~~~
跟上面一样,除了淘汰的策略不是 LRU,而是 key 的剩余寿命 ttl 的值,ttl 越小越优先被淘汰。
~~~
    
* volatile-random：
~~~
跟上面一样,不过淘汰的 key 是过期 key 集合中随机的 key。
~~~
    
* allkeys-lru：
~~~
区别于 volatile-lru,这个策略要淘汰的 key 对象是全体的 key 集合,而不只是过期的 key 集合。这意味着没有设置过期时间的 key 也会被淘汰。
allkeys-random跟上面一样,不过淘汰的策略是随机的 key。
~~~
    
* volatile-xxx ：
~~~
策略只会针对带过期时间的 key 进行淘汰,allkeys-xxx 策略会对所有的 key 进行淘汰。如果你只是拿 Redis 做缓存,那应该使用 allkeys-xxx,客户端写缓存时不必携带过期时间。如果你还想同时使用 Redis 的持久化功能,那就使用 volatile-xxx 策略,这样可以保留没有设置过期时间的 key,它们是永久的 key 不会被 LRU 算法淘汰。
~~~
    
###### 核心原理概述
    因为它所有的数据都在内存中,所有的运算都是内存级别的运算(纳秒),单线程避免了多线程的切换(上下文切换,各种锁)性能耗损问题.
    对于耗时的指令(比如keys),一定要谨慎使用,一不小心就可能会导致 Redis 卡顿.
    通过I/O复用来处理多并发客户端连接(单线程如何处理那么多的并发客户端连接？)
    通过持久化来保证数据的完整性
    通过缓存淘汰策略(解决数据热点问题),保证实时性

#### 基本数据类型
###### String
    Redis中最基本,也是最简单的数据类型.
	注意,VALUE既可以是简单的String,也可以是复杂的String,如JSON,在实际中常常利用fastjson将对象序列化后存储到Redis中.另外注意mget批量获取可以提高效率.

###### Hash
    Hash结构适用于存储对象,相较于String,存储占用更少的内存.Hash结构可以使你像在数据库中Update一个属性一样只修改某一项属性值,而且还可以快速定位数据.比如,如果我们把表User中的数据可以这样放置到Redis中：Hash存储,KEY：User,Field:USERID,VALUE：user序列化后的string.

###### List
    有序元素的序列,既可以当做栈、又可以当做队列.实际上,可以利用List的先进先出或者先进后出的特性维护一段列表,比如排行榜、实时列表等,甚至还可以简单的当做消息队列来使用.
```
命令: rpush mylist 1 23 4 56 7 89 , 返回: 6 
结构:
row value
1   1
2   23
3   4
.   ...
```
###### Set
    Set是String类型的不重复无序集合.Set的特点在于,它提供了集合的一些运算,比如交集、并集、差集等.这些运算特性,非常方便的解决实际场景中的一些问题,如共同关注、共同粉丝等.

###### ZSet
    ZSet就是SortedSet.实际中,很多排序场景都可以考虑ZSet来做.

#### Redis发展过程中的三种模式：
    主从、哨兵、集群
    Redis的发展可以从版本的变化看出来,从1.X的主从模式,到2.X的哨兵模式,再到今天3.X的集群模式,可以说这些都是Redis保证数据可靠性、高可用的思路.

#### Redis持久化:把Redis内存中的数据同步到磁盘中来保证持久化
    Snapshotting（快照）也是默认方式
    Append-only file（缩写aof）的方式
    虚拟内存
    diskstore
    
#### 常用命令
| 命令 | 说明 |    
| :---: | :---: |    
| select index | 选择对应的db,index为(0-15)|
| flushdb | 清空当前db内容		|
| flushall | 清空所有db内容		|
| keys * | 查看当前db所有的key	|
| ---String | 操作--- |
| get key | 获取|
| SET key value [EX seconds] [PX milliseconds] [NX或XX] | 设置|

####参考文献
* [Redis单线程为何速度如此之快](https://blog.csdn.net/wangwenru6688/article/details/82467890)		