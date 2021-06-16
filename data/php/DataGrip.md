#### 前言

datagrip 是一款数据库管理工具。[官网地址](https://www.jetbrains.com/datagrip/) ，可以使用通用的 jetbrains 账号。

#### 使用

具体使用方式请参考官网，还可以使用插件来优化管理。

#### 小错误

##### 1. This table is read-only. Unresolved table reference.

翻译: 当前表为只读，未解决表的引用|参考(reference)。

分析: 根据编辑器中的提示查找，比如 `查询中的字段颜色，某个字段颜色不同`、`有没有指定schema`、`对应表的结构有没有刷新` 等。
