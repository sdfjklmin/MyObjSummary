## MySQL [社区下载](https://dev.mysql.com/downloads/mysql/)

#### Command
##### 登陆
     mysql -h localhost -u root -p    // mysql -h ('ip地址|localhost') -u ('用户名字,root') -p
     mysql -u root -p

#### SQL 
    组合索引 : alter table {table_name} add index {index_name} ({column},{column});
    
    显示配置：show variables;
    显示某些配置： show variables like '%some_conf%';
    
    #显示配置-连接信息
    show variables like '%connection%';
    
    #显示配置-最大连接数
    show variables like '%max_user_connections%';
    
    显示状态：show status;
    显示某些状态：show status like '%some_conf%';
    
    #显示最大的使用连接
    show  status like '%max_used_connections%';
    
    #查看进程
    show processlist;
    
    #查看所有进程,可以根据具体的响应时间进行排查
    #手动 kill 时间较长的死进程
    show full processlist;
    
    #显示线程状态
    show status like 'Threads%';
    +-------------------+-------+
    | Variable_name     | Value |
    +-------------------+-------+
    | Threads_cached    | 1     |
    | Threads_connected | 1595  |   #打开的连接数
    | Threads_created   | 2     |
    | Threads_running   | 1478  |   #激活的连接数,这个数值一般远低于connected数值  
    +-------------------+-------+
    
    #刷新当前hosts
    flush hosts;
    
    # unix_timestamp() 当前时间戳
    # now()  当前日期
    # unix_timestamp(now()) 当前日期的时间戳
    # select unix_timestamp(), now(), unix_timestamp(now());
    
#### Error
##### Uncaught PDOException: SQLSTATE[HY000] [1130] Host '192.168.1.108' is not allowed to connect to this MariaDB server
	a.外部ip不允许访问,给对应的ip设置访问权限
		grant all privileges on *.* to 'root'@'192.168.5.34' identified by 'passwd';
	b.如果是本机ip,将ip换成localhost
		Uncaught PDOException: SQLSTATE[HY000] [2002] No such file or directory
			出现这个问题的原因是PDO无法找到mysql.sock或者mysqld.sock
			a.把ip改成127.0.0.1
			b.找到相应的.sock文件,并设置php.ini文件中的pdo_mysql.default_socket的值为.sock文件的路径。
				pdo_mysql.default_socket= /tmp/mysqld.sock  
    
##### 忘记root的登陆密码
    警告:首先确认服务器出于安全的状态，也就是没有人能够任意地连接MySQL数据库。 
         因为在重新设置MySQL的root密码的期间，MySQL数据库完全出于没有密码保护的 
         状态下，其他的用户也可以任意地登录和修改MySQL的信息。可以采用将MySQL对 
         外的端口封闭，并且停止Apache以及所有的用户进程的方法实现服务器的准安全 
         状态。最安全的状态是到服务器的Console上面操作，并且拔掉网线。 
    a.修改MySQL的登录设置： 
        # vi /etc/my.cnf 
        在[mysqld]的段中加上一句：skip-grant-tables    // 跳过授予表
        例如： 
        [mysqld] 
        datadir=/var/lib/mysql 
        skip-grant-tables 
        保存并且退出vi。 
    b.重新启动mysqld 
        # /etc/init.d/mysqld restart 
    c.登录并修改MySQL的root密码 
         # /usr/bin/mysql 	
         mysql>USE mysql ;  // 使用数据库
         mysql>UPDATE user SET Password = password ( 'new-password' ) WHERE User = 'root' ;  //更改root的密码
         mysql> flush privileges ;  // 重新加载权限表;更新权限
         mysql> quit  // 退出
     d．将MySQL的登录设置修改回来 
         # vi /etc/my.cnf 
         将刚才在[mysqld]的段中加上的skip-grant-tables删除 
         保存并且退出vi。 
    e．重新启动mysqld 
        # /etc/init.d/mysqld restart 
      

#### Question

##### 1.MySQL的复制原理以及流程
    (1) 主：binlog线程——记录下所有改变了数据库数据的语句，放进master上的binlog中；
    (2) 从：io线程——在使用start slave 之后，负责从master上拉取 binlog 内容，放进 自己的relay log中；
    (3) 从：sql执行线程——执行relay log中的语句；
    
##### 2.MySQL中myisam与innodb的区别
    (1) 不同点；
        1>.InnoDB支持事物，而MyISAM不支持事物
            事务也非常耗性能，会影响吞吐量，建议只对一致性要求较高的业务使用复杂事务
            MyISAM可以通过lock table表锁，来实现类似于事务的东西，但对数据库性能影响较大，强烈不推荐使用
            
        2>.InnoDB可以支持行级锁，而MyISAM只支持表级锁
            MyISAM：执行读写SQL语句时，会对表加锁，所以数据量大，并发量高时，性能会急剧下降。
            InnoDB：细粒度行锁，在数据量大，并发量高时，性能比较优异。锁是通过索引来进行的,如果索引失效那么可能造成表锁.
            说明: 常常说，select+insert的业务用MyISAM，因为MyISAM在文件尾部顺序增加记录速度极快。
                  但是绝大部分业务是混合读写，只要数据量和并发量较大，一律使用InnoDB。
                  
        3>.InnoDB支持MVCC(多版本并发控制), 而MyISAM不支持
        
        4>.InnoDB支持外键，而MyISAM不支持
            不管哪种存储引擎，在数据量大并发量大的情况下，都不应该使用外键，而建议由应用程序保证完整性
            
        5>.InnoDB 5.6之前不支持全文索引，而MyISAM支持。
            数据量大并发量大的情况下，都不应该使用数据库自带的全文索引，
            会导致小量请求占用大量数据库资源
            大数据量+高并发量的业务场景，全文索引，MyISAM也不是最优之选
       
        6>.说明
            在大数据量，高并发量的互联网业务场景下，对于MyISAM和InnoDB
            有where条件，count(*)两个存储引擎性能差不多
            不要使用全文索引，应当使用《索引外置》的设计方案
            事务影响性能，强一致性要求才使用事务
            不用外键，由应用程序来保证完整性
            不命中索引，InnoDB也不能用行锁
            
    (2) innodb引擎的4大特性
        插入缓冲（insert buffer),二次写(double write),自适应哈希索引(ahi),预读(read ahead)
    (3) 2者 select count(*) 哪个更快，为什么
        myisam更快，因为myisam内部维护了一个计数器,直接存储了总行数，可以直接调取。
        innodb需要按行扫描,性能消耗极大,
        当加上 where 条件后,两者处理方式类似
        
##### 3.MySQL中varchar与char的区别以及varchar(50)中的50代表的涵义  
    (1) varchar与char的区别
        char是一种固定长度的类型，varchar则是一种可变长度的类型
    (2) varchar(50)中50的涵义
        最多存放50个字符，varchar(50)和(200)存储hello所占空间一样，但后者在排序时会消耗更多内存，因为order by col采用fixed_length计算col长度(memory引擎也一样)
    (3) int（20）中20的涵义
        是指显示字符的长度zerofill（零填充）
        但要加参数的，最大为255，比如它是记录行数的id,插入10笔资料，它就显示00000000001 ~~~00000000010，
        当字符的位数超过11,它也只显示11位，如果你没有加那个让它未满11位就前面加0的参数，它不会在前面加0
        20表示最大显示宽度为20，但仍占4字节存储，存储范围不变；
    (4) mysql为什么这么设计
        对大多数应用没有意义，只是规定一些工具用来显示字符的个数；int(1)和int(20)存储和计算均一样；
        字节 ： tinyint(1)、smallint(2)、mediumint(3)、int(4)、bigint(8)
        无符号:      8           16         24          32      64
        次方计算:   2   
    (5) [详情查看](data_type.md)

##### 4.Innodb的事务与日志的实现方式
    (1) 有多少种日志
            错误日志：记录出错信息，也记录一些警告信息或者正确的信息。
            查询日志：记录所有对数据库请求的信息，不论这些请求是否得到了正确的执行。
            慢查询日志：设置一个阈值，将运行时间超过该值的所有SQL语句都记录到慢查询的日志文件中。
            二进制日志：记录对数据库执行更改的所有操作。
            中继日志：中继日志也是二进制日志，用来给slave 库恢复
            事务日志：重做日志redo和回滚日志undo
    (2) 事物的4种隔离级别
            隔离级别
            读未提交(RU)
            读已提交(RC)
            可重复读(RR)
            串行
    (3) 事务是如何通过日志来实现的
            事务日志是通过redo和innodb的存储引擎日志缓冲（Innodb log buffer）来实现的，当开始一个事务的时候，会记录该事务的lsn(log sequence number)号;
            当事务执行时，会往InnoDB存储引擎的日志的日志缓存里面插入事务日志；
            当事务提交时，必须将存储引擎的日志缓冲写入磁盘（通过innodb_flush_log_at_trx_commit来控制），也就是写数据前，需要先写日志。这种方式称为“预写日志方式”
           
##### 5.MySQL binlog的几种日志录入格式以及区别
    (1) 每一条会修改数据的sql都会记录在binlog中。
        优点：不需要记录每一行的变化，减少了binlog日志量，节约了IO，提高性能。(相比row能节约多少性能 与日志量，这个取决于应用的SQL情况，正常同一条记录修改或者插入row格式所产生的日志量还小于Statement产生的日志量，但是考虑到如果带条 件的update操作，以及整表删除，alter表等操作，ROW格式会产生大量日志，因此在考虑是否使用ROW格式日志时应该跟据应用的实际情况，其所 产生的日志量会增加多少，以及带来的IO性能问题。)
        缺点：由于记录的只是执行语句，为了这些语句能在slave上正确运行，因此还必须记录每条语句在执行的时候的 一些相关信息，以保证所有语句能在slave得到和在master端执行时候相同 的结果。另外mysql 的复制,像一些特定函数功能，slave可与master上要保持一致会有很多相关问题(如sleep()函数， last_insert_id()，以及user-defined functions(udf)会出现问题).
        使用以下函数的语句也无法被复制：
        * LOAD_FILE()
        * UUID()
        * USER()
        * FOUND_ROWS()
        * SYSDATE() (除非启动时启用了 --sysdate-is-now 选项)
        同时在INSERT ...SELECT 会产生比 RBR 更多的行级锁
    (2) Row:不记录sql语句上下文相关信息，仅保存哪条记录被修改。
        优点： binlog中可以不记录执行的sql语句的上下文相关的信息，仅需要记录那一条记录被修改成什么了。所以rowlevel的日志内容会非常清楚的记录下 每一行数据修改的细节。而且不会出现某些特定情况下的存储过程，或function，以及trigger的调用和触发无法被正确复制的问题
        缺点:所有的执行的语句当记录到日志中的时候，都将以每行记录的修改来记录，这样可能会产生大量的日志内容,比 如一条update语句，修改多条记录，则binlog中每一条修改都会有记录，这样造成binlog日志量会很大，特别是当执行alter table之类的语句的时候，由于表结构修改，每条记录都发生改变，那么该表每一条记录都会记录到日志中。
    (3) Mixedlevel: 是以上两种level的混合使用，一般的语句修改使用statment格式保存binlog，如一些函数，statement无法完成主从复制的操作，则 采用row格式保存binlog,MySQL会根据执行的每一条具体的sql语句来区分对待记录的日志形式，也就是在Statement和Row之间选择 一种.新版本的MySQL中队row level模式也被做了优化，并不是所有的修改都会以row level来记录，像遇到表结构变更的时候就会以statement模式来记录。至于update或者delete等修改数据的语句，还是会记录所有行的 变更。

##### 6.MySQL数据库cpu飙升到500%的话他怎么处理？
    列出所有进程  show [full(可省)] processlist  观察所有进程  多秒没有状态变化的(干掉 kill ID)
    查看超时日志或者错误日志 (做了几年开发,一般会是查询以及大批量的插入会导致cpu与i/o上涨,,,,当然不排除网络状态突然断了,,导致一个请求服务器只接受到一半，比如where子句或分页子句没有发送,,当然的一次被坑经历)

##### 7.SQL优化
    (1) explain出来的各种item的意义；
        select_type 表示查询中每个select子句的类型
        type 表示MySQL在表中找到所需行的方式，又称“访问类型”
        possible_keys 指出MySQL能使用哪个索引在表中找到行，查询涉及到的字段上若存在索引，则该索引将被列出，但不一定被查询使用
        key 显示MySQL在查询中实际使用的索引，若没有使用索引，显示为NULL
        key_len 表示索引中使用的字节数，可通过该列计算查询中使用的索引的长度
        ref 表示上述表的连接匹配条件，即哪些列或常量被用于查找索引列上的值 
        Extra 包含不适合在其他列中显示但十分重要的额外信息
    
    (2) profile的意义以及使用场景；
        查询到 SQL 会执行多少时间, 并看出 CPU/Memory 使用量, 执行过程中 Systemlock, Table lock 花多少时间等等

##### 8.备份计划，mysqldump以及xtranbackup的实现原理
    (1) 备份计划；
        这里每个公司都不一样，您别说那种1小时1全备什么的就行
    (2) 备份恢复时间；
        这里跟机器，尤其是硬盘的速率有关系，以下列举几个仅供参考
        20G的2分钟（mysqldump）
        80G的30分钟(mysqldump)
        111G的30分钟（mysqldump)
        288G的3小时（xtra)
        3T的4小时（xtra)
        逻辑导入时间一般是备份时间的5倍以上
    (3) xtrabackup实现原理
        在InnoDB内部会维护一个redo日志文件，我们也可以叫做事务日志文件。事务日志会存储每一个InnoDB表数据的记录修改。当InnoDB启动时，InnoDB会检查数据文件和事务日志，并执行两个步骤：它应用（前滚）已经提交的事务日志到数据文件，并将修改过但没有提交的数据进行回滚操作。

##### 9.mysqldump中备份出来的sql，如果我想sql文件中，一行只有一个insert....value()的话，怎么办？如果备份需要带上master的复制点信息怎么办？
    --skip-extended-insert
    [root@helei-zhuanshu ~]# mysqldump -uroot -p helei --skip-extended-insert
    Enter password:
      KEY `idx_c1` (`c1`),
      KEY `idx_c2` (`c2`)
    ) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
    /*!40101 SET character_set_client = @saved_cs_client */;
    
    --
    -- Dumping data for table `helei`
    --
    
    LOCK TABLES `helei` WRITE;
    /*!40000 ALTER TABLE `helei` DISABLE KEYS */;
    INSERT INTO `helei` VALUES (1,32,37,38,'2016-10-18 06:19:24','susususususususususususu');
    INSERT INTO `helei` VALUES (2,37,46,21,'2016-10-18 06:19:24','susususususu');
    INSERT INTO `helei` VALUES (3,21,5,14,'2016-10-18 06:19:24','susu');

##### 10.500台db，在最快时间之内重启
    puppet自动化，安装dsh

##### 11.innodb的读写参数优化
    (1) 读取参数
        global buffer pool以及 local buffer；
    
    (2) 写入参数；
        innodb_flush_log_at_trx_commit
        innodb_buffer_pool_size
    
    (3) 与IO相关的参数；
        innodb_write_io_threads = 8
        innodb_read_io_threads = 8
        innodb_thread_concurrency = 0
    
    (4) 缓存参数以及缓存的适用场景。
        query cache/query_cache_type
        并不是所有表都适合使用query cache。造成query cache失效的原因主要是相应的table发生了变更
        第一个：读操作多的话看看比例，简单来说，如果是用户清单表，或者说是数据比例比较固定，比如说商品列表，是可以打开的，前提是这些库比较集中，数据库中的实务比较小。
        第二个：我们“行骗”的时候，比如说我们竞标的时候压测，把query cache打开，还是能收到qps激增的效果，当然前提示前端的连接池什么的都配置一样。大部分情况下如果写入的居多，访问量并不多，那么就不要打开，例如社交网站的，10%的人产生内容，其余的90%都在消费，打开还是效果很好的，但是你如果是qq消息，或者聊天，那就很要命。
        第三个：小网站或者没有高并发的无所谓，高并发下，会看到 很多 qcache 锁 等待，所以一般高并发下，不建议打开query cache


##### 12.你是如何监控你们的数据库的？你们的慢日志都是怎么查询的？
    监控的工具有很多，例如zabbix，lepus，我这里用的是lepus

##### 13.你是否做过主从一致性校验，如果有，怎么做的，如果没有，你打算怎么做？
    主从一致性校验有多种工具 例如checksum、mysqldiff、pt-table-checksum等

##### 14.你们数据库是否支持emoji表情，如果不支持，如何操作？
    如果是utf8字符集的话，需要升级至utf8_mb4方可支持
    之前的数据库字符集设计为 编码最大字符长度为3个字节（普通中文占两个字节，英文占一个字节，足够使用）
    utf8_mb4（most bytes 4） 内存为 4个字节，不排除以后还会增加新的字符集类型。

##### 15.你是如何维护数据库的数据字典的？
    这个大家维护的方法都不同，我一般是直接在生产库进行注释，利用工具导出成excel方便流通。

##### 16.你们是否有开发规范，如果有，如何执行的
    有，开发规范网上有很多了，可以自己看看总结下

##### 17.表中有大字段X(例如：text类型)，且字段X不会经常更新，以读为为主，请问:您是选择拆成子表，还是继续放一起，为什么？
    答：拆带来的问题：连接消耗 + 存储拆分空间；不拆可能带来的问题：查询性能；
    如果能容忍拆分带来的空间问题,拆的话最好和经常要查询的表的主键在物理结构上放置在一起(分区) 顺序IO,减少连接消耗,最后这是一个文本列再加上一个全文索引来尽量抵消连接消耗
    如果能容忍不拆分带来的查询性能损失的话:上面的方案在某个极致条件下肯定会出现问题,那么不拆就是最好的选择

##### 18.MySQL中InnoDB引擎的行锁是通过加在什么上完成(或称实现)的？为什么是这样子的？
    答：InnoDB是基于索引来完成行锁
    例: select * from tab_with_index where id = 1 for update;
    for update 可以根据条件来完成行锁锁定,并且 id 是有索引键的列,
    如果 id 不是索引键那么InnoDB将完成表锁,,并发将无从谈起

##### 19.如何从mysqldump产生的全库备份中只恢复某一个库、某一张表？
    答案见：http://suifu.blog.51cto.com/9167728/1830651

##### 20.开放性问题一个6亿的表a，一个3亿的表b，通过外间tid关联，你如何最快的查询出满足条件的第50000到第50200中的这200条数据记录。
    1、如果A表TID是自增长,并且是连续的,B表的ID为索引
    select * from a,b where a.tid = b.id and a.tid>500000 limit 200;
    
    2、如果A表的TID不是连续的,那么就需要使用覆盖索引.TID要么是主键,要么是辅助索引,B表ID也需要有索引。
    select * from b , (select tid from a limit 50000,200) a where b.id = a .tid;


##### 21.组合索引采用的是最左原则进行索引命中的。
    A、B、C为组合和索引，查询时 ABC生效，AC生效，BC不生效
    在Innodb引擎下or无法使用组合索引
    (select * from table where A=* or B=*)
    改进
    (select * from table where A=*) union (select * from table where B=*)
    索引的劣势（虽然提示了查询速度但是也会降低更新表的速度，更新时会更新数据和保存索引文件，
    建立索引会占用磁盘空间的索引文件）
    
    索引是对数据库表中一列或多列的值进行排序的一种结构
    DB在执行一条Sql语句的时候，默认的方式是根据搜索条件进行全表扫描，遇到匹配条件的就加入搜索结果集合。
    如果我们对某一字段增加索引，查询时就会先去索引列表中一次定位到特定值的行数，大大减少遍历匹配的行数，所以能明显增加查询的速度
 
##### 22.什么是索引 ？
    索引其实是一种数据结构，能够帮助我们快速的检索数据库中的数据
    
##### 23.索引具体采用的哪种数据结构？
    常见的MySQL主要有两种结构：Hash索引和B+ Tree索引，InnoDB引擎，默认的是B+树    
    
##### 24.InnoDB使用的B+ 树的索引模型，为什么采用B+ 树？这和Hash索引比较起来有什么优缺点吗？    
    (1) 因为Hash索引底层是哈希表，哈希表是一种以key-value存储数据的结构，O(1)
        所以多个数据在存储关系上是完全没有任何顺序关系的，所以，对于区间查询是无法直接通过索引查询的，就需要全表扫描O(n)。
        所以，哈希索引只适用于等值查询的场景。
        而B+ 始终保持 O(log(n)) 树是一种多路平衡查询树，所以他的节点是天然有序的（左子节点小于父节点、父节点小于右子节点），所以对于范围查询的时候不需要做全表扫描
    (2) B+Tree索引和Hash索引区别在于
        哈希索引适合等值查询，但是无法进行范围查询
        哈希索引没办法利用索引完成排序
        哈希索引不支持多列联合索引的最左匹配规则
        如果有大量重复键值的情况下，哈希索引的效率会很低，因为存在哈希碰撞问题
        
##### 24.1 B+树
     1.二叉树
        当数据量大的时候，树的高度会比较高，数据量大的时候，查询会比较慢；
        每个节点只存储一个记录，可能导致一次查询有很多次磁盘IO；
                    8
                   - - (这里始终是两个)
                  /   \ 
                 7     10
                - -    - -
               /  \   /   \  
              4   6  9    15
             
     2.B树
      不再是二叉搜索，而是m叉搜索；
      叶子节点，非叶子节点，都存储数据；
      中序遍历，可以获得所有节点；
      非根节点包含的关键字个数j满足，(m/2)-1 <= j <= m-1，节点分裂时要满足这个条件。
      B树被作为实现索引的数据结构被创造出来，是因为它能够完美的利用“局部性原理”。            
             左边 <  15 < 右边
                    -- (这里是m叉) 
                  /    \
               (4  8)  (17 18 19 20) 这里有两个节点,可以存储更多记录,充分利用预读的特性,极大减少磁盘IO  
              ---(3叉)    ----(4叉)
             /   |   \ 
           1 3  4  7  10 14
             -  -  -
            /   \ /
           2    5 6 
          (这里5可以挂在4上,也可以挂在6上,这里有个红黑平衡树,如果将5挂在6上,但5没有子节点,它会自动往上与6平级)
      3.什么是局部性原理？
        局部性原理的逻辑是这样的：
        (1)内存读写快，磁盘读写慢，而且慢很多；
        (2)磁盘预读：磁盘读写并不是按需读取，而是按页预读，一次会读一页的数据，
            每次加载更多的数据，如果未来要读取的数据就在这一页中，可以避免未来的磁盘IO，提高效率；
            通常，一页数据是4K。
        (3)局部性原理：
            软件设计要尽量遵循“数据读取集中”与“使用到一个数据，大概率会使用其附近的数据”，
            
      4.B树为何适合做索引？
        (1)由于是m分叉的，高度能够大大降低；
        (2)每个节点可以存储j个记录，如果将节点大小设置为页大小，
            例如4K，能够充分的利用预读的特性，极大减少磁盘IO；
            这样磁盘预读能充分提高磁盘IO；
            
      5.B+树,仍是m叉搜索树，在B树的基础上，做了一些改进：

                         1 - 8
                          --- 
                    /      |      \
                  1  4     5      6 8
                   --      -       --
                   /\      |       /\
                 1 2 3 4   5      6 7 8    
                 1 ------链表----->>> 8
                     数据存储在叶子节点
                每个叶子节点到 '根' 的长度一致
                    
            (1)非叶子节点不再存储数据，数据只存储在同一层的叶子节点上；
                B+树中根到每一个节点的路径长度一样，而B树不是这样。
            (2)叶子之间，增加了链表，获取所有节点，不再需要中序遍历；
            (3)这些改进让B+树比B树有更优的特性：
                (1) 范围查找:
                        定位min与max之后，中间叶子节点，就是结果集，不用中序回溯；
                        范围查询在SQL中用得很多，这是B+树比B树最大的优势。
                (2) 存储:
                        叶子节点存储实际记录行，记录行相对比较紧密的存储，适合大数据量 '磁盘存储' ；
                        非叶子节点存储记录的PK，用于查询加速，适合 '内存存储' ；
                (3) 存储更多索引:
                        非叶子节点，不存储实际记录，而只存储记录的KEY的话，
                        那么在相同内存的情况下，B+树能够存储更多索引；
                    
      6.为什么m叉的B+树比二叉搜索树的高度大大大大降低？
            (1)局部性原理，将一个节点的大小设为一页，一页4K，假设一个KEY有8字节，一个节点可以存储500个KEY，即j=500
            (2)m叉树，大概m/2<= j <=m，即可以差不多是1000叉树
            (3)那么：
            一层树：1个节点，1*500个KEY，大小4K
            二层树：1000个节点，1000*500=50W个KEY，大小1000*4K=4M
            三层树：1000*1000个节点，1000*1000*500=5亿个KEY，大小1000*1000*4K=4G
            可以看到，存储大量的数据（5亿），并不需要太高树的深度（高度3），索引也不是太占内存（4G）。
      7. end
        数据库索引用于加速查询
        虽然哈希索引是O(1)，树索引是O(log(n))，但SQL有很多“有序”需求，故数据库使用树型索引
        InnoDB不支持哈希索引
        数据预读的思路是：磁盘读写并不是按需读取，而是按页预读，一次会读一页的数据，每次加载更多的数据，以便未来减少磁盘IO
        局部性原理：软件设计要尽量遵循“数据读取集中”与“使用到一个数据，大概率会使用其附近的数据”，这样磁盘预读能充分提高磁盘IO
        数据库的索引最常用B+树：
            (1)很适合磁盘存储，能够充分利用局部性原理，磁盘预读；
            (2)很低的树高度，能够存储大量数据；
            (3)索引本身占用的内存很小；
            (4)能够很好的支持单点查询，范围查询，有序性查询；            
       
##### 25.1 B+ Tree的叶子节点都可以存哪些东西吗？        
    (1) InnoDB的B+ Tree可能存储的是整行数据，也有可能是主键的值   
##### 25.2 那这两者有什么区别吗 ？   (聚簇索引和非聚簇索引)
    在 InnoDB 里，索引B+ Tree的叶子节点存储了整行数据的是主键索引，也被称之为聚簇索引。
    而索引B+ Tree的叶子节点存储了主键的值的是非主键索引，也被称之为非聚簇索引
    聚簇: 叶子节点保存整行数据,非叶子节点保存PK值(用于查询加速)
    普通: 叶子节点保存PK值
##### 25.3 那么，聚簇索引(聚集索引)和非聚簇索引(普通索引)，在查询数据的时候有区别吗？    
    聚簇索引查询会更快
    因为主键索引树的叶子节点直接就是我们要查询的整行数据了。
    而非主键索引的叶子节点是主键的值，查到主键的值以后，还需要再通过主键的值再进行一次查询
    主键索引查询只会查一次，而非主键索引需要回表查询多次（回表）。
    
###### 25.3.1 回表 : 先定位主键值,再定位行记录,它的性能较扫一遍索引树更低  
    表信息: test ( id:pk , name:index , sex)
    数据 : 
        1  boy  男
        3  girl 女
        7  cam  男
        9  sun  女
        
    主键索引: id(聚簇索引) 的树结构
             1-9
           /     \  
        1-3      7-9
       /   \     /  \
      1     3   7    9
         这里保存了整行数据
        (1  boy  男), ...
     
     select name from test where id = 1;  
     通过ID查找直接获取到整条数据,然后取name的值
     
    普通索引 : name 的树结构
                b-s
               /   \
            b-c    g-s
           /  \   /   \
        boy cam girl  sun 
        这里保存的是主键值
        1    7    3    9
    select * from test where name = 'boy';  
    通过 name 查找到数据的主键值,再通过主键值去找整条数据  
    
##### 25.4  是所有情况都是这样的吗？非主键索引一定会查询多次吗？
    通过覆盖索引也可以只查询一次
    
##### 25.5 覆盖索引？
    覆盖索引（covering index）指一个查询语句的执行只用从索引中就能够取得，不必从数据表中读取。也可以称之为实现了索引覆盖。
    当一条查询语句符合覆盖索引条件时，MySQL只需要通过索引就可以返回查询所需要的数据，这样避免了查到索引后再返回表操作，减少I/O提高效率。
    表 test 中有添加一个组合索引 name_sex(`name`,'sex') ,这个时候子节点的值为 `boy,男,1`, `girl,女,9`, ...
    语句：
        select sex from test where name = 'boy'; 的时候，就可以通过覆盖索引查询，无需回表,因为节点上有对应的数据。
    
##### 26. 联合索引多个字段之间顺序你们是如何选择？
    把识别度最高的字段放到最前面 ？
    左前缀匹配吗 ？
    在创建多列索引时，根据业务需求，where子句中使用最频繁的一列放在最左边，因为MySQL索引查询会遵循最左前缀匹配的原则，即最左优先，在检索数据时从联合索引的最左边开始匹配。
    所以当我们创建一个联合索引的时候，如(key1,key2,key3)，相当于创建了（key1）、(key1,key2)和(key1,key2,key3)三个索引，这就是最左匹配原则
    
##### 27.索引下推(减少like查询时的回表数)、查询优化
    MySQL 5.6中，对索引做了哪些优化 ？

    Index Condition Pushdown（索引下推）
    MySQL 5.6引入了索引下推优化，默认开启，使用SET optimizer_switch = 'index_condition_pushdown=off';可以将其关闭。
    show variables like "%optimizer_switch%"; #会有很多子选项
    
    说明 :
        user 表中 (name,age) 为一个索引
        select * from user where age = 10 and name like "张%";
        如果没有使用索引下推技术，则MySQL会通过 age=10 从索引中找到符合 age = 10 的主键值通过回表获取到 对应的数据，返回到MySQL服务端，
            然后MySQL服务端基于 name like "张%" 来找到对应的主键值通过回表获取数据 。
        如果使用了索引下推技术，则MYSQL首先会返回符合 age = 10 的索引，
            然后根据 name like "张%" 来判断索引是否符合条件。
            如果符合条件，则根据该索引来定位对应的数据，如果不符合，则直接拒绝掉。
    优化 : 
        有了索引下推优化，可以在有like条件查询的情况下，减少回表次数。

##### 28. 索引生效，或者使用索引查询统计 ？
    可以通过explain查看sql语句的执行计划，通过执行计划来分析索引使用情况
    
##### 29. 什么情况下会发生明明创建了索引，但是执行的时候并没有通过索引呢 ？
    查询优化器 ？
    一条SQL语句的查询，可以有不同的执行方案，至于最终选择哪种方案，需要通过优化器进行选择，选择执行成本最低的方案。
    在一条单表查询语句真正执行之前，MySQL的查询优化器会找出执行该语句所有可能使用的方案，对比之后找出成本最低的方案。
    这个成本最低的方案就是所谓的执行计划。优化过程大致如下：
    1、根据搜索条件，找出所有可能使用的索引
    2、计算全表扫描的代价
    3、计算使用不同索引执行查询的代价
    4、对比各种执行方案的代价，找出成本最低的那一个
    
##### 30. 事务离级别 ？
    事务隔离级别(针对多个事务的数据处理)
    READ-UNCOMMITTED(读-未提交,有脏读) RU
      数据: name = test , A事务更新 name = test2,同时B事务也起了,B执行查询name = test2,
      若A回滚,实际数据为 name = test ,而B却返回了 name = test2 ,这就称之为脏读.
    READ-COMMITTED(读-已提交,不可重复读) RC
      一个事务只能读到另一个事务修改的已经提交了事务的数据
      A隐式提交了事务,B查询 name = test2,这是没有问题的,但B还没有结束,A中执行更新 name = test3,
      B执行查询 name = test3,这种称之为 不可重复读.
    REPEATABLE-READ(可重复读) RR
      一个事务只能读到另一个事务修改的已提交了事务的数据.
      但是第一次读取的数据，即使别的事务修改的这个值，
      这个事务再读取这条数据的时候还是和第一次获取的一样，不会随着别的事务的修改而改变
    SERIALIZABLE(串行化)
      只能进行读-读并发。只要有一个事务操作一条记录的写，那么其他要访问这条记录的事务都得等着
      一般没人用串行化，性能比较低，常用的是已提交读和可重复读。
      
##### 31.1 视图 ？
    是一种虚拟存在的表，视图可以理解为是一个容器，
	表通过条件查询之后，将查询后的结果放入这个容器内，
	然后给容器命名后即为视图。   

##### 31.2 视图相对于表的优势 ？
    简单，使用视图的用户不必关系后面的表，只需要使用过滤好的内容就行了；
    安全，因为对表的全新不能限制到表的行或者是列，所以可以通过视图来限制用户对表的访问权限；
    数据独立，确定了视图的结构之后，如果给原来的表增加了列，并不会影响视图，增加行，视图的相对于的行也会增加，如果源表的列名称发生了改变，可以通过修改视图来解决。
	
	表依赖修改视图也会修改，性能查询可能会慢特别是视图基于视图。
	
	
##### 32. Mysql读写性能是多少,有哪些性能相关的配置 ?
    读写性能可以根据压力测试来进行获取.
    相关配置:
        max_connections :	最大连接数,整个mysql服务器的最大连接数
        max_user_connections :	最大连接数,指的是每个数据库用户的最大连接数，是限制用户连接的。
                                比如：虚拟主机可以用这个参数控制每个虚拟主机用户的数据库最大连接数
        (max_connections是指MySQL实例的最大连接数，上限值是16384，max_user_connections是指每个数据库用户的最大连接数。)                                
        table_cache		 :	缓存打开表的数量
        key_buffer_size	 :	索引缓存大小
        query_buffer_size:	查询缓存大小
        sort_buffer_size :	排序缓存大小
        read_buffer_size :	顺序读缓存大小
        具体查询配置:
            show variables like '%max_connecttions%'
##### 33. SQL层面已经难以优化,请求量还在增加的对应策略 ?
    分库分表
    使用集群(master-slave),读写分离
    增加业务层的cache层
    使用连接层
##### 34. 如何防止DB误操作和做好防灾 ?	
    重要DB数据的手工修改操作，操作前需做到2点：
        1.先在测试环境操作 
        2.备份数据:根据业务重要性做定时备份，考虑系统可承受的恢复时间
        进行容灾演练，感觉很必要
    MySql备份和恢复	
    
##### 35. MySql内部结构有哪些层次 ?
    Connectors：连接器。接收不同语言的Client交互
    Management Serveices & Utilities：系统管理和控制工具
    Connection Pool: 连接池。管理用户连接
    SQL Interface: SQL接口。接受用户的SQL命令，并且返回用户需要查询的结果
    Parser: 解析器。验证和解析SQL语句成内部数据结构
    Optimizer: 查询优化器。为查询语句选择合适的执行路径
    Cache和Buffer：查询缓存。缓存查询的结果，有命中即可直接返回
    Engine：存储引擎。MySQL数据最后组织并存储成具体文件	 	
    
##### 36. 针对InnoDB的机制，我们可以尝试几种优化方法:
    a.在session级别，提供可设置预读的触发条件，并使用多个后台线程来完成异步IO请求。因为没有减少小IO请求，作者尝试了这种方法，收益甚小；
    b.独立一个buffer pool，专门进行多块读，针对next extent，一次读取到buffer pool中，这种方式就和Oracle的multiblock-read比较类似了；
    c.终极优化方法，就是使用并行查询，Oracle在全表扫描的时候，使用/* parallel */ hint方法启动多个进程完成查询，InnoDB的聚簇索引结构，需要逻辑分片，针对每一个分片启动一个线程完成查询。
   
   
##### 37. Specified key was too long; max key length is 767 bytes
    原因
        数据库表采用utf8编码，其中varchar(255)的column进行了唯一键索引
        而mysql默认情况下单个列的索引不能超过767位(不同版本可能存在差异)
        于是utf8字符编码下，255*3 byte 超过限制
    解决
        1  使用innodb引擎；
        2  启用innodb_large_prefix选项，将约束项扩展至3072byte；
        3  重新创建数据库；
     
    my.cnf配置：
        default-storage-engine=INNODB
        innodb_large_prefix=on
    一般情况下不建议使用这么长的索引，对性能有一定影响；   
    
##### 38. 索引失效

        a.单列索引无法储null值，复合索引无法储全为null的值。
        b.查询时，采用is null条件时，不能利用到索引，只能全表扫描。
        select * from talbe where index = null;
    
        为什么索引列无法存储Null值？
        a.索引是有序的。NULL值进入索引时，无法确定其应该放在哪里。
        （将索引列值进行建树，其中必然涉及到诸多的比较操作，null 值是不确定值无法　　
        比较，无法确定null出现在索引树的叶子节点位置。）　
        b.如果需要把空值存入索引，
        其一，把NULL值转为一个特定的值，在WHERE中检索时，用该特定值查找。
        其二，建立一个复合索引。例如　
        create index ind_a on table(col1,1);
        通过在复合索引中指定一个非空常量值，而使构成索引的列的组合中，不可能出现全空值。　  
        
        不适合键值较少的列（重复数据较多的列）        
        
        前导模糊查询不能利用索引(like '%XX'或者like '%XX%')  
        
        or like != 等非成立表达式
        
##### 39. 优化
###### 问题
* 优化不总是对一个单纯的环境进行，还很可能是一个复杂的已投产的系统。
* 优化手段本来就有很大的风险，只不过你没能力意识到和预见到！
* 任何的技术可以解决一个问题，但必然存在带来一个问题的风险！
* 对于优化来说解决问题而带来的问题,控制在可接受的范围内才是有成果。
* 保持现状或出现更差的情况都是失败！   
###### 需求
* 稳定性和业务可持续性,通常比性能更重要！
* 优化不可避免涉及到变更，变更就有风险！
* 优化使性能变好，维持和变差是等概率事件！
* 切记优化,应该是各部门协同，共同参与的工作，任何单一部门都不能对数据库进行优化！
* 所以优化工作,是由业务需要驱使的！！！ 
###### 思路
~~~
安全 : 数据可持续性
性能 : 数据的高性能访问
~~~    
###### 范围
* 存储、主机和操作系统
~~~
1) 主机架构稳定性
2) I/O规划及配置 
3) Swap交换分区
4) OS内核参数和网络问题
~~~
* 应用程序
~~~
1) 应用程序稳定性 
2) SQL语句性能 
3) 串行访问资源 
4) 性能欠佳会话管理
5) 这个应用适不适合用MySQL
~~~
* 数据库方面
~~~
1) 内存
2) 数据库结构(物理&逻辑) 
3) 实例配置
~~~
说明：不管是在，设计系统，定位问题还是优化，都可以按照这个顺序执行。
###### 数据库优化
~~~
硬件、系统配置、数据库表结构、SQL及索引。
优化选择：
1) 优化成本: 硬件>系统配置>数据库表结构>SQL及索引
2) 优化效果: 硬件<系统配置<数据库表结构<SQL及索引
~~~
###### 工具
检查问题常用工具
```
mysql
msyqladmin                                 mysql客户端，可进行管理操作
mysqlshow                                  功能强大的查看shell命令
show [SESSION | GLOBAL] variables          查看数据库参数信息
SHOW [SESSION | GLOBAL] STATUS             查看数据库的状态信息
information_schema                         获取元数据的方法
SHOW ENGINE INNODB STATUS                  Innodb引擎的所有状态
SHOW PROCESSLIST                           查看当前所有连接session状态
explain                                    获取查询语句的执行计划
show index                                 查看表的索引信息
slow-log                                   记录慢查询语句
mysqldumpslow                              分析slowlog文件的
```
不常用但好用的工具
```
zabbix                  监控主机、系统、数据库（部署zabbix监控平台）
pt-query-digest         分析慢日志
mysqlslap               分析慢日志
sysbench                压力测试工具
mysql profiling         统计数据库整体状态工具    
Performance Schema      mysql性能状态统计的数据
workbench               管理、备份、监控、分析、优化工具（比较费资源）
```
###### 数据库层面问题解决思路
应急:针对突然的业务办理卡顿，无法进行正常的业务处理！需要立马解决的场景！
```
#显示进程
show processlist

#sql语句分析
explain  select id ,name from stu where name='clsn'; 
#ALL  id name age  sex

select id,name from stu  where id=2-1 函数 结果集>30;

#查看表索引
show index from table;

#通过执行计划判断，索引问题（有没有、合不合理）或者语句本身问题

#查询锁状态
show status  like '%lock%';   

#杀掉有问题的session
kill SESSION_ID;   
```
常规:针对业务周期性的卡顿，例如在每天10-11点业务特别慢，但是还能够使用，过了这段时间就好了
~~~
1) 查看slowlog，分析slowlog，分析出查询慢的语句。
2) 按照一定优先级，进行一个一个的排查所有慢语句。
3) 分析top sql，进行explain调试，查看语句执行时间。
4) 调整索引或语句本身。
~~~
###### 系统
* CPU `vmstat、sar top、htop、nmon、mpstat`
* 内存 `free 、 ps -aux `
* IO设备(磁盘、网络) `iostat 、 ss 、 netstat 、 iptraf、iftop、lsof、`

###### 系统层面问题解决办法
~~~
你认为到底负载高好，还是低好呢？
在实际的生产中，一般认为 cpu只要不超过90%都没什么问题 。
当然不排除下面这些特殊情况：
问题一：cpu负载高，IO负载低
1、内存不够 2、磁盘性能差 3、SQL问题 ------>去数据库层，进一步排查sql问题 4、IO出问题了（磁盘到临界了、raid设计不好、raid降级、锁、在单位时间内tps过高） 5、tps过高: 大量的小数据IO、大量的全表扫描
问题二：IO负载高，cpu负载低
1、大量小的IO 写操作：2、autocommit ，产生大量小IO 3、IO/PS,磁盘的一个定值，硬件出厂的时候，厂家定义的一个每秒最大的IO次数。4、大量大的IO 写操作 5、SQL问题的几率比较大
问题三：IO和cpu负载都很高
硬件不够了或sql存在问题
~~~
###### 基本操作
定位问题点 `硬件 --> 系统 --> 应用 --> 数据库 --> 架构（高可用、读写分离、分库分表）`
处理方向 `明确优化目标、性能和安全的折中、防患未然`
硬件优化
~~~
主机方面：
1) 根据数据库类型，主机CPU选择、内存容量选择、磁盘选择 
2) 平衡内存和磁盘资源 
3) 随机的I/O和顺序的I/O 
4) 主机 RAID卡的BBU(Battery Backup Unit)关闭
cpu的选择：
1) cpu的两个关键因素：核数、主频 
2) 根据不同的业务类型进行选择
3) cpu密集型：计算比较多，OLTP 主频很高的cpu、核数还要多 
4) IO密集型：查询比较，OLAP 核数要多，主频不一定高的
内存的选择：
1) OLAP类型数据库，需要更多内存，和数据获取量级有关。
2) OLTP类型数据一般内存是cpu核心数量的2倍到4倍，没有最佳实践。
存储方面：
1) 根据存储数据种类的不同，选择不同的存储设备 
2) 配置合理的RAID级别(raid5、raid10、热备盘) 
3) 对与操作系统来讲，不需要太特殊的选择，最好做好冗余（raid1）（ssd、sas 、sata）
raid卡：主机raid卡选择：
1) 实现操作系统磁盘的冗余（raid1）
 2) 平衡内存和磁盘资源 
3) 随机的I/O和顺序的I/O 
4) 主机 RAID卡的BBU(Battery Backup Unit)要关闭。
网络设备方面：
使用流量支持更高的网络设备（交换机、路由器、网线、网卡、HBA卡）
注意：以上这些规划应该在初始设计系统时就应该考虑好。
~~~
服务器硬件优化
~~~
1) 物理状态灯：
2) 自带管理设备：远程控制卡（FENCE设备：ipmi ilo idarc），开关机、硬件监控。
3) 第三方的监控软件、设备（snmp、agent）对物理设施进行监控
4) 存储设备：自带的监控平台。EMC2（hp收购了）， 日立（hds），IBM低端OEM hds，高端存储是自己技术，华为存储
~~~
系统优化
~~~
Cpu：
基本不需要调整，在硬件选择方面下功夫即可。
内存：
基本不需要调整，在硬件选择方面下功夫即可。
SWAP：
MySQL尽量避免使用swap。阿里云的服务器中默认swap为0
IO ：
1) raid、no lvm、 ext4或xfs、ssd、IO调度策略 
2) Swap调整(不使用swap分区)
    /proc/sys/vm/swappiness的内容改成0（临时），/etc/sysctl.conf上添加vm.swappiness=0（永久）
这个参数决定了Linux是倾向于使用swap，还是倾向于释放文件系统cache。在内存紧张的情况下，数值越低越倾向于释放文件系统cache。当然，这个参数只能减少使用swap的概率，并不能避免Linux使用swap。修改MySQL的配置参数innodbflushmethod，开启O_DIRECT模式。这种情况下，InnoDB的buffer pool会直接绕过文件系统cache来访问磁盘，但是redo log依旧会使用文件系统cache。值得注意的是，Redo log是覆写模式的，即使使用了文件系统的cache，也不会占用太多。
IO调度策略：
    vi /boot/grub/grub.conf
    更改到如下内容:
    kernel /boot/vmlinuz-2.6.18-8.el5 ro root=LABEL=/ elevator=deadline rhgb quiet

~~~
系统参数调整
~~~
Linux系统内核参数优化：
vim /etc/sysctl.conf
    net.ipv4.ip_local_port_range = 1024 65535   # 用户端口范围
    net.ipv4.tcp_max_syn_backlog = 4096 
    net.ipv4.tcp_fin_timeout = 30 
    fs.file-max=65535          # 系统最大文件句柄，控制的是能打开文件最大数量
用户限制参数（mysql可以不设置以下配置）：
vim    /etc/security/limits.conf 
    * soft nproc 65535
    * hard nproc 65535
    * soft nofile 65535
    * hard nofile 65535
~~~
应用优化
~~~
业务应用和数据库应用独立,防火墙：iptables、selinux等其他无用服务(关闭)：
    chkconfig --level 23456 acpid off
    chkconfig --level 23456 anacron off
    chkconfig --level 23456 autofs off
    chkconfig --level 23456 avahi-daemon off
    chkconfig --level 23456 bluetooth off
    chkconfig --level 23456 cups off
    chkconfig --level 23456 firstboot off
    chkconfig --level 23456 haldaemon off
    chkconfig --level 23456 hplip off
    chkconfig --level 23456 ip6tables off
    chkconfig --level 23456 iptables  off
    chkconfig --level 23456 isdn off
    chkconfig --level 23456 pcscd off
    chkconfig --level 23456 sendmail  off
    chkconfig --level 23456 yum-updatesd  off
安装图形界面的服务器不要启动图形界面 runlevel 3,另外，思考将来我们的业务是否真的需要MySQL，还是使用其他种类的数据库。用数据库的最高境界就是不用数据库。
~~~
###### 数据库优化
* SQL优化方向：执行计划、索引、SQL改写
* 架构优化方向：高可用架构、高性能架构、分库分表

实例整体（高级优化，扩展）
```
thread_concurrency       # 并发线程数量个数
sort_buffer_size         # 排序缓存
read_buffer_size         # 顺序读取缓存
read_rnd_buffer_size     # 随机读取缓存
key_buffer_size          # 索引缓存
thread_cache_size        # (1G—>8, 2G—>16, 3G—>32, >3G—>64)
```
连接层（基础优化）设置合理的连接客户和连接方式
```
max_connections           # 最大连接数，看交易笔数设置    
max_connect_errors        # 最大错误连接数，能大则大
connect_timeout           # 连接超时
max_user_connections      # 最大用户连接数
skip-name-resolve         # 跳过域名解析
wait_timeout              # 等待超时
back_log                  # 可以在堆栈中的连接数量
```
SQL层（基础优化）
~~~
querycachesize：查询缓存-->>>OLAP类型数据库,需要重点加大此内存缓存.
1) 但是一般不会超过GB.
2) 对于经常被修改的数据，缓存会立马失效。
3) 我们可以实用内存数据库（redis、memecache），替代他的功能。
~~~
存储引擎层（innodb基础优化参数）
```
default-storage-engine
innodb_buffer_pool_size       # 没有固定大小，50%测试值，看看情况再微调。但是尽量设置不要超过物理内存70%
innodb_file_per_table=(1,0)
innodb_flush_log_at_trx_commit=(0,1,2) # 1是最安全的，0是性能最高，2折中
binlog_sync
Innodb_flush_method=(O_DIRECT, fdatasync)
innodb_log_buffer_size        # 100M以下
innodb_log_file_size          # 100M 以下
innodb_log_files_in_group     # 5个成员以下,一般2-3个够用（iblogfile0-N）
innodb_max_dirty_pages_pct   # 达到百分之75的时候刷写 内存脏页到磁盘。
log_bin
max_binlog_cache_size         # 可以不设置
max_binlog_size               # 可以不设置
innodb_additional_mem_pool_size    #小于2G内存的机器，推荐值是20M。32G内存以上100M
```

##### 40.主从一致
    主从DB存在一定的同步时间,在这个时间段中有可能读取到旧数据
    (1)半同步复制 -> 等主从同步完成之后，主库上的写请求再返回, semi-sync
    (2)强制读主
    (3)数据库中间件  -> 数据库请求通过中间件完成,记录写的key,在同步时间内读取主库
    (4)缓存记录写key -> 同 中间件,记录写的key和对应的同步时间,时间内走主库,不在走从库
    
##### 41.秒杀
    两个 架构优化思路：
    （1）尽量将请求拦截在系统上游（越上游越好）；
    （2）读多写少的常用多使用缓存（缓存抗读压力）；
    前端: 扩容,限流,静态化
    浏览器和APP：做限速,做单次点击
    站点层：按照uid做限速，做页面缓存
    后端: 内存,缓存,排队,MQ
    服务层：按照业务做写请求队列控制流量，做数据缓存
    数据层：处理
    
    具体信息: 
        分表 -> 分库 -> 一主多从(数据路由,分发) -> 多主多从(一致性,高可读,高可写)   
    分表
        1.分表ID无关联,自增即可
        2.分表数据有连贯性,设置总分表记录自增ID,将ID分发到各自分表
        3.自定义唯一ID,需要通过生成器生成
        4.按照数据范围,1 - 2亿在 a, 3 - 4亿在 b,...
        5.按照hash值
        6.数据取模
    分库
        1.无关联,自增即可
        2.有关联,设置自增歩长
        3.统一分发    
