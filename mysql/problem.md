## MySQL

##### 1.MySQL的复制原理以及流程
    (1) 主：binlog线程——记录下所有改变了数据库数据的语句，放进master上的binlog中；
    (2) 从：io线程——在使用start slave 之后，负责从master上拉取 binlog 内容，放进 自己的relay log中；
    (3) 从：sql执行线程——执行relay log中的语句；
    
##### 2.MySQL中myisam与innodb的区别
    (1) 不同点；
        1>.InnoDB支持事物，而MyISAM不支持事物
        2>.InnoDB支持行级锁，而MyISAM支持表级锁
        3>.InnoDB支持MVCC(多版本并发控制), 而MyISAM不支持
        4>.InnoDB支持外键，而MyISAM不支持
        5>.InnoDB不支持全文索引，而MyISAM支持。
    (2) innodb引擎的4大特性
        插入缓冲（insert buffer),二次写(double write),自适应哈希索引(ahi),预读(read ahead)
    (3) 2者 select count(*)哪个更快，为什么
        myisam更快，因为myisam内部维护了一个计数器，可以直接调取。
        
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
        
##### 4.Innodb的事务与日志的实现方式
    (1) 有多少种日志
            错误日志：记录出错信息，也记录一些警告信息或者正确的信息。
            查询日志：记录所有对数据库请求的信息，不论这些请求是否得到了正确的执行。
            慢查询日志：设置一个阈值，将运行时间超过该值的所有SQL语句都记录到慢查询的日志文件中。
            二进制日志：记录对数据库执行更改的所有操作。
            中继日志：
            事务日志：
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
            