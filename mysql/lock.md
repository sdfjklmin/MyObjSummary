### 锁的类型
#### 表锁
    表锁由 MySQL Server 实现，一般在执行 DDL(Data Definition Language) 语句时会对整个表进行加锁，
    比如说 ALTER,UPDATE TABLE 等操作。
    在执行 SQL 语句时，也可以明确指定对某个表进行加锁。
    表锁使用的是一次性锁技术,只能访问加锁的表，不能访问其他表，直到最后通过 unlock tables 释放所有表锁
    |-------
    | read  |
    |---------------------------------------------------------------------------------------------
    |`lock table test read;` #为test表设置读锁[read|write]
    |
    |`select * from test where id = 1;` #成功
    |
    |#失败,Table 'test_2' was not locked with LOCK TABLES,没有读表锁
    |`select * from test_2 where id = 2;`
    |
    |#Table 'test' was locked with a READ lock and can't be updated
    |`update test  set name = 'one' where id = 1;` # 失败，未提前获得test的写表锁
    |
    |`unlock talbes;`#解锁
    |`lock table test_2 read;`#此时会释放 test 的读表锁,或者 start transaction | begin 也会释放之前的锁
    |---------------------------------------------------------------------------------------------
    
    |-------
    | write |
    |---------------------------------------------------------------------------------------------
    |`lock table test write;` #为test表设置读锁[read|write]
    |
    |`select * from test where id = 1;` #成功
    |
    |#失败,Table 'test_2' was not locked with LOCK TABLES,没有读表锁
    |`select * from test_2 where id = 2;`
    |
    |`update test  set name = 'one' where id = 1;` # 成功
    |
    |`unlock talbes;`#解锁
    |---------------------------------------------------------------------------------------------
#### 行锁
#### 页锁
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
#在区间(3,10)插入id=7,需要获取排他间隙锁(防止其它事物也插入id=7),但此时存在 共享间隙锁,所以会阻塞.


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

#### 记录锁(Record Locks) : 锁定索引记录
~~~
记录锁，它封锁索引记录，例如：
select * from t where id=1 for update;
它会在id=1的索引记录上加锁，以阻止其他事务插入，更新，删除id=1的这一行。
需要说明的是：
    select * from t where id=1;
    则是快照读(SnapShot Read)，它并不加锁.
~~~
#### 间隙锁(Gap Locks) : 锁定间隔，防止间隔中被其他事务插入
~~~
它封锁索引记录中的间隔，或者第一条索引记录之前的范围，又或者最后一条索引记录之后的范围。
插入id=10会封锁区间，以阻止其他事务id=10的记录插入。

为什么要阻止id=10的记录插入？
如果能够插入成功，头一个事务执行相同的SQL语句，会发现结果集多出了一条记录，即幻影数据。

间隙锁的主要目的，就是为了防止其他事务在间隔中插入数据，以导致“不可重复读”。
如果把事务的隔离级别降级为读提交(Read Committed, RC)，间隙锁则会自动失效。
~~~

#### 临键锁(Next-Key Locks) : 锁定索引记录+间隔，防止幻读
~~~
临键锁，是记录锁与间隙锁的组合，它的封锁范围，既包含索引记录，又包含索引区间。
更具体的，临键锁会封锁索引记录本身，以及索引记录之前的区间。

如果一个会话占有了索引记录R的共享/排他锁，其他会话不能立刻在R之前的区间插入新的索引记录。
临键锁的主要目的，也是为了避免幻读(Phantom Read)。如果把事务的隔离级别降级为RC，临键锁则也会失效。
 ~~~