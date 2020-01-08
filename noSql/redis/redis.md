## 网站
[官网](https://redis.io/commands)
[中文网](http://www.redis.cn/) 
[中文网2](https://www.redis.net.cn/) 
[redisdoc文档](http://redisdoc.com) 
[redisfans文档](http://doc.redisfans.com/)
 
#### Redis为什么这么快
###### 纯内存操作
    避免大量访问数据库，减少直接读取磁盘数据，
    redis 将数据储存在内存里面，读写数据的时候都不会受到硬盘 I/O 速度的限制，所以速度快。
    
###### 单线程操作
    避免了不必要的上下文切换和竞争条件，也不存在多进程或者多线程导致的切换而消耗 CPU，
    不用去考虑各种锁的问题，不存在加锁释放锁操作，没有因为可能出现死锁而导致的性能消耗；
    
###### 采用了非阻塞I/O多路复用机制

###### 灵活多样的数据结构

###### 持久化
    RDB(快照) ： 将redis在内存中的数据库记录定时 dump到磁盘上的RDB持久化
    AOF(Append-only file) : 将redis的操作日志以追加的方式写入文件

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