## [Swoole 新版文档 ☺](https://wiki.swoole.com/#/)

#### 快速入门
* 和之前没多大差别, 请移步[official](../official)

#### TCP/UDP Server



//连接: $type = 4,$data = '';
//发送: $type = 0,$data = "数据";
//退出: $type = 3,$data = '';
$type 数据的类型，0 表示来自客户端的数据发送，4 表示客户端连接关闭，5 表示客户端连接建立
