## [官网地址](https://www.jetbrains.com/help/phpstorm/quick-start-guide-phpstorm.html)

## 具体设置选项的地方可能有版本差异，可能层级有所不同，但大体位置是相同的

## 取消代码重复提醒
    Settings -> Editor -> Inspection -> General -> duplicated code framework (去重勾选)
    
## 代码库提示
    External Libraires(项目文件夹下方) -> 点击右键 ->  Configure PHP Include Paths -> Include Path (点击+号，条件提示代码库)
    
## 命令脚本 
    Add Configuration(屏幕右上方) -> 点击+号 -> PHP Script -> 设置 Name(脚本名称), File(对应脚本地址,/tt/tt.php), Arguments(对应参数,start),
    运行成功后在左下角有 Run 的信息
    
## 设置PHP版本号
    Settings -> Languages && Framworks -> PHP(点击选中,可以修改版本号) --版本号无法选择--> 点击PHP下的Composer -> IDE Setting with composer.json (取消勾选)
    
## 插件
    Settings -> Plugins(插件) -> PHP Annotation(注解)(search and install) -> resart IDE
    
    #搜索背景图片
    Background Image Plus
    View -> Set Background Image
    
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

#### File -> New Scratch File(生成[抹掉,忽略]文件)