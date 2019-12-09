## mysql json 对应的mysql版本要>=5.7，某些特定的函数需要对应的高版本才能使用，统一以5.7作为测试
 
## mysql json 数据格式 [友情链接](https://dev.mysql.com/doc/refman/5.7/en/json-functions.html)
```
# 公用json数据， @jsonOne 、 @jsonTwo 可以是对应的字段名称

# 无键json
set @jsonOne = '[10, 20, [30, 40]]';

# key:value
set @jsonTwo = '{"id": "3", "name": "Barney","sex":{"man" : "man","woman" : "woman"}}';

```

#### JSON_EXTRACT(json_doc, path[, path] ...)
从 JSON 文档中提前(extract)返回数据 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html#function_json-extract)

```
##########@jsonOne#########
# 获取全部
SELECT JSON_EXTRACT(@jsonOne, '$');

# 获取第一个
SELECT JSON_EXTRACT(@jsonOne, '$[0]');

# 获取最后一个，比较麻烦 concat:字符串连接 , json_depth:获取json深度，单index是从0开始，所以要减1
select json_extract(@jsonOne,concat("$[",json_depth(@jsonOne) - 1,"]"));

# 获取第三个中的第一个
SELECT JSON_EXTRACT(@jsonOne, '$[2][0]');


##########@jsonTwo#########
# 获取全部
select json_extract(@jsonTwo,'$');

# 获取某个key
select json_extract(@jsonTwo,'$.id');

# 获取多层级key
select json_extract(@jsonTwo,'$.sex.man');

# 直接获取 > 5.7.9
select field->"$.key.key" from table;
select field->"$[index]"  from table;

# 查询运用
select * from talbe where field->"$.key" = value
select * from talbe where json_extract(field,"$.key") = value

```

#### JSON_SET(json_doc, path, val[, path, val] ...)
将数据插入JSON文档 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-modification-functions.html#function_json-set)

##### 其它对比
    JSON_SET() 替换现有值并添加不存在的值。
    
    JSON_INSERT() 插入值而不替换现有值。
    
    JSON_REPLACE()仅替换 现有值。
```
#######官网示例#########
mysql> SET @j = '{ "a": 1, "b": [2, 3]}';

# 更新a的值并新增c和对应的值
mysql> SELECT JSON_SET(@j, '$.a', 10, '$.c', '[true, false]');
+-------------------------------------------------+
| JSON_SET(@j, '$.a', 10, '$.c', '[true, false]') |
+-------------------------------------------------+
| {"a": 10, "b": [2, 3], "c": "[true, false]"}    |
+-------------------------------------------------+

# 只插入c和对应的值
mysql> SELECT JSON_INSERT(@j, '$.a', 10, '$.c', '[true, false]');
+----------------------------------------------------+
| JSON_INSERT(@j, '$.a', 10, '$.c', '[true, false]') |
+----------------------------------------------------+
| {"a": 1, "b": [2, 3], "c": "[true, false]"}        |
+----------------------------------------------------+

# 只替换a的值
mysql> SELECT JSON_REPLACE(@j, '$.a', 10, '$.c', '[true, false]');
+-----------------------------------------------------+
| JSON_REPLACE(@j, '$.a', 10, '$.c', '[true, false]') |
+-----------------------------------------------------+
| {"a": 10, "b": [2, 3]}                              |
+-----------------------------------------------------+

########更新应用#########
# 更新table表json_field字段中的值(json格式)，将 key = value2 的 更新为 key = value
update table 
set json_field = json_set(json_field,'$.key','value') 
where  json_field->"$.key" = 'value2';

```

#### JSON_DEPTH(json_doc)
获取json的深度(depth)，不支持层级，即整个json的深度 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-attribute-functions.html#function_json-depth)
```
select JSON_DEPTH(@jsonOne); #result:3

select JSON_DEPTH(@jsonTwo); #result:3

```

#### JSON_LENGTH(json_doc[, path])
获取json的长度，支持层级，默认为整个json的长度，如果有path，则对应具体层级的长度 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-attribute-functions.html#function_json-length)
```
select JSON_LENGTH(@jsonTwo);           #result:3
select JSON_LENGTH(@jsonTwo,"$.sex");   #result:2
```

#### JSON_TYPE(json_val)
返回utf8mb4指示JSON值类型的字符串。可以是对象，数组或标量类型。 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-attribute-functions.html#function_json-type)

#### JSON_SEARCH(json_doc, one_or_all, search_str[, escape_char[, path] ...])
返回JSON文档中给定字符串的路径 [官方文档](https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html#function_json-search)
```
# 查找 @jsonTwo 中 所有(all) 值包含 man 的 key
select JSON_SEARCH(@jsonTwo,'all','man'); #result:"$.sex.man"

# 支持通配符
select JSON_SEARCH(@jsonTwo,'all','%man%'); #result:["$.sex.man", "$.sex.woman"]

```

#### 其它函数 [官网文档](https://dev.mysql.com/doc/refman/5.7/en/json-function-reference.html)
| 函数   |  描述 |
| :---: | :----:|
|JSON_APPEND() | （已过时5.7.9）	将数据附加到JSON文档|
|JSON_ARRAY() |	创建JSON数组|
|JSON_ARRAY_APPEND() |	将数据附加到JSON文档|
|JSON_ARRAY_INSERT() |	插入JSON数组|
|->	 |评估路径后从JSON列返回值；等效于JSON_EXTRACT（）。|
|JSON_CONTAINS() |	JSON文档是否在路径中包含特定对象|
|JSON_CONTAINS_PATH() |	JSON文档是否在路径中包含任何数据|
|JSON_DEPTH() |	JSON文档的最大深度|
|JSON_EXTRACT() |	从JSON文档返回数据|
|->>	 |在评估路径并取消引用结果后，从JSON列返回值；等效于JSON_UNQUOTE（JSON_EXTRACT（））。|
|JSON_INSERT() |	将数据插入JSON文档|
|JSON_KEYS() |	JSON文档中的键数组|
|JSON_LENGTH() |	JSON文档中的元素数|
|JSON_MERGE() | （不建议使用5.7.22）	合并JSON文档，保留重复的键。JSON_MERGE_PRESERVE（）的已弃用同义词|
|JSON_MERGE_PATCH() |	合并JSON文档，替换重复键的值|
|JSON_MERGE_PRESERVE() |	合并JSON文档，保留重复的键|
|JSON_OBJECT() |	创建JSON对象|
|JSON_PRETTY() |	以易于阅读的格式打印JSON文档|
|JSON_QUOTE() |	引用JSON文档|
|JSON_REMOVE() |	从JSON文档中删除数据|
|JSON_REPLACE() |	替换JSON文档中的值|
|JSON_SEARCH() |	JSON文档中值的路径|
|JSON_SET() |	将数据插入JSON文档|
|JSON_STORAGE_SIZE() |	用于存储JSON文档的二进制表示形式的空间|
|JSON_TYPE() |	JSON值类型|
|JSON_UNQUOTE() |	取消引用JSON值|
|JSON_VALID() |	JSON值是否有效|

