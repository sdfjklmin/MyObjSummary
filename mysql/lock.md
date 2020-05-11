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
    目前主要针对与InnoDB,行锁的机制是根据索引来的.
    索引分为聚簇索引和非聚簇索引.
    聚簇索引(主键索引加锁)
    非聚簇索引(普通索引加锁,主键索引加锁)
    范围更新加锁(内部执行:获取满足条数的第一条数据->返回并加锁->更新记录->更新成功->重复)
##### 行锁的模式
###### 共享锁 `读写锁->读锁`
    读锁，又称共享锁（Share locks，简称 S 锁），加了读锁的记录，
    所有的事务都可以读取，但是不能修改，
    并且可同时有多个事务对记录加读锁。
                    ts1
                     ↓
     ts2  →   [(one row data),SL]     ←   ts3
                   (ts0) 
###### 排他锁 `读写锁->写锁`
    写锁，又称排他锁（Exclusive locks，简称 X 锁），或独占锁，
    对记录加了排他锁之后，只有拥有该锁的事务可以读取和修改，
    其他事务都不可以读取和修改，并且同一时间只能有一个事务加写锁。
                    ts1
                     ↓
                     ↑
                 我只属于ts0    
     ts2  →←   [(one row data),XL]     →←   ts3
                   (ts0) 
###### 意向锁(intention:意向)
    由于表锁和行锁虽然锁定范围不同，但是会相互冲突。
    所以当你要加表锁时，势必要先遍历该表的所有记录，判断是否加有排他锁。
    这种遍历检查的方式显然是一种低效的方式，MySQL 引入了意向锁，来检测表锁和行锁的冲突。
    
    意向锁也是表级锁，也可分为读意向锁（IS 锁）和写意向锁（IX 锁）。
    当事务要在记录上加上读锁或写锁时，要首先在表上加上意向锁。
    这样判断表中是否有记录加锁就很简单了，只要看下表上是否有意向锁就行了。
    
    意向锁之间是不会产生冲突的，也不和 AUTO_INC 表锁冲突，它只会阻塞表级读锁或表级写锁，
    另外，意向锁也不会和行锁冲突，行锁只会和行锁冲突。
###### 自增锁(auto increment)
    AUTOINC 锁又叫自增锁（一般简写成 AI 锁），是一种表锁，当表中有自增列（AUTOINCREMENT）时出现。
    当插入表中有自增列时，数据库需要自动生成自增值，它会先为该表加 AUTOINC 表锁，阻塞其他事务的插入操作，这样保证生成的自增值肯定是唯一的。
    AUTOINC 锁具有如下特点：
        AUTO_INC 锁互不兼容，也就是说同一张表同时只允许有一个自增锁；
        自增值一旦分配了就会 +1，如果事务回滚，自增值也不会减回去，所以自增值可能会出现中断的情况。
    显然，AUTOINC 表锁会导致并发插入的效率降低，为了提高插入的并发性，
    MySQL 从 5.1.22 版本开始，引入了一种可选的轻量级锁（mutex）机制来代替 AUTOINC 锁，
    可以通过参数 innodbautoinclockmode 来灵活控制分配自增值时的并发策略。
###### 总结
    意向锁之间互不冲突；
    S 锁只和 S/IS 锁兼容，和其他锁都冲突；
    X 锁和其他所有锁都冲突；
    AI 锁只和意向锁兼容；
##### 行锁的类型
    根据锁的粒度可以把锁细分为表锁和行锁，行锁根据场景的不同又可以进一步细分，
    依次为 Next-Key Lock，Gap Lock 间隙锁，Record Lock 记录锁和插入意向 GAP 锁。
    
    不同的锁锁定的位置是不同的，
    比如说记录锁只锁住对应的记录，
    而间隙锁锁住记录和记录之间的间隔，
    Next-Key Lock 则所属记录和记录之前的间隙。
    不同类型锁的锁定范围大致如下
                          Next-Key Lock
                     ↑                   ↑ 
                     (Gap Lock 1)         (Gap Lock 2)         (Gap Lock 3)    
    (-∞)     (Key=3)              (Key=10)            (Key=20)             (+∞)
                                  ↑      ↑            ↑      ↑
                               Record Lock 1        Record Lock 2  
###### 记录锁(Record Lock)
    记录锁是最简单的行锁。
    InnoDB 加锁原理中的锁就是记录锁，只锁住 id = 1 或者 field = 'value' 这一条记录。
    
    当 SQL 语句无法使用索引时，会进行全表扫描，这个时候 MySQL 会给整张表的所有数据行加记录锁，
    再由 MySQL Server 层进行过滤。但是，在 MySQL Server 层进行过滤的时候，如果发现不满足 WHERE 条件，会释放对应记录的锁。
    这样做，保证了最后只会持有满足条件记录上的锁，但是每条记录的加锁操作还是不能省略的。
    
    所以更新操作必须要根据索引进行操作，没有索引时，不仅会消耗大量的锁资源，增加数据库的开销，还会极大的降低了数据库的并发性能。
###### 间隙锁
    如果 Key = 17 这条记录不存在，这个 SQL 语句还会加锁吗？
    在 RC 隔离级别不会加任何锁，在 RR 隔离级别会在 Key = 17 前后两个索引之间加上间隙锁。

    间隙锁是一种加在两个索引之间的锁，或者加在第一个索引之前，或最后一个索引之后的间隙。
    这个间隙可以跨一个索引记录，多个索引记录，甚至是空的。
    使用间隙锁可以防止其他事务在这个范围内插入或修改记录，保证两次读取这个范围内的记录不会变，从而不会出现幻读现象。

    值得注意的是，间隙锁和间隙锁之间是互不冲突的，间隙锁唯一的作用就是为了防止其他事务的插入，所以加间隙 S 锁和加间隙 X 锁没有任何区别。
###### Next-Key 锁
    Next-key锁是记录锁和间隙锁的组合，它指的是加在某条记录以及这条记录前面间隙上的锁
    参考 Key = 10 上的 Next-Key Lock,
    (-∞,3],(3,10],(10,20],(20,+∞)
    通常用这种左开右闭区间来表示 Next-key 锁，其中，圆括号表示不包含该记录，方括号表示包含该记录
    RR级别下才有
###### 插入意向锁
    插入意向锁是一种特殊的间隙锁（简写成 II GAP）表示插入的意向，只有在 INSERT 的时候才会有这个锁。
    注意，这个锁虽然也叫意向锁，但是和上面介绍的表级意向锁是两个完全不同的概念，不要搞混了。
    
    插入意向锁和插入意向锁之间互不冲突，所以可以在同一个间隙中有多个事务同时插入不同索引的记录。
    譬如，id = 30 和 id = 49 之间如果有两个事务要同时分别插入 id = 32 和 id = 33 是没问题的，
    虽然两个事务都会在 id = 30 和 id = 50 之间加上插入意向锁，但是不会冲突。
    
    插入意向锁只会和间隙锁或 Next-key 锁冲突，
    正如上面所说，间隙锁唯一的作用就是防止其他事务插入记录造成幻读，
    正是由于在执行 INSERT 语句时需要加插入意向锁，而插入意向锁和间隙锁冲突，从而阻止了插入操作的执行。
###### 总结
| \          |record  |gap     |next-key|ii gap|
| :------:   | :-----:  | :----: | :----:| :---:|
| record     |          |  兼容  |       | 兼容  |
| gap        | 兼容     |   兼容  | 兼容  |  兼容 |
| next-key   |         |   兼容  |       |  兼容 |
| ii gap     | 兼容     |        |       |      |
    其中，第一行表示已有的锁，第一列表示要加的锁。
    插入意向锁较为特殊，所以我们先对插入意向锁做个总结，
    如下：
        插入意向锁不影响其他事务加其他任何锁。也就是说，一个事务已经获取了插入意向锁，对其他事务是没有任何影响的；
        插入意向锁与间隙锁和 Next-key 锁冲突。也就是说，一个事务想要获取插入意向锁，如果有其他事务已经加了间隙锁或 Next-key 锁，则会阻塞。
    其他类型的锁的规则较为简单：
        间隙锁不和其他锁（不包括插入意向锁）冲突；
        记录锁和记录锁冲突，Next-key 锁和 Next-key 锁冲突，记录锁和 Next-key 锁冲突；
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