#### 1.安装
	curl -O https://fastdl.mongodb.org/linux/mongodb-linux-x86_64-3.0.6.tgz //下载文件
	tar -zxvf mongodb-linux-x86_64-3.0.6.tgz //解压
#### 2.数据库目录
	有些安装会默认创建 /data/db #如果没有自行创建
#### 3.启动
	在MongoDB的bin中
		./mongod #如果报连接错误
		=> ./mongod --dbpath=/data/bin #指定启动的数据库目录,指定一次后之后会默认		
		(如果不行可以删除 /data/db/mongod.lock)
		默认端口:27017
		默认web访问端口:27018
#### 4.进入后台管理
	./mongo		(启动和进入需要两个窗口)
	#php连接,这里参照官网给出的信息有两种
		1.mongo(旧版) $mongo = new MongoClient();
		2.mongoDb(新版) $mongo = new MongoDb\Dirver\Manager('mongodb://127.0.0.1:27017');
#### 5.使用	
	show dbs			#显示所有数据列表
	db 					#显示当前连接数据库
	use test 			#使用test数据库
	use min 			#创建use数据库
	db.min.insert({"name":"min test"})		#向min中插入数据,如果没有数据是不会显示数据库
	db.dropDatabase() 	#删除数据库
	show tables 		#查看集合
	_对比关系型数据库
	SQL术语/概念			MongoDB术语/概念			解释/说明
	database				database					数据库
	table					collection					数据库表/集合
	row						document					数据记录行/文档	
	column					field						数据字段/域
	index					index						索引
	table 					joins	 					表连接,MongoDB不支持
	primary key				primary key					主键,MongoDB自动将_id字段设置为主键
#### 6.插入文档
	语法:db.COLLECTION_NAME.insert(document)
		 document支持定义 document=({})
		 db.col.insert({title:'标题',name:'测试',id:'没有'})
		 db.col.find(<query>)				#寻找col文档
		 db.col.find(<query>).pretty() 		#便于查看格式
#### 7.修改文档
	语法:db.collection.update(
		   <query>,
		   <update>,
		   {
		     upsert: <boolean>,
		     multi: <boolean>,
		     writeConcern: <document>
		   }
		)		 
	参数说明：
		query : update的查询条件，类似sql update查询内where后面的。
		update : update的对象和一些更新的操作符（如$,$inc...）等，也可以理解为sql update查询内set后面的
		upsert : 可选，这个参数的意思是，如果不存在update的记录，是否插入objNew,true为插入，默认是false，不插入。
		multi : 可选，mongodb 默认是false,只更新找到的第一条记录，如果这个参数为true,就把按条件查出来多条记录全部更新。
		writeConcern :可选，抛出异常的级别。
	eg:
		db.col.update({'title':'标题'},{$set:{'title':'测试'}});	
#### 8.删除文档
	语法:db.collection.remove(
		   <query>,
		   <justOne>
		)		
	参数说明：
		query :（可选）删除的文档的条件。
		justOne : （可选）如果设为 true 或 1，则只删除一个文档。
		writeConcern :（可选）抛出异常的级别。	
	eg:
		db.col.remove({'title':'标题'},2) 	#删除两条	
#### 9.查询
	 语法:db.col.find(<query>)
	 参数说明:
	 	等于		{<key>:<value>}			db.col.find({"by":"菜鸟教程"}).pretty()						where by = '菜鸟教程'
		小于		{<key>:{$lt:<value>}}	db.col.find({"likes":{$lt:50}}).pretty()					where likes < 50
		小于或等于	{<key>:{$lte:<value>}}	db.col.find({"likes":{$lte:50}}).pretty()					where likes <= 50
		大于		{<key>:{$gt:<value>}}	db.col.find({"likes":{$gt:50}}).pretty()					where likes > 50
		大于或等于	{<key>:{$gte:<value>}}	db.col.find({"likes":{$gte:50}}).pretty()					where likes >= 50
		不等于		{<key>:{$ne:<value>}}	db.col.find({"likes":{$ne:50}}).pretty()					where likes != 50
#### 10.其它
	语法:db.COLLECTION_NAME.find().limit(NUMBER)		
	语法:db.COLLECTION_NAME.find().limit(NUMBER).skip(NUMBER) #指定跳过几条数据
	语法:db.COLLECTION_NAME.find().sort({KEY:1}) 排序 1和-1正倒叙
		 db.col.find().sort({'title':-1})
#### 11.索引
	语法:db.COLLECTION_NAME.ensureIndex({KEY:1})		
		语法中 Key 值为你要创建的索引字段，1为指定按升序创建索引，如果你想按降序来创建索引指定为-1即可。 
		db.col.ensureIndex({'title':1,'name':-1},{}) 		#复合索引
		db.col.ensureIndex({'title':1})
	_没做说明的以后了解	
#### 12.聚合(处理数据,计算,求和等)
	语法:db.COLLECTION_NAME.aggregate(AGGREGATE_OPERATION)		
	管道聚合
#### 13.复制(主从节点)	
#### 14.分片(可以满足MongoDB数据量大量增长的需求)
#### 15.备份和恢复
	mongodump命令来备份MongoDB数据,安装是自带的命令在bin目录中
	语法:
		mongodump -h dbhost -d dbname -o dbdirectory
	参数说明:	
		-h：
		MongDB所在服务器地址，例如：127.0.0.1，当然也可以指定端口号：127.0.0.1:27017

		-d：
		需要备份的数据库实例，例如：test

		-o：
		备份的数据存放位置，例如：c:\data\dump，当然该目录需要提前建立，在备份完成后，系统自动在dump目录下建立一个test目录，这个目录里面存放该数据库实例的备份数据。
	测试:
		在mongo的bin目录中运行以上命令	
		./mongodump -h 127.0.0.1 -d test -o /data/mongodata
#### 16.监控
	MongoDB中提供了mongostat 和 mongotop 两个命令来监控MongoDB的运行情况。(bin中)		
