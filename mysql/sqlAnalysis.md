### sql 分析

#### explain sql

##### table
~~~
显示这一行的数据是关于哪张表的
~~~

##### type
~~~
显示连接类型(the join type),它描述了找到所需数据使用的扫描方式(由快到慢)
系统, 常量, 等值参考, 参考, 范围, 索引树, 全部
~~~

* system    `系统表，少量数据，往往不需要进行磁盘IO；`
~~~
#从mysql的系统表中获取 time_zone ,数据已经加载到内存里,不需要进行磁盘IO
explain select * from mysql.time_zone;

#pkId 为 const, tmp 类型为 system
explain select * from ( select * from user where pkId=1 ) tmp;
~~~

* const     `常量连接`
~~~
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

### 锁的排查
* 并发事务，间隙锁可能互斥
    * A删除不存在的记录，获取共享间隙锁；
    * B插入，必须获得排他间隙锁，故互斥；
* 并发插入相同记录，可能死锁(某一个回滚)
* 并发插入，可能出现间隙锁死锁(难排查)
* show engine innodb status; 可以查看InnoDB的锁情况，也可以调试死锁
#### 死锁
* 复现
* 查看事务与锁信息
* 分析sql
~~~
    # 设置事物隔离级别 RR
    # show variables like "%isolation%";
    # read uncommitted, read committed, repeatable read, serializable     
    set session transaction isolation level repeatable read;
    
    # 关闭自动提交
    # show variables like "autocommit";
    set session autocommit=0;
    
    #开始事物
    start transaction;
    #sql
    commit;
    
    #查看事务与锁信息
    show engine innodb status;

    #分析sql,死锁:1.没走行锁,索引失效,全表扫描
    explain sql;
~~~

#### 间隙锁 互斥(删除?)
~~~
#设置rr
set session transaction isolation level repeatable read;

#表信息
create table t (
id int(10) primary key
)engine=innodb;

#初始化数据
start transaction;
insert into t values(1);
insert into t values(3);
insert into t values(10);
commit;

#开启区间锁，RR的隔离级别下，上例会有四个区间
(-infinity, 1)
(1, 3)
(3, 10)
(10, infinity)

#实例1
set session autocommit=0;
start transaction;
delete from t where id=5;

#实例2
set session autocommit=0;
start transaction;
insert into t values(0);
insert into t values(2);
insert into t values(12);
insert into t values(7);

#结果
#实例1 删除某个区间内的一条不存在记录，获取到共享间隙锁，
#会阻止其他事务 实例2 在相应的区间插入数据，因为插入需要获取排他间隙锁
#实例2 插入的值：0, 2, 12都不在(3, 10)区间内，能够成功插入，而7在(3, 10)这个区间内，会阻塞。
#+++
#删除 id=5,5在区间(3,10)触发共享间隙锁,事物未提交,
#在区间(3,10)插入id=7,需要获取排他间隙锁,但此时存在 共享间隙锁,所以会阻塞.


#验证 {{{ lock_mode }}}
show engine innodb status;

*** (1) TRANSACTION:
TRANSACTION 788058, ACTIVE 28 sec starting index read
mysql tables in use 1, locked 1
LOCK WAIT 3 lock struct(s), heap size 360, 5 row lock(s), undo log entries 4
MySQL thread id 358, OS thread handle 0x7f733c67f700, query id 9079 localhost 127.0.0.1 root updating
/* ApplicationName=DataGrip 2019.2.3 */ DELETE FROM `tt`.`t` WHERE `id` = 1
*** (1) WAITING FOR THIS LOCK TO BE GRANTED:
RECORD LOCKS space id 261 page no 3 n bits 80 index `PRIMARY` of table `tt`.`t` trx id 788058 {{{ lock_mode }}} X locks rec but not gap waiting
Record lock, heap no 4 PHYSICAL RECORD: n_fields 3; compact format; info bits 0
0: len 4; hex 80000001; asc     ;;
1: len 6; hex 0000000c0640; asc      @;;
2: len 7; hex fb000001fc0144; asc       D;;

#正在等待共享间隙锁的释放。
#insert into t values(7);

#如果 事务1 提交或者回滚， 事务2 就能够获得相应的锁，以继续执行。
#如果 事务2 一直不提交， 事务2 会一直等待，直到超时，超时后会显示：
#ERROR 1205 (HY000): Lock wait timeout exceeded; try restarting transaction
~~~

#### 共享排他锁死锁(插入?)
~~~
#表同 间隙锁互斥
# A
set session autocommit=0;
start transaction;
insert into t values(7);

# B
set session autocommit=0;
start transaction;
insert into t values(7);

# C
set session autocommit=0;
start transaction;
insert into t values(7);

#结果:
# 1.A先执行，插入成功，并获取id=7的排他锁；
# 2.B后执行，需要进行PK校验，故需要先获取id=7的共享锁，阻塞；
# 3.C后执行，也需要进行PK校验，也要先获取id=7的共享锁，也阻塞；
# 如果此时，session A执行：
#   rollback;
# id=7排他锁释放。
# 则B，C会继续进行主键校验：
#   (1)B会获取到id=7共享锁，主键未互斥；
#   (2)C也会获取到id=7共享锁，主键未互斥；
# B和C要想插入成功，必须获得id=7的排他锁，但由于双方都已经获取到id=7的共享锁，
# 它们都无法获取到彼此的排他锁，死锁就出现了。
# 当然，InnoDB有死锁检测机制，B和C中的一个事务会插入成功，另一个事务会自动放弃：
# ERROR 1213 (40001): Deadlock found when trying to get lock; try restarting transaction
~~~

#### 并发间隙锁的死锁(插入,删除?)
~~~
#共享排他锁，在并发量插入相同记录的情况下会出现，相应的案例比较容易分析。
#而并发的间隙锁死锁，是比较难定位的。
#两个并发的session，其SQL执行序列如下：
A：set session autocommit=0;
A：start transaction;
A：delete from t where id=6;
         B：set session autocommit=0;
         B：start transaction;
         B：delete from t where id=7;
A：insert into t values(5);
         B：insert into t values(8);
#A执行delete后，会获得(3, 10)的共享间隙锁。
#B执行delete后，也会获得(3, 10)的共享间隙锁。
#A执行insert后，希望获得(3, 10)的排他间隙锁，于是会阻塞。
#B执行insert后，也希望获得(3, 10)的排他间隙锁，于是死锁出现。

~~~