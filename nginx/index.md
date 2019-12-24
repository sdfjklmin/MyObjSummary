# 反向代理
代码说明

```
# 至少需要一个 Hyperf 节点，多个配置多行
upstream hyperf {
    # Hyperf HTTP Server 的 IP 及 端口
    server 127.0.0.1:9501;
    server 127.0.0.1:9502;
}

server {
    # 监听端口
    listen 80; 
    # 绑定的域名，填写您的域名
    server_name proxy.hyperf.io;

    location / {
        # 将客户端的 Host 和 IP 信息一并转发到对应节点  
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

        # 执行代理访问真实服务器
        proxy_pass http://hyperf;
    }
}
```

错误信息
* "proxy_pass" directive is not allowed : proxy_pass 位置错误。

```
http {
   upstream name1 {

   } 
   server {
        location / {
            proxy_pass http://name1;
        }
   }
}


```

# PHP模块配置

fastcgi

```
server {

    location ~ \.php$ {

        #指定网页根目录
        root           /data/www/default.com;

        #指定fastcgi的地址和端口
        #Nginx和PHP-FPM的进程间通信有两种方式,一种是TCP,一种是UNIX Domain Socket.
        #其中TCP是IP加端口,可以跨服务器.而UNIX Domain Socket不经过网络,只能用于Nginx跟PHP-FPM都在同一服务器的场景。
        #502 配置错误|php-fpm未启动 因为 nginx 找不到php-fpm了，所以报错。
        #   一般是fastcgi_pass后面的路径配置错误了，后面可以是socket或者是ip:port
        #   修改php-fpm.conf的配置文件里面的 listen = /tmp/php-fcgi.sock改为listen = 127.0.0.1:9000，
        #   如果php-fpm.conf中找不到listen = 127.0.0.1:9000就在文件末尾添加，然后重启
        #   修改nginx的配置文件nginx.conf里面的 fastcgi_pass unix:/tmp/php-fcgi.sock; 改为 fastcgi_pass 127.0.0.1:9000;
        fastcgi_pass   127.0.0.1:9000;
        # 或者
        fastcgi_pass   unix:/tmp/php-cgi.sock;

        #默认页面
        fastcgi_index  index.php;

        #fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
        #配置fastcgi参数 $document_root指向的是 root设置的地址
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

        #引入fastcgi参数
        include        fastcgi_params;
    }
}
```

# Nginx与PHP运行原理

#### 名词说明

##### CGI
    common gateway interface (公共网关接口)

    请求模式:
        Web Brower(浏览器) ----(通过http协议传输)----> Http Server(服务器nginx/apache) -----> CGI Program -----> Db

	Server 与 CGI 通过 STDIN/STDOUT(标准的输入/输出)进行数据传递
	nginx(动态加载模块) apache(指定加载模块)

##### CGI工作原理
	每当客户请求CGI的时候，WEB服务器就请求操作系统生成一个新的CGI解释器进程(如php-cgi.exe)，
	CGI 的一个进程则处理完一个请求后退出，下一个请求来时再创建新进程。
	当然，这样在访问量很少没有并发的情况也行。可是当访问量增大，并发存在，这种方式就不 适合了。于是就有了fastcgi。

##### FastCGI
	像是一个常驻(long-live)型的CGI，它可以一直执行着，只要激活后，
	不会每次都要花费时间去fork一次（这是CGI最为人诟病的fork-and-execute 模式）。
    
    一般情况下，FastCGI的整个工作流程是这样的：
        1.Web Server启动时载入FastCGI进程管理器（IIS ISAPI或Apache Module)
        2.FastCGI进程管理器自身初始化，启动多个CGI解释器进程(可见多个php-cgi)并等待来自Web Server的连接。
        3.当客户端请求到达Web Server时，FastCGI进程管理器选择并连接到一个CGI解释器。 Web server将CGI环境变量和标准输入发送到FastCGI子进程php-cgi。
        4.FastCGI 子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。
          当FastCGI子进程关闭连接时， 请求便告处理完成。
          FastCGI子进程接着等待并处理来自FastCGI进程管理器(运行在Web Server中)的下一个连接。 
          在CGI模式中，php-cgi在此便退出了。

##### php-fpm(PHP内置的一种fast-cgi)  
    php-fpm即php-Fastcgi Process Manager.
    php-fpm是 FastCGI 的实现，并提供了进程管理的功能。
    进程包含 master 进程和 worker 进程两种进程。
    master 进程只有一个，负责监听端口，接收来自 Web Server 的请求，而 worker 进程则一般有多个(具体数量根据实际需要配置)，
    每个进程内部都嵌入了一个 PHP 解释器，是 PHP 代码真正执行的地方。

##### 请求步骤
    Web Brower(浏览器访问) www.example.com
    |
            |
       通过http协议传输  
    |
            |
        http server
     (服务器nginx/apache)            
    |
            |
         配置解析    
    路由到 www.example.com/index.php
    |
            |
    加载 nginx 的 fast-cgi 模块
    |
            |
    fast-cgi 监听 127.0.0.1:9000 地址
    通过 fast-cgi 协议将请求转发给 php-fpm 处理
    |
            |
    请求到达 127.0.0.1:9000
    |
            |
    php-fpm 监听 127.0.0.1:9000
    可通过 php-fpm.conf 进行修改
    (php-fpm 是一个多进程的 fast-cgi 管理程序)
    |
            |
    php-fpm 接收到请求，启用 worker 进程处理请求
    (worker进程 会抢占式的获得 cgi 请求进行处理)
    |
            |
        php-fpm 处理请求
    |       
            |
         处理详解
            |
    |处理过程:等待 php 脚本的解析,等待业务处理的结果返回,
    |       完成后回收子进程,这整个的过程是阻塞等待的.
    |处理弊端:也就意味着 php-fpm 的进程数有多少能处理的请求也就是多少,
    |       假设 php-fpm 有 200 个 worker进程,一个请求将耗费 1 秒的时间,
    |       那么简单的来说整个服务器理论上最多可以处理的请求也就是 200 个,QPS 即为 200/s.
    |       在高并发的场景下，这样的性能往往是不够的，尽管可以利用 nginx 作为负载均衡配合多台 php-fpm 服务器来提供服务，
    |       但由于 php-fpm 的阻塞等待的工作模型，一个请求会占用至少一个 MySQL 连接，
    |       多节点高并发下会产生大量的 MySQL 连接，而 MySQL 的最大连接数默认值为 100，尽管可以修改，
    |       但显而易见该模式没法很好的应对高并发的场景
            |
    |   PHP生命周期(宏都是在walu.c中)           
    |              前置初始化(Apache或Nginx相关操作)
    |              模块初始化       对应扩展 php.dll
    |              请求初始化       $_SERVER等参数    [  
    |      frame   执行php脚本      code                可以重复执行(一般为框架内容)
    |              请求处理完成      request          ]
    |              关闭模块         close
    |
           |
        其它内容   
    |
           |         
    nginx 将结果通过 http 返回给浏览器