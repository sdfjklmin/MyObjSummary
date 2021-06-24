#### 前言

datagrip 是一款数据库管理工具。[官网地址](https://www.jetbrains.com/datagrip/) ，可以使用通用的 jetbrains 账号。

#### 使用

具体使用方式请参考官网，还可以使用插件来优化管理。

#### 小错误

##### 1. This table is read-only. Unresolved table reference.

翻译: 当前表为只读，未解决表的引用|参考(reference)。

分析: 根据编辑器中的提示查找，比如 `查询中的字段颜色，某个字段颜色不同`、`有没有指定schema`、`对应表的结构有没有刷新` 等。

##### 2. [HY000][1193] Unknown system variable 'tx_isolation’错误

未知的系统变量 'tx_isolation’。

原因 -> 在Mysql8中，tx_isolation 变量已修改为 transaction_isolation

编辑器报错:
    如果 DataGrip 编辑器报错，则需要修改 `驱动方式`。
    编辑器默认会升级到最新的版本，去适配高版本的 MySQL，驱动也是一样。
    解决方式:
        降低驱动版本 | 安装自动匹配的驱动器
        点击数据库链接 -> 链接设置 -> General(Driver: Amazon Aurora MySQL | Go to Driver)

代码报错解决方式(一般来说云数据库会做兼容,请谨慎操作):
    SET transaction_isolation = 'READ-UNCOMMITTED';
        或
    SET SESSION transaction_isolation = 'READ-UNCOMMITTED';
