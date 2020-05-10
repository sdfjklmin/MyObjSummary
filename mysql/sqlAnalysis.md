### sql 分析
#### explain extended + show warnings
    explain extended：会在 explain  的基础上额外提供一些查询优化的信息。
    紧随其后通过 show warnings 命令可以 得到优化后的查询语句，从而看出优化器优化了什么。
    额外还有 filtered 列，是一个半分比的值，rows * filtered/100 可以估算出将要和 explain 中前一个表进行连接的行数
    （前一个表指 explain 中的id值比当前表id值小的表）。    
```
mysql> show columns from admin;
+------------+-----------+------+-----+-------------------+----------------+
| Field      | Type      | Null | Key | Default           | Extra          |
+------------+-----------+------+-----+-------------------+----------------+
| id         | int(11)   | NO   | PRI | NULL              | auto_increment |
| email      | char(30)  | NO   | UNI | NULL              |                |
| pwd        | char(60)  | NO   |     | NULL              |                |
| token      | char(60)  | NO   |     |                   |                |
| id_char    | char(35)  | NO   | UNI |                   |                |
| create_at  | timestamp | NO   |     | CURRENT_TIMESTAMP |                |
| updated_at | timestamp | YES  |     | NULL              |                |
+------------+-----------+------+-----+-------------------+----------------+
7 rows in set (0.01 sec)

mysql> explain extended select id,email from admin;
+----+-------------+-------+-------+---------------+----------+---------+------+------+----------+-------------+
| id | select_type | table | type  | possible_keys | key      | key_len | ref  | rows | filtered | Extra       |
+----+-------------+-------+-------+---------------+----------+---------+------+------+----------+-------------+
|  1 | SIMPLE      | admin | index | NULL          | admin_pk | 120     | NULL |    1 |   100.00 | Using index |
+----+-------------+-------+-------+---------------+----------+---------+------+------+----------+-------------+
1 row in set, 1 warning (0.00 sec)

mysql> show warnings;
+-------+------+---------------------------------------------------------------------------------------------------------------+
| Level | Code | Message                                                                                                       |
+-------+------+---------------------------------------------------------------------------------------------------------------+
| Note  | 1003 | /* select#1 */ select `hyperf`.`admin`.`id` AS `id`,`hyperf`.`admin`.`email` AS `email` from `hyperf`.`admin` |
+-------+------+---------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)
```

#### explain partitions
    相比 explain 多了个 partitions 字段，如果查询是基于分区表的话，会显示查询将访问的分区。

#### [explain sql](https://dev.mysql.com/doc/refman/5.7/en/explain-output.html)
    在 select 语句之前增加 explain 关键字，
    MySQL 会在查询上设置一个标记，执行查询时，会返回执行计划的信息，而不是执行这条SQL
    （如果 from 中包含子查询，仍会执行该子查询，将结果放入临时表中）


##### [參考](https://www.cnblogs.com/butterfly100/archive/2018/01/15/8287569.html)


##### id列
    id列的编号是 select 的序列号，有几个 select 就有几个id，
    并且id的顺序是按 select 出现的顺序增长的
    
##### select_type 
* simple: 简单查询,查询不包含子查询和union
* primary: 复杂查询中最外层的 select
* subquery: 包含在 select 中的子查询（不在 from 子句中）
* derived: 包含在 from 子句中的子查询。
    MySQL会将结果存放在一个临时表中，也称为派生表(derived的英文含义)
* union：在 union 中的第二个和随后的 select
* union result: 从 union 临时表检索结果的 select

##### table
~~~
显示这一行的数据是关于哪张表的
~~~

##### type
~~~
显示连接类型(the join type),它描述了找到所需数据使用的扫描方式(由快到慢)
system  const  eq_ref   ref   range  index   all
系统     常量   等值参考   参考  范围    索引树   全部

ref:reference(re,fen,ce)[參考]
system > const > eq_ref > ref > fulltext > ref_or_null > index_merge > unique_subquery > index_subquery > range > index > ALL
~~~

* NULL `mysql能够在优化阶段分解查询语句，在执行阶段用不着再访问表或索引`
~~~
explain select min(id) from table;
在索引列中选取最小值，可以单独查找索引来完成，不需要在执行时访问表
~~~

* system    `系统表，少量数据，往往不需要进行磁盘IO；`
~~~
#从mysql的系统表中获取 time_zone ,数据已经加载到内存里,不需要进行磁盘IO
explain select * from mysql.time_zone;

#pkId 为 const, tmp 类型为 system
#mysql能对查询的某部分进行优化并将其转化成一个常量
explain select * from ( select * from user where pkId=1 ) tmp;
~~~

* const     `常量连接`
~~~
mysql能对查询的某部分进行优化并将其转化成一个常量
1.命中主键(primary key)或者唯一(unique)索引
2.被连接的部分是一个常量(const)值,pkId = 1
tip.不要类型转换
~~~

* eq_ref    `主键索引(primary key)或者非空唯一索引(unique not null)等值扫描`
~~~
对于前表的每一行(row)，后表只有一行被扫描
1.join查询；
2.命中主键(primary key)或者非空唯一(unique not null)索引；
3.等值连接；
~~~

* ref       `非主键非唯一索引等值扫描`
~~~
1.普通索引
2.对于前表的每一行(row)，后表可能有多于一行的数据被扫描
3.常量的连接查询，也由const降级为了ref，因为也可能有多于一行的数据被扫描
~~~

* ref_or_null `类似ref，但是可以搜索值为NULL的行。`

* index_merge `表示使用了索引合并的优化方法`
~~~
例如：id是主键，tenant_id是普通索引。or 的时候没有用 primary key，而是使用了 primary key(id) 和 tenant_id 索引
explain select * from role where id = 11011 or tenant_id = 8888;
~~~

* range     `范围扫描`
~~~
sql语句 between in, in, >, < 等
~~~

* index     `索引树扫描`
~~~
需要扫描索引上的全部数据
explain select COUNT(*) from user;
~~~
* ALL       `全表扫描(full table scan)`
	
##### possible_keys
~~~
显示可能应用在这张表中的索引。如果为空，没有可能的索引。可以为相关的域从WHERE语句中选择一个合适的语句
~~~

##### key
~~~
实际使用的索引。如果为NULL，则没有使用索引。很少的情况下，MYSQL会选择优化不足的索引。这种情况下，可以在SELECT语句中使用USE INDEX（indexname）来强制使用一个索引或者用IGNORE INDEX（indexname）来强制MYSQL忽略索引
~~~

##### key_len
~~~
使用的索引的长度。在不损失精确性的情况下，长度越短越好
字符串
    char(n)：n字节长度
    varchar(n)：2字节存储字符串长度，如果是utf-8，则长度 3n + 2
数值类型
    tinyint：1字节
    smallint：2字节
    int：4字节
    bigint：8字节　　
时间类型　
    date：3字节
    timestamp：4字节
    datetime：8字节
如果字段允许为 NULL，需要1字节记录是否为 NULL
索引最大长度是768字节，当字符串过长时，mysql会做一个类似左前缀索引的处理，
将前半部分的字符提取出来做索引。
~~~

##### ref
~~~
显示索引的哪一列被使用了，如果可能的话，是一个常数
~~~

##### rows
~~~
MYSQL认为必须检查的用来返回请求数据的行数
~~~

##### extra ： 解析查询的额外信息
* distinct
~~~
一旦mysql找到了与行相联合匹配的行，就不再搜索了
~~~
* Using where
~~~
SQL使用了where条件过滤数据,可以配合 type 优化,若 type 为 all,仍然需要优化
~~~
* Using index
~~~
SQL所需要返回的所有列数据均在一棵索引树上，而无需访问实际的行记录,即数据在子节点上,命中索引
~~~
* Using index condition
~~~
确实命中了索引，但不是所有的列数据都在索引树上，还需要访问实际的行记录,命中普通索引
~~~
* Using filesort
~~~
得到所需结果集，需要对所有记录进行文件排序。这类SQL语句性能极差，需要进行优化,
在一个没有建立索引的列上进行了order by，就会触发filesort,按需添加索引即可
~~~
* Using temporary
~~~
需要建立临时表(temporary table)来暂存中间结果。这类SQL语句性能较低，往往也需要进行优化。
典型的，group by和order by同时存在，且作用于不同的字段时，就会建立临时表，以便计算出最终的结果集
~~~
* Using join buffer (Block Nested Loop)
~~~
需要进行嵌套循环计算,内层和外层的type均为ALL，rows均为4，需要循环进行4*4次计算。
这类SQL语句性能往往也较低，需要进行优化。
典型的，两个关联表join，关联字段均未建立索引，就会出现这种情况。
常见的优化方案是，在关联字段上添加索引，避免每次嵌套循环计算。
~~~

####  profile
	1.使用之前先查看当前数据库的版本信息,低版本无法使用.
		show version();  或者 show variables like '%version%'
	2.查看profiling
		show variables like '%profil%'	;

		result:
			+------------------------+-------+  
			| Variable_name          | Value |  
			+------------------------+-------+  
			| have_profiling         | YES   |   --只读变量，用于控制是否由系统变量开启或禁用profiling  
			| profiling              | OFF   |   --开启SQL语句剖析功能  
			| profiling_history_size | 15    |   --设置保留profiling的数目，缺省为15，范围为0至100，为0时将禁用p

		show profiles; 查看是否开启,效果同上.
	3.查看使用说明 
		help profile;
	4.开启profile
		set profiling=1; 赋值时候不要有多余的空格.
	5.运行sql,查看对应的profile
		select * from test ;
		show profiles;			

		result:
		+----------+------------+--------------------------------------------------------------------------------------------------------------------------+
		| Query_ID | Duration   | Query                                                                                                                    |
		+----------+------------+--------------------------------------------------------------------------------------------------------------------------+
		|       28 | 0.00033575 | select * from test                                                                                        |
		+----------+------------+--------------------------------------------------------------------------------------------------------------------------+

		分析sql性能,分析的时候可以加上对应的开销字段
		show profile [cpu,io][all] for query 28 ;

		show profile for query 28 ;

		+----------------------+----------+
		| Status               | Duration |
		+----------------------+----------+
		| starting             | 5.7E-5   |
		| checking permissions | 7E-6     |
		| Opening tables       | 1.7E-5   |
		| init                 | 2.3E-5   |
		| System lock          | 8E-6     |
		| optimizing           | 5E-6     |
		| statistics           | 1.1E-5   |
		| preparing            | 9E-6     |
		| executing            | 3E-6     |
		| Sending data         | 8.8E-5   |
		| end                  | 5E-6     |
		| query end            | 6E-6     |
		| closing tables       | 5E-6     |
		| freeing items        | 7.8E-5   |
		| cleaning up          | 1.5E-5   |
		+----------------------+----------+
	6.关闭
		set profiling=off;	
### 优化
* 1.“列类型”与“where值类型”不符，不能命中索引，会导致全表扫描
~~~
table tt ( name[pk]] varchar(30))
value: 1,2
sql:
    select * from tt where name = '1';   #命中PK,type为const,获取单条
    select * from tt where name = 1;     #命中索引,type为index,扫描全表
~~~

* 2.join的两个表的字符编码不同，不能命中索引，会导致笛卡尔积的循环计算（nested loop）
~~~
table tt1 charset=utf8
table tt2 charset=latinl
join
~~~
### 搜索方案
#### 优化等级
* 普通 LIKE, 尽量命中索引
* 全文索引, 尽量避免与 CURD 冲突, 5.6以后 innodb 已经支持,(只支持英文检索), 5.8支持中文
* 开源外置索引(ES)
* 自研搜索引擎
    * 短文类 (DAT,...)
    
#### 网站搜索
    
    (站长)                              (用户)  
      |                                   |          
      V                                   V  
    (gen-web)                         (search-item) 
      |1                           a/                ^ 
      V                 (send data)                    \ d
    (spider)  -2-> (web)             \b  (index)  -c->  rank
                     3\ (build data) /4  
    
    （1）全网搜索引擎系统由spider， search&index， rank三个子系统构成
    （2）站内搜索引擎与全网搜索引擎的差异在于，少了一个spider子系统
    （3）spider和search&index系统是两个工程系统，rank系统的优化却需要长时间的调优和积累
    （4）正排索引（forward index）是由网页url_id快速找到分词后网页内容list<item>的过程
    （5）倒排索引（inverted index）是由分词item快速寻找包含这个分词的网页list<url_id>的过程
    （6）用户检索的过程，是先分词，再找到每个item对应的list<url_id>，最后进行集合求交集的过程
    （7）有序集合求交集的方法有
             a）二重for循环法，时间复杂度O(n*n)
             b）拉链法，时间复杂度O(n)
             c）水平分桶，多线程并行
             d）bitmap，大大提高运算并行度，时间复杂度O(n)
             e）跳表，时间复杂度为O(log(n))
#### 简单设计
    假设网站 属性,类别等都分非常多
    test_1(id,uid,name,ext)
    1 21 手机  {"type":"苹果","money":"6500"}{"type":"小米","money":"1988"}
    2 23 工作  {"ways":"厨师","money":"6500"}{"ways":"烹饪","money":"1988"}
    如果一个类目下数据非常多,那么 ext 数据必然会持续增加.
    
    将 ext 中的数据key抽离出来,固定表
    test_2(id,t1_id,key,name,verify)
    1 1 type  类型     int
    2 2 ways  工作类型  string
    3 1 money 金额     int
    
    test_1 数据
    1 21 手机 {"1":"苹果","3":"6500"} 
    
    
    
### 连接池
~~~
为数据库连接建立一个“缓冲池”，预先在池中放入一定数量的数据库连接管道，需要时
从池子中取出管道进行使用，操作完毕后，在将管道放入池子中，
从而避免了频繁的向数据库申请资源，释放资源带来的性能损耗。
~~~
#### 常用的连接池
~~~
数据库连接池有C3P0，DBCP，Druid等等
~~~
#### 迷你设计
~~~
                     (应用系统)
                         |
(request) --             V
(request)   |  ->   (数据库连接池)   <-- (外部配置文件,控制连接池初始化,重置等操作)
(request) --             |   
                         |
            (数据库操作管道1,数据库操作管道2,...)
                         |
                         V
                      (MySql)        
第一，数据库连接池中存放的就是数据库操作管道，不仅仅是存放，而且应该是管理这些管道；
第二，应该提供外部配置文件去初始化数据库连接池；
第三，如果一个数据库操作管道已经被占用，那么其他请求是否应该得到这个管道，也就是说我们要考虑多线程并发下，管道的分配问题；
第四，如果做到管道的复用？放回池子中，标示可用，并不是真正的关闭管道；
第五，demo这里暂不提供 :)
~~~
