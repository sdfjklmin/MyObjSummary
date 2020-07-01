## [官网地址](https://www.jetbrains.com/help/phpstorm/quick-start-guide-phpstorm.html)

## [免费申请](https://www.jetbrains.com/shop/eform/opensource)

## 具体设置选项的地方可能有版本差异，可能层级有所不同，但大体位置是相同的

## 取消代码重复提醒
    Settings -> Editor -> Inspection -> General -> duplicated code framework (去重勾选)
    
## 代码库提示
    External Libraires(项目文件夹下方) -> 点击右键 ->  Configure PHP Include Paths -> Include Path (点击+号，添加提示代码库)
    
## 命令脚本 
    Add Configuration(屏幕右上方) -> 点击+号 -> PHP Script -> 设置 Name(脚本名称), File(对应脚本地址,/tt/tt.php), Arguments(对应参数,start),
    运行成功后在左下角有 Run 的信息
    
## 设置PHP版本号
    Settings -> Languages && Framworks -> PHP(点击选中,可以修改版本号) --版本号无法选择--> 点击PHP下的Composer -> IDE Setting with composer.json (取消勾选)
    
## [插件](https://plugins.jetbrains.com/phpstorm)
    Settings -> Plugins(插件) -> PHP Annotation(注解)(search and install) -> resart IDE
    
    #搜索背景图片
    Background Image Plus
    View -> Set Background Image
    #便捷方式
    按两次 shift （或者 Ctrl +shift+A） 输入 Set Background Image 命令
    
    选择图片(弹窗可能不够图片显示,可以自己拉伸) -> 设置透明度 -> 保存
    
## 内置请求
    Tools ->  Http Client -> Test RESTFul Web Service

#### 内置请求文件快捷操作
    新建 name.http 文件(api格式)
    命令 : 
         gtr  => get request(普通get请求)
         gtrp => get request params(普通get请求带参数)
         http|https =>  请求
         
         ptr  => post request(普通的post请求)
         ptrp => post request params(带参数请求 id=1&name=11)
         fptr => file post request (文件格式请求)
         mptr => multipart post request(post请求,带参数)
               --WebAppBoundary
               Content-Disposition: form-data; name="field-name"
               
               field-value
               --WebAppBoundary--
               
    配置 : 新建 http-client.env.json ，配置 development
        {
          "development": {
            "host": "localhost:9501",
            "username": "sjm",
            "password": "123456",
            "api-tt" : "index/ttPost",
            "api-tt-params": "id=11&name=sjm&pwd=123456",
            "api-t": {
              "name" : "tt"
            }
          }
        }               
## 连接远程服务器
    Tools -> Deployment -> 新增(configuration -> +) | 浏览(browse remote host -> 点击 Remote Host 中的 ... )
    
## 快捷使用
    
#### Alt+Shift+Enter(快速生成)
    把光标指向要自动生成的代码上,使用快捷键快速生成,可生成(对应使用的命名空间,对应类的属性,方法等)  

#### Alt+Enter(自定义完善对应的属性,方法,类等)
    把光标指向要自动生成的代码上,使用快捷键快速生成,可生成(对应使用的命名空间,对应类的属性,方法等)

#### Alt+INS
    文件中使用(快速生成 构造函数，属性等)
    目录使用(快速生成文件)

#### Alt+Enter
    快速生成当前方法的注释      
    快速改变当前方法的属性，public private protected。
    
#### Ctrl+ Alt +Shift + 2 (快速打开当前文件夹所在的路径)
    快速打开当前文件夹所在的路径

#### File -> New Scratch File(生成[抹掉,忽略]文件)
    生成[抹掉,忽略]文件
    
    
#### Ctrl + 减号(- 或者 +)  
     逐层折叠(展开)代码
     
     
#### Ctrl +  Shift +  减号(- 或者 +)     
    全部折叠(展开)代码
    可以先 Ctrl + Shift + 减号,全部折叠,然后使用 Ctrl + 加号,查看各个方法
    
#### Ctrl + Alt + L (快速格式代码)
    Settings -> Editor -> Code Style -> PHP->( Wrapping(包装) and Braces(大括号) 
        #设置 等号 对齐
        -> Assignment(分配) statemnet(申明) -> `勾选`  Align(对齐) consecutive(连续) assignments
        #数组设置 多行对齐, key=>value 对齐
        -> Array initializer ->  `勾选` Align when multiline , Align key-value pairs
        )
        
#### Ctrl + w (快速选中文本,可重复按 w ,逐层选择)

#### Ctrl + b (快速进入对应的类,方法,属性)
    
#### Ctrl + F4 (关闭当前窗口)

#### Ctrl + TAB (窗口切换)

#### Ctrl + End (跳转到全文文本最末尾)

#### Ctrl + Home (跳转到全文文本开头)

#### Ctrl + 左右键[< >] (跳转至当前文本的开头或结尾)

#### Ctrl + Shift + 左右键[< >] (选中当前文本)

#### Shift + DEl (删除当前行)

#### Shift + Enter (跳转到新生成的一行)
## 编辑器卡顿
~~~
phpstorm是由java编写而成，可以通过配置参数来进行优化
1.编辑配置 PhpStorm-192.7142.41/bin/phpstorm.vmoptions 新增如下内容
-Dawt.usesystemAAFontSettings=lcd
-Dawt.java2d.opengl=true

2.提高配置文件中所配置的内存大小【不推荐】

3.优化内存配置 `Xms -> 初始堆大小M`, `Xmx -> 最大堆大小M`, `XX:MaxPermSize -> 设置持久代最大值M`
-Xms256m
-Xmx2048m
-XX:MaxPermSize=350m
~~~