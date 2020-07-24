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

* c10k(一瞬间1w请求)
~~~
早期的一个TCP请求,就会创建一个进程(或线程),而进程属于操作系统,是非常昂贵的资源,并且有数量限制.
创建的进程线程多了,数据拷贝频繁（缓存I/O、内核将数据拷贝到用户进程空间、阻塞）, 进程/线程上下文切换消耗大, 导致操作系统崩溃,这就是C10K问题的本质！
~~~  
  
* 简单说下epoll(基于Linux)
~~~
支持一个进程打开大数目的socket描述符(fd)
只对发生变化的文件句柄感兴趣,工作机制类似于"事件"
通过 epoll_create 创建 epoll对象,
 linux内核会创建一个eventpoll结构体(
 重要的两个成员:
    红黑树的根节点: 这棵树中存储着所有添加到epoll中的事件，也就是这个epoll监控的事件。
    双向链表rdllist: 保存着将要通过epoll_wait返回给用户的、满足条件的事件。  
)
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
| src/redis-server [path] |  启动redis,path为对应的redis.conf,可省.更多 -h|
| src/redis-cli |  快速进入redis命令行,具体可以 -h|
| src/redis-cli shutdown	|  关闭redis|
| cli> auth 123456 | 如果有密码,操作前需要认证权限 |
| cli> config get requirepass | 获取当前密码 |
| cli> config set requirepass 123456| 设置当前密码为 123456,一般不使用,可修改配置 |
| vi redis.conf -> `requirepass abcd`| 取消注释,设置密码 abcd,保存退出并重启 |
| select index | 选择对应的db,index为(0-15)|
| flushdb | 清空当前db内容		|
| flushall | 清空所有db内容		|
| keys * | 查看当前db所有的key	|
| ---String | 操作--- |
| GET key | 获取|
| SET key value [EX seconds] [PX milliseconds] [NX或XX] | 设置|

#### 其它
##### `./redis-server --help` 启动帮助
    #启动,加载默认配置
    ./redis-server (run the server with default conf)
    
    #启动,指定加载配置,如果有时候修改配置文件不生效,可以指定配置文件试试
    #这里是绝对地址哦
    ./redis-server /etc/redis/6379.conf
    
    #指定端口
    ./redis-server --port 7777
    
    #指定端口,和slave地址
    ./redis-server --port 7777 --slaveof 127.0.0.1 8888
    
    #指定配置和loglevel
    ./redis-server /etc/myredis.conf --loglevel verbose

##### info（可以一次性获取所有的信息，也可以按块获取信息）
    server:服务器运行的环境参数
    clients:客户端相关信息
    memory：服务器运行内存统计数据
    persistence：持久化信息
    stats：通用统计数据
    Replication：主从复制相关信息
    CPU：CPU使用情况
    cluster：集群信息
    Keypass：键值对统计数量信息
    
    eg:
    ./redis-cli info 按块获取信息 | grep 需要过滤的参数
    ./redis-cli info stats | grep ops
    
    或
    
    ./redis-cli 
    > info server
    
    #每秒操作数
    ./redis-cli info | grep ops
    
    #内存监控
    ./redis-cli info | grep used | grep human       
    used_memory_human:2.99M         # 内存分配器从操作系统分配的内存总量
    used_memory_rss_human:8.04M     #操作系统看到的内存占用，top命令看到的内存
    used_memory_peak_human:7.77M    # redis内存消耗的峰值
    used_memory_lua_human:37.00K    # lua脚本引擎占用的内存大小
    
    #由于最大内存限制被移除的key的数量
    ./redis-cli info | grep evicted_keys
    
    #内存碎片率
    ./redis-cli info | grep mem_fragmentation_ratio
    
    #已使用内存
    ./redis-cli info | grep used_memory
    
    #连接数,如果 connected_clients 很大，则意味着服务器的最大连接数设置得过低，需要调整maxclients
    ./redis-cli info | grep connected
    
    #最后一次持久化保存磁盘的时间戳
    ./redis-cli info | grep rdb_last_save_time
    
    #自最后一次持久化以来数据库的更改数
    ./redis-cli info | grep rdb_changes_since_last_save
    
    #key值查找失败(没有命中)次数，出现多次可能是被hei ke gong ji
    ./redis-cli info | grep keyspace
    
    #主从断开的持续时间（以秒为单位)
    ./redis-cli info | grep rdb_changes_since_last_save
    
    #复制积压缓冲区如果设置得太小，会导致里面的指令被覆盖掉找不到偏移量，从而触发全量同步
    ./redis-cli info | grep backlog_size
    
    #通过查看sync_partial_err变量的次数来决定是否需要扩大积压缓冲区，它表示主从半同步复制失败的次数
    ./redis-cli info | grep sync_partial_err
    
    #性能测试, 100个连接，5000次请求对应的性能。
    ./redis-benchmark -c 100 -n 5000

        
#### 参考文献
* [Redis单线程为何速度如此之快](https://blog.csdn.net/wangwenru6688/article/details/82467890)		

#### 与 memcache 的区别
* 复杂数据结构 : value是哈希，列表，集合，有序集合这类复杂的数据结构时，会选择redis，因为mc无法满足这些需求。
* mc无法满足持久化的需求，只得选择redis。
* redis天然支持集群功能，可以实现主动复制，读写分离。而memcache，要想要实现高可用，需要进行二次开发
* memcache的value存储，最大为1M，如果存储的value很大，只能使用redis。
* 纯KV，数据量非常大，并发量非常大的业务，使用memcache或许更适合。

#### mc与redis的底层实现机制差异说起。
##### 内存分配
    memcache使用预分配内存池的方式管理内存，能够省去内存分配时间。
    redis则是临时申请空间，可能导致碎片。
    从这一点上，mc会更快一些。
##### 虚拟内存使用
    memcache把所有的数据存储在物理内存里。
    redis有自己的VM机制，理论上能够存储比物理内存更多的数据，当数据超量时，会引发swap，把冷数据刷到磁盘上。
    从这一点上，数据量大时，mc会更快一些。
##### 网络模型
    memcache使用非阻塞IO复用模型，redis也是使用非阻塞IO复用模型。
    但由于redis还提供一些非KV存储之外的排序，聚合功能，在执行这些功能时，复杂的CPU计算，会阻塞整个IO调度。
    从这一点上，由于redis提供的功能较多，mc会更快一些。
##### 线程模型
    memcache使用多线程，主线程监听，worker子线程接受请求，执行读写，这个过程中，可能存在锁冲突。
    redis使用单线程，虽无锁冲突，但难以利用多核的特性提升整体吞吐量。
    从这一点上，mc会快一些。
##### 代码可读性，代码质量
    看过mc和redis的代码，从可读性上说，redis是我见过代码最清爽的软件，甚至没有之一，或许简单是redis设计的初衷，编译redis甚至不需要configure，不需要依赖第三方库，一个make就搞定了。
    而memcache，可能是考虑了太多的扩展性，多系统的兼容性，代码不清爽，看起来费劲。
    例如网络IO的部分，redis源码1-2个文件就搞定了，mc使用了libevent，一个fd传过来传过去，又pipe又线程传递的，特别容易把人绕晕。
##### 水平扩展的支持
    不管是mc和redis，服务端集群没有天然支持水平扩展，需要在客户端进行分片，这其实对调用方并不友好。
    如果能服务端集群能够支持水平扩展，会更完美一些。
    
    
#### memcache 
* mc的核心职能是KV内存管理，value存储最大为1M，它不支持复杂数据结构（哈希、列表、集合、有序集合等）；
~~~
业务决定技术方案，mc的诞生，以“以服务的方式，而不是库的方式管理KV内存”为设计目标，
它是，KV内存管理组件库，复杂数据结构与持久化并不是它的初衷
~~~
* mc不支持持久化；
* mc支持key过期；
~~~
内存管理
    chunk：它是将内存分配给用户使用的最小单元。
    item：用户要存储的数据，包含key和value，最终都存储在chunk里。
    slab：它会管理一个固定chunk size的若干个chunk，而mc的内存管理，由若干个slab组成。
    
    slabs[] -> [slab0][slab1][slab2][slab3]
                128    256     512    ... 
    一系列slab，分别管理128B，256B，512B…的chunk内存单元。

    将128B的slab0放大：
       chunk_size       -> 128
       free_chunk_list  ->                        2 -> 3 -> nil
       chunk[]          -> [128B] -> [128B] -> [128B] ...
                            0          1        2 
                            used      used      free
    chunk_size：该slab管理的是128B的chunk
    free_chunk_list：用于快速找到空闲的chunk
    chunk[]：已经预分配好，用于存放用户item数据的实际chunk空间

假如用户要存储一个100B的item，是如何找到对应的可用chunk的呢？
会从最接近item大小的slab的chunk[]中，通过free_chunk_list快速找到对应的chunk，
如上图所示，与item大小最接近的chunk是128B。
为什么不会出现内存碎片呢？
拿到一个128B的chunk，去存储一个100B的item，余下的28B不会再被其他的item所使用，
即：实际上浪费了存储空间，来减少内存碎片，保证访问的速度。


懒淘汰(lazy expiration)。
    item不会被主动淘汰，即没有超时线程，也没有信号通知来主动检查
    item每次会查询(get)时，检查一下时间戳，如果已经过期，被动淘汰，并返回cache miss
 举个例子，假如set了一个key，有效期100s：
    在第50s的时候，有用户查询(get)了这个key，判断未过期，返回对应的value值
    在第200s的时候，又有用户查询(get)了这个key，判断已过期，将item所在的chunk释放，返回cache miss
 这种方式的实现代价很小，消耗资源非常低：
    在item里，加入一个过期时间属性
    在get时，加入一个时间判断
内存总是有限的，chunk数量有限的情况下，能够存储的item个数是有限的，假如chunk被用完了，该怎么办？
仍然是上面的例子，假如128B的chunk都用完了，用户又set了一个100B的item，要不要挤掉已有的item？
要。
这里的启示是：
（1）即使item的有效期设置为“永久”，也可能被淘汰；
（2）如果要做全量数据缓存，一定要仔细评估，cache的内存大小，必须大于，全量数据的总大小，否则很容易采坑；
挤掉哪一个item？怎么挤？
这里涉及LRU淘汰机制。
如果操作系统的内存管理，最常见的淘汰算法是FIFO和LRU：
    FIFO(first in first out)：最先被set的item，最先被淘汰
    LRU(least recently used)：最近最少被使用(get/set)的item，最先被淘汰
使用LRU算法挤掉item，需要增加两个属性：
    最近item访问计数
    最近item访问时间
并增加一个LRU链表，就能够快速实现。
~~~
* mc持续运行很少会出现内存碎片，速度不会随着服务运行时间降低；
~~~
提前分配内存。
~~~
* mc使用非阻塞IO复用网络模型，使用监听线程/工作线程的多线程模型；
~~~
目的是提高吞吐量。
多线程能够充分的利用多核，但会带来一些锁冲突。
~~~
