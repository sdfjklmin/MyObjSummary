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
    |`unlock tables;`#解锁
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
* show status like 'innodb_row_lock%';查看InnoDB_row_lock的状态
* show variables like '%lock_wait_timeout%';

#### Innodb Row Lock
    Innodb_row_lock_current_waits:当前正在等待锁的数量；
    
    Innodb_row_lock_time:从系统启动到现在锁定总时间长度；
    
    Innodb_row_lock_time_avg：每次等待所花平均时间；
    
    Innodb_row_lock_time_max:从系统启动到现在等待最长的一次所花的时间长度；
    
    Innodb_row_lock_waits:系统启动到现在总共等待的次数；
    
    对于这5个状态变量，比较重要的是：
    
    Innodb_row_lock_time_avg，Innodb_row_lock_waits，Innodb_row_lock_time。

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

当有主键或者索引的时候，会形成行锁。
当无法命中索引或者无索引、范围、模糊时，会形成表锁。
明确指定索引，若无查询数据，无锁。

for update 仅适用于InnoDB，并且必须开启事务，在begin与commit之间才生效。
for update锁住表或者锁住行，只允许当前事务进行操作（读写），其他事务被阻塞，直到当前事务提交或者回滚，被阻塞的事务自动执行。
for update nowait 锁住表或者锁住行，只允许当前事务进行操作（读写），其他事务被拒绝，事务占据的statement连接也会被断开。

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

#### 源数据锁(metadata lock) Waiting for table metadata lock
    KILL 掉某些事物占用的锁，使DDL成功，然后进而不阻塞其他DML操作。
    
    设置锁超时短些 lock_wait_timeout
    
    DML（data manipulation language）数据操纵语言：
        就是我们最经常用到的 SELECT、UPDATE、INSERT、DELETE。 主要用来对数据库的数据进行一些操作
        
    DDL（data definition language）数据库定义语言：
        其实就是我们在创建表的时候用到的一些sql，比如说： CREATE、ALTER、DROP、TRUNCATE等。
        DDL主要是用在定义或改变表的结构，数据类型，表之间的链接和约束等初始化工作上。
   
    DQL(Data Query Language) 数据查询语言DQL：
        select 相关
             
    DCL（Data Control Language）数据库控制语言：
        是用来设置或更改数据库用户或角色权限的语句，包括（grant,deny,revoke等）语句。这个比较少用到

#### 锁等待超时，尝试重启事物 Lock wait timeout exceeded; try restarting transaction
    出现死锁应该从锁的源头进行排查，这里需要和业务逻辑相互结合进行排查。
    DB: information_schema(数据库相关信息)
    
    Table:
    innodb_trx 当前运行的所有事务
    innodb_locks 当前出现的锁
    innodb_lock_waits 锁等待的对应关系
    可以使用 Desc 查看表结构。
    
    通过 状态 进行排除，  kill 对应的 死锁。 
    select * from information_schema.innodb_trx;
    
    设置锁的超时时间
    show variables like '%lock_wait_timeout%';

#### 死锁之多事物加锁顺序不一致
|事物A|事物B  |
| :------:   | :-----:  |
| update table set value = 1 where id = 20  | update table set value = 1 where id = 30 | 
| update table set value = 1 where id = 30  | update table set value = 1 where id = 20 |
    
    AB同时执行，A获取 id = 20 的锁，B获取 id = 30 的锁。
    A获取 id = 30 的锁，此时锁在B事物中，需要等待。
    B获取 id = 20 的锁，此时锁在A事物中，需要等待。
    AB同时等待彼此的锁，从而导致死锁。

#### 3.4 如何避免死锁

在工作过程中偶尔会遇到死锁问题，虽然这种问题遇到的概率不大，但每次遇到的时候要想彻底弄懂其原理并找到解决方案却并不容易。其实，对于 MySQL 的 InnoDb 存储引擎来说，死锁问题是避免不了的，没有哪种解决方案可以说完全解决死锁问题，但是我们可以通过一些可控的手段，降低出现死锁的概率。

如上面的案例一和案例三所示，对索引加锁顺序的不一致很可能会导致死锁，所以如果可以，尽量以相同的顺序来访问索引记录和表。在程序以批量方式处理数据的时候，如果事先对数据排序，保证每个线程按固定的顺序来处理记录，也可以大大降低出现死锁的可能；

如上面的案例二所示，Gap 锁往往是程序中导致死锁的真凶，由于默认情况下 MySQL 的隔离级别是 RR，所以如果能确定幻读和不可重复读对应用的影响不大，可以考虑将隔离级别改成 RC，可以避免 Gap 锁导致的死锁；

为表添加合理的索引，如果不走索引将会为表的每一行记录加锁，死锁的概率就会大大增大；

我们知道 MyISAM 只支持表锁，它采用一次封锁技术来保证事务之间不会发生死锁，所以，我们也可以使用同样的思想，在事务中一次锁定所需要的所有资源，减少死锁概率；

避免大事务，尽量将大事务拆成多个小事务来处理；因为大事务占用资源多，耗时长，与其他事务冲突的概率也会变高；

避免在同一时间点运行多个对同一表进行读写的脚本，特别注意加锁且操作数据量比较大的语句；我们经常会有一些定时脚本，避免它们在同一时间点运行；

设置锁等待超时参数：innodb_lock_wait_timeout，这个参数并不是只用来解决死锁问题，在并发访问比较高的情况下，如果大量事务因无法立即获得所需的锁而挂起，会占用大量计算机资源，造成严重性能问题，甚至拖跨数据库。我们通过设置合适的锁等待超时阈值，可以避免这种情况发生。

##### 3.5 业务分析
风险提示：等待行锁

注册会话快照：

|id	| user	|host	|db	|command	|time	|state| info |
| :------:   | :-----:  | :----: | :----:| :---:| :---:| :---:| :---:|
|18077187	|pro_link	|172.27.0.4:38548	|vdsns	|Execute	|449	|updating |DELETE FROM `pyjy_member_third_login` WHERE `member_id` = 353424 |
|18093488	|pro_link	|172.27.0.4:52505	|vdsns	|Execute	|46	    |updating |UPDATE `pyjy_member_account` SET `account` = '930222D967F87C6B32BC01576FE78604' , `token` = 'b3fd932e262dd61acef925edb93c84b2' , `token_expire` = 1614823639 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 37 , `invite_code` = 'f031e00749ab64d4333e606130c6a22e' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093496	|pro_link	|172.27.0.4:52613	|vdsns	|Execute	|46	    |updating |UPDATE `pyjy_member_account` SET `account` = '0783E89A040DF376CA34DF035125BACF' , `token` = 'b3fd932e262dd61acef925edb93c84b2' , `token_expire` = 1614823639 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 53 , `invite_code` = 'f031e00749ab64d4333e606130c6a22e' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093491	|pro_link	|172.27.0.4:52534	|vdsns	|Execute	|46	    |updating |UPDATE `pyjy_member_account` SET `account` = '15138493313' , `token` = 'b3fd932e262dd61acef925edb93c84b2' , `token_expire` = 1614823639 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 43 , `invite_code` = 'f031e00749ab64d4333e606130c6a22e' , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093549	|pro_link	|172.27.0.4:53586	|vdsns	|Execute	|44	    |updating |UPDATE `pyjy_member_account` SET `account` = '19560718036' , `token` = '84164aa936b149b43d8a17314c744f69' , `token_expire` = 1614823641 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 30 , `invite_code` = '9b10dec5a4da46552e20b58e2b1dd429' , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093562	|pro_link	|172.27.0.4:53780	|vdsns	|Execute	|44	    |updating |UPDATE `pyjy_member_account` SET `account` = '17391981239' , `token` = '84164aa936b149b43d8a17314c744f69' , `token_expire` = 1614823641 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 34 , `invite_code` = '9b10dec5a4da46552e20b58e2b1dd429' , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093546	|pro_link	|172.27.0.4:53494	|vdsns	|Execute	|44	    |updating |UPDATE `pyjy_member_account` SET `account` = 'oqYpgtxCfv7kz0VzxQ24pgkx7RJw' , `token` = '84164aa936b149b43d8a17314c744f69' , `token_expire` = 1614823641 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 23 , `invite_code` = '9b10dec5a4da46552e20b58e2b1dd429' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093680	|pro_link	|172.27.0.4:55578	|vdsns	|Execute	|41	    |updating |UPDATE `pyjy_member_account` SET `account` = '17861511276' , `token` = 'f474940c7e6fe6d897ed551d192fe745' , `token_expire` = 1614823644 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 27 , `invite_code` = 'c1b1b794c46c4219a405fe162cc79747' , `register_is_finished` = 0 WHERE `id` = 354754 |
|18093821	|pro_link	|172.27.0.4:57776	|vdsns	|Execute	|39	    |updating |UPDATE `pyjy_member_account` SET `login_date_cnt` = 1 , `free_time` = 1612404446 , `last_login_ip` = 989067844 , `hb_time` = 1612404446 , `machine` = 'HUAWEI FRL-AN00a android 10' WHERE `id` = 354754 |
|18093843	|pro_link	|172.27.0.4:58234	|vdsns	|Execute	|37	    |updating |UPDATE `pyjy_member_account` SET `account` = '15751656669' , `token` = 'bbe2d320f7081efa124176927950d7ca' , `token_expire` = 1614823648 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 32 , `invite_code` = '78d8d45da6a7f20e49b2d6810b3616dd' , `register_is_finished` = 0 WHERE `id` = 354755 |
|18093820	|pro_link	|172.27.0.4:57718	|vdsns	|Execute	|37	    |updating |UPDATE `pyjy_member_account` SET `account` = '412141BA7BB60272460CCCF0D11D3A07' , `token` = 'bbe2d320f7081efa124176927950d7ca' , `token_expire` = 1614823648 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 14 , `invite_code` = '78d8d45da6a7f20e49b2d6810b3616dd' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354755 |
|18094060	|pro_link	|172.27.0.4:33288	|vdsns	|Execute	|33	    |updating |UPDATE `pyjy_member_account` SET `login_date_cnt` = 1 , `free_time` = 1612404452 , `last_login_ip` = 2871237327 , `hb_time` = 1612404452 , `machine` = 'Xiaomi M2011K2C android 11' WHERE `id` = 354755 |
|18094087	|pro_link	|172.27.0.4:33636	|vdsns	|Execute	|31	    |updating |UPDATE `pyjy_member_account` SET `account` = 'oqYpgt31XDLbcdFRXiVP9dztOt-U' , `token` = '4d51a529eecb29d03d0fa494d77e6bd3' , `token_expire` = 1614823654 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 21 , `invite_code` = 'b9bcee0db031514b39cacb8d83452ab0' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354756 |
|18094094	|pro_link	|172.27.0.4:33786	|vdsns	|Execute	|31	    |updating |UPDATE `pyjy_member_account` SET `account` = '15918293696' , `token` = '4d51a529eecb29d03d0fa494d77e6bd3' , `token_expire` = 1614823654 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 51 , `invite_code` = 'b9bcee0db031514b39cacb8d83452ab0' , `register_is_finished` = 0 WHERE `id` = 354756 |
|18094147	|pro_link	|172.27.0.4:35052	|vdsns	|Execute	|31	    |updating |UPDATE `pyjy_member_account` SET `login_date_cnt` = 1 , `free_time` = 1612404454 , `last_login_ip` = 1699913819 , `hb_time` = 1612404454 , `machine` = 'Redmi M2006J10C android 10' WHERE `id` = 354755 |
|18094117	|pro_link	|172.27.0.4:34122	|vdsns	|Execute	|30	    |updating |UPDATE `pyjy_member_account` SET `account` = 'oqYpgtzv7Q8nFRJzTIJrMWJIbXss' , `token` = 'ff912be664f352cfe162e830894908a1' , `token_expire` = 1614823655 , `prepare_state` = 3 , `nickname` = '' , `avatar` = '' , `sign` = '' , `address_distance` = 61 , `invite_code` = '65c95a40e39461e2f16e89e5f797265c' , `have_phone` = 0 , `register_is_finished` = 0 WHERE `id` = 354756 |

分析：等待行锁
>354754 出现了 8 次，均为 `update`，每次更新的 `account` 都不相同。
> 
>注册逻辑为账号分配+事物。当并发量比较高的时候，单毫秒随机分配可能对应多个用户，`find one`、`get one`、`one` 等都会失效。
> 
>比如： 用户ID `354754` 对应了 `8` 个用户。由于注册逻辑中包含了事物，导致多事物同时操作一条数据，
> `sql 语句` 可能会触发 `锁` 相关的问题，导致 `cup` 处理变慢，从而导致卡顿，甚至宕机。

分析： Gap Lock 
> `pyjy_member_third_login`
> 
> column：`member_id(pk)`，`open_id`
> 
> 由于 `member_id` 来源于 `pyjy_member_account -> id`，相对随机。
> 
> 当出于事物时，会触发 `Gap Lock` 。