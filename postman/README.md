## [官网下载](https://www.getpostman.com/downloads/)

## [官方文档](https://learning.getpostman.com/docs/postman/launching-postman/introduction/)

## 常用快捷键
|快捷键|应用|
| --------          | :-----: |
| Ctrl + t          | 新开窗口 |
| Ctrl + w          | 关闭窗口 |
| Ctrl + s          | 保存 |
| Ctrl + l          | 跳转到URL |
| Ctrl + Enter      | 发送请求 |
| Ctrl + Tab        | 窗口切换 |
| Ctrl + Alt + c    | 开启 console 台 |
| Ctrl + Shift + f  | 内容查找 |
| Ctrl + Shift + i  | 开发工具 |

## 新建请求,点击 顶部左边的 [`+ New ▾`](https://learning.getpostman.com/docs/postman/launching-postman/newbutton/)

#### `+ New` 创建新的,具体是什么自行选择. `▾` 快捷创建
~~~~
Request(请求)  Collection(集合) Environment(环境变量)

API Document(api 文档)    Mock Server(模拟服务器)      Monitor(监听器,性能响应)

API(API,免费最多3个)
~~~~

#### 环境变量
###### 点击 `+ New` 选择 `Environment` ,  填写信息
~~~
Manager Environments (弹框)

Add Environment
   localhost (Environment Name,填写环境变量的名称)

#可添加多个
#对应的Key   #对应的vlaue     #对应的value
VARIABLE    INITIAL VALUE   CURRENT VALUE
  host       http://a.com       http://a.com
  name          tom             tom
  name2         tom2            tom2
~~~
###### 使用环境变量 ` No Environment ⓔ  ✲`(顶部右上角)
~~~
#环境选择            #眼睛图标,查看         #设置图标  
No Environment      ⓔ                   ✲
  localhost

#使用变量
{{host}}/test/tt    => http://a.com/test/tt

#在设置环境变量时,不同的环境变量名称应该保存一直,方便变量的使用
~~~

#### 全局变量(Globals) 
###### ` No Environment ⓔ  ✲`(顶部右上角),点击`眼睛图标`,点击 Globles 后面的 `Edit` 
###### 使用
~~~
{{key}}
~~~

#### 环境变量中的变量受限于环境,全局变量中的变量没有限制可以在任意环境中使用

## 发送请求
* Params
* Authorization
* Headers
* Body
* Pre-request Script
* Tests
* Settings
* Cookies
* Code

#### URL编码 decode|encode
~~~
https://postman-echo.com/:method?a=a&b=b&c=c

用鼠标选中 ? 以后的所有参数,点击右键选中 decode | encode
~~~

#### Params : 参数
###### Query Params : URL参数 (key:value)
###### Path Variables : URL占位符(:key)
~~~
https://postman-echo.com/:method
在 Params 中  Path Variables 设置 method 的值
~~~

#### Body : 发送数据体
###### none : 无参数
###### form-data : 表单数据 `Content-Type: multipart/form-data`
~~~
表单数据允许您发送键值对,并指定内容类型
~~~

###### x-www-form-urlencoded : URL编码
~~~
URL编码的数据使用与URL参数相同的编码,输入您的键值对以与请求一起发送,会在发送前对它们进行编码。
~~~

###### raw : 原始数据
~~~
您可以使用原始身体数据发送可以输入为文本的任何内容。
使用原始选项卡，然后使用类型下拉列表指示数据的格式（Text，JavaScript，JSON，HTML或XML），
Postman将启用语法突出显示并将相关标头附加到您的请求。
~~~
* Text : 文本格式 `key=value&key2=value2`
* JSON : JSON格式 `{"key":"value","key2":"value2"}`

###### binary : 二进制
~~~
您可以使用二进制数据发送无法在Postman编辑器中随请求正文手动输入的信息，
例如图像，音频和视频文件（也可以发送文本文件）
~~~

###### GraphQL : github API 格式

#### Authorization : 验证

#### Headers : 请求头信息
###### Presets(headers栏目下最后面),点击 Manager Presets 进行添加管理

#### Cookies
~~~
点击 Cookies 
Manager Cookies
  www.tt.com (输入域名名称,点击 Add )

在 www.tt.com 下,点击 Add Cookie,新增 信息
~~~