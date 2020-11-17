1.更新数据字段(数据集合)

	update `table` t1 INNER JOIN (`查询集合`) t2 on t1.pid = t2.id set t1.name = t2.pid_name where t1.pid != 0;

2.str存在strlist中 

    FIND_IN_SET(str,strlist)

3.将查询结果插入到表中
	
	insert into tab (a,b,c) 
	select * from tab2
	
4.查看表名的字段

	information_schema系统库(mysql相关的信息都能在系统库中找到)
	SELECT COLUMN_NAME FROM information_schema.COLUMNS where table_name = 'tab_name';
		
5.复制一张表
 
    CREATE TABLE abc AS
    SELECT * FROM test t 	会把test的结构和数据都会创建到abc中
   
6.创建一张临时表

    CREATE TEMPORARY TABLE abc
    SELECT * FROM m_b_address
    SHOW CREATE TABLE abc;   #  查看创建表的sql
   
7.查看表的列

    DESC cp_bets ; 	
    SHOW columns FROM  cp_bets ;
    show full columns from table; #查看完整的字段信息

8.explain sql语句 (查看sql语句,看索引是否生效)   
 
9.触发器

    在创建表的时候可以运用触发器去操作另一张表的数据.
    触发器是基于行触发的，所以删除、新增或者修改操作可能都会激活触发器，
    所以不要编写过于复杂的触发器，
    也不要增加过得的触发器，这样会对数据的插入、修改或者删除带来比较严重的影响，
    同时也会带来可移植性差的后果，所以在设计触发器的时候一定要有所考虑。
   
10.this is incompatible with sql_mode=only_full_group_by

	select version();  #查看版本信息 >5.7 -> 修改配置支持group( sql_mode = '' ) 或者在字段前加上 any_value
    #table
    user_id tag_name
    1           2
    1           3
     	
	select user_id,tag_name from table group by user_id.
	此时 user_id = 1,有两条数据,mysql无法判断到底获取哪一条数据,就会出现 only_full_group_by

	
11.mysql循环插入数据

	#创建存储过程
	CREATE PROCEDURE test_insert () 
		#开始
		BEGIN
			#定义变量 
			DECLARE i INT DEFAULT 1;
				#条件判断
				WHILE i<1000000 
				#执行
				DO 
					#SQL
					INSERT SQL ;
					#变量增加
					SET i=i+1;
				#结束循环 
				END WHILE ;
			#提交 
			commit; 
		#结束
		END;
	#执行
	CALL test_insert();	
	#删除存储过程
	drop procedure test_insert ;
	#查看存储过程
	SHOW PROCEDURE STATUS ;
	#查看创建存储过程的语句
	SHOW CREATE PROCEDURE test_insert8 ;
	
12.原生导出

	select * from `user` into outfile 't1.xls';	
	CHARACTER SET gbk ; #设置编码excl默认为gbk
	#如果无法运行在命令行查看
	#show variables like '%secure%';
	#如果secure_file_priv为null修改my.ini添加或者修改
	#secure-file-priv=  或者 secure-file-priv='保存导出的地址'
	#上面不填写的地址默认为mysql data的地址
	#保存 重启 执行
	
13.查询替换和新增

	SELECT
	 	insert(mobile_number, 4, 4, 'XXXX') ,
	 	REPLACE(mobile_number, SUBSTR(mobile_number,4,4), 'XXXX')
	FROM
		play_order;
		
	update table
    set   key_name = REPLACE(key_name,'要替换的内容','新内容') 
    where key_name like '%要替换的内容%';
    
    #去除换行和回车
    #char(10):  换行符
    #char(13):  回车符
    UPDATE table SET field = REPLACE(REPLACE(field, CHAR(10), ''), CHAR(13), '');
		
14:having 指定一组行或聚合的过滤条件,通常和group by连用

    (通过column分组,查询出名称和对应的总数,获取总数大于10的)
    SELECT count(id) as total,name FROM T GROUP BY column having total > 10
    
15:

    LIKE “%name”或者LIKE “%name%”，这种查询会导致索引失效而进行全表扫描。但是可以使用LIKE “name%”。
    那如何查询%name%？使用全文索引
    eg: select id,fnum,fdst from table_name where user_name like '%zhangsan%' ;
    新增全文索引:(全文索引不能用InnoDB,用MyISAM,MySQL5.6.24上InnoDB引擎也加入了全文索引)
        ALTER TABLE `table_name` ADD FULLTEXT
        INDEX `idx_user_name` (`user_name`);
    新查询: select id,fnum,fdst from table_name
    where match(user_name) against('zhangsan' in boolean mode);
    全文索引格式:
         MATCH (columnName,...) AGAINST ('string')
16.系统常用

    DESC table_name; #显示table列的信息
    SHOW CREATE TABLE table_name; #显示创建table的SQL
    SHOW INDEX FROM table_name; #显示table的索引
    
17.表 user_table, 存在 多条用户数据并状态不同 , 需要将用户数据合并成一条 以不同字段 显示数据

    (不建议使用SQL查询， 应当按照结果去创建对应的表结构， 通过任务脚本进行数据填充)
    user_table
    id  user_id state
    1   1       1
    2   1       2
    3   1       3
    =>
    user_id state1 state2 state3
    1       1       1       1

    SQL1:
        select
                  user_id,
                  ( select count(*) from user_table as t1 where t1.user_id = temp.user_id and  state = 1) as state1,
                  ( select count(*) from user_table as t1 where t1.user_id = temp.user_id and  state = 2) as state2,
                  ( select count(*) from user_table as t1 where t1.user_id = temp.user_id and  state = 3) as state3
        from (
                # 主sql
                select user_id from user_table group by user_id
             )
        as temp;

    SQL2:根据 user 表进行连表查询
    select
        ut.user_id,
       (select count(*) from user_table as t1 where state = 1 and t1.user_id = u.id) as state1,
       (select count(*) from user_table as t1 where state = 2 and t1.user_id = u.id) as state2,
       (select count(*) from user_table as t1 where state = 3 and t1.user_id = u.id) as state3

    from user as u
             left join user_table as ut on u.id = ut.user_id
    where ut.user_id != ''
    group by pma.id

18.字符串区分大小写:(不同数据表的字符集，可以区别大小写)

    Mysql字符检索策略：
        utf8_general_ci，表示不区分大小写；
        utf8_general_cs表示区分大小写，
        utf8_bin表示二进制比较，同样也区分大小写
    binary : 类型转换运算符,将字符串转换为二进制,进而区分大小写
    eg:
        select * from table where field like binary 'a%';
        
19.根据 in 排序

    select * 
    from 
    table_name
    where id in (1,2,3,5,4,6)
    order by field (id,1,2,3,5,4,6)
    ;        

20. like escape(逃逸),在like中某也字符串被mysql赋予了一些规则
    %   全部匹配
    _   只匹配一个
    如果查询内容包含 %，_ 或者其它特殊意义字符，则需要使用 escape 进行转义
    Table: tt
    Data:
        ID  VAR_NAME
        1	小A%
        2	张%三
        3	李四
        4	小M
        5	大M
        6	_M
    Commend:  like "" escape "任意符号，需要与like中匹配，建议使用/"
    #1,2
    select * from tt where var_name like "%>%%" escape '>';
    
    #all
    select * from tt where var_name like "%%%" escape '/';
    
    #all
    select * from tt where var_name like "%%%";
    
    #6
    select * from tt where var_name like "/_M" escape '/';
    
    #4,5,6
    select * from tt where var_name like "_M";
    
    
