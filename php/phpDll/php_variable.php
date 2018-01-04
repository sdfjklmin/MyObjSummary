<?php
// php变量在内核中的发现

// 1.变量的类型
// web:http://www.cunmou.com/phpbook/2.1.md
// PHP在内核中是通过zval这个结构体来存储变量的，它的定义在Zend/zend.h文件里，简短精炼，只有四个成员组成：
struct _zval_struct {
    zvalue_value value; /* 变量的值 */
    zend_uint refcount__gc;
    zend_uchar type;    /* 变量当前的数据类型 */
    zend_uchar is_ref__gc;
};
typedef struct _zval_struct zval;
 
//在Zend/zend_types.h里定义的：
typedef unsigned int zend_uint;
typedef unsigned char zend_uchar;
        
// zval里的refcout__gc是zend_uint类型，也就是unsigned int型，is_ref__gc和type则是unsigned char型的。

// 保存变量值的value则是zvalue_value类型(PHP5)，它是一个union，同样定义在了Zend/zend.h文件里：

typedef union _zvalue_value {
    long lval;  /* long value */
    double dval;    /* double value */
    struct {
        char *val;
        int len;
    } str;
    HashTable *ht;  /* hash table value */
    zend_object_value obj;
} zvalue_value;

// php实现的8中数据类型,具体内核实现请参考web
IS_NULL  第一次初始化如果变量没有赋值,则会自动的被赋予这个常量
IS_BOOL  true 和 false 
IS_LONG  可以存储从 -2147483648 到 +2147483647 范围内的任一整数 ,如果超出不会溢出,会自动转换为 IS_DOUBLE
IS_DOUBLE 	浮点数
IS_STRING 	字符串,内核会保存字符自身的长度,储存的时候 自身长度+1 ,
IS_ARRAY    聚集别的变量,它可以承载任意类型的数据,通过 hash table(哈希表) 来实现
IS_OBJECT   对象也是用来存储复合数据的,但是与数组不同的是,对象还需要保存以下信息:方法,访问权限,类常量以及其它的处理逻辑
IS_RESOURCE  一种资源,一些无法呈现给php用户的数据,比如数据库连接
             var_dump(@mysql_connect('127.0.0.1' . ':' . '3306', 'root', '123456'))


@var_dump(is_null($undefined)) ;
var_dump(is_bool(false))	;
var_dump(is_long(223)) ;
var_dump(is_double(232.23)) ;
var_dump(is_string('23')) ;
var_dump(is_array([])) ;
@var_dump(is_object(new PDO('mysql:host=127.0.0.1;dbname=lcm;charset=UTF-8', 'root', '123456'))) ;
var_dump(is_resource(@mysql_connect('127.0.0.1' . ':' . '3306', 'root', '123456'))) ;


zval结构体里的type成员的值便是以上某个IS_*常量之一。 内核通过检测变量的这个成员值来知道他是什么类型的数据并做相应的后续处理。