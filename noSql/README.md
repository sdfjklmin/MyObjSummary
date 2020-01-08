## Not Only SQL
    泛指非关系型的数据库
    
#### K-V 数据库
* Redis
* Cassandra
* LevelDB 
###### 优点,以 `redis` 为例
    性能极高：Redis能支持超过10W的TPS
    丰富的数据类型： Redis支持包括String，Hash，List，Set，Sorted Set，Bitmap和hyperloglog
    丰富的特性：Redis还支持 publish/subscribe, 通知, key 过期等等特性
###### 缺点
    针对ACID,Redis事务不能支持原子性和持久性(A和D)，只支持隔离性和一致性(I和C)
    特别说明一下，这里所说的无法保证原子性，是针对Redis的事务操作，因为事务不支持回滚（roll back），
    因为Redis的单线程模型，Redis的普通操作是原子性的
   
   
#### 文档数据库(通常以 JSON 或 XML 格式存储数据) 
* MongoDB
* CouchDB

#### 全文搜索引擎
* Elasticsearch
* Solr

#### 图形数据库
* Neo4j
* ArangoDB
* Titan

####参考文献
* [NoSQL 还是 SQL ？](https://www.jianshu.com/p/296bacba3510)