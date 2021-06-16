#### 1. sum() `计数`

#### 2. left(str, len) `字符串截取`
    select left('123456789',3); 
    #123

#### 3. round(decimal, int) `保留几位小数，向上取整`
    select round(1.2345,3);
    #1.235

#### 4. concat(str*) `字符串连接`
    select concat('a','b','c');
    #abc

#### 5. find_in_set(str, strList) `str是否存在strList中`
    select FIND_IN_SET('search','a,search,c');
    #2