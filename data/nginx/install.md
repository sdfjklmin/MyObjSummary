#### 直接安装
    sudo apt install -y nginx
    yum install -y nginx 
    
#### 1.必备软件和一些问题
    a.安装 pcre 为了支持 rewrite 功能，我们需要安装 pcre

        #yum install pcre* //如过你已经装了，请跳过这一步

        yum -y install pcre-devel (建议)

    b.安装 openssl 需要 ssl 的支持，如果不需要 ssl 支持，请跳过这一步

        #yum install openssl*

        yum -y install openssl openssl-devel (建议)

    c. C compiler cc is not found
    
         yum -y install gcc-c++    

    d.nginx cache purge 清缓存模块安装(可选)
        
        wget http://labs.frickle.com/files/ngx_cache_purge-1.3.tar.gz
        wget http://labs.frickle.com/files/ngx_cache_purge-2.3.tar.gz
     
        (当前包与nginx不兼容,下载最新包即可)
        no ngx_cache_purge-1.3/./configure/config was found

    e.nginx: [error] invalid PID number "" in "/usr/local/nginx/logs/nginx.pid"

        当前nginx.pid没有数据,解决如下
        ps aux | grep nginx
        echo 91761 >> nginx.pid
        重启

 #### 2.安装
    a.下载 wget http://nginx.org/download/nginx-1.16.0.tar.gz (自行选择版本,建议最新的稳定版本),自行解压.

    b.进入对应文件夹运行
         (旧版本)./configure --prefix=/usr/local/nginx --with-http_ssl_module --with-http_spdy_module --with-http_stub_status_module --with-pcre
         (新版本)
             ./configure --prefix=/usr/local/nginx --with-http_ssl_module --with-http_v2_module --with-http_stub_status_module --with-pcre --with-http_realip_module --add-module=../ngx_cache_purge-2.3

         spdy : 用以最小化网络延迟，提升网络速度，优化用户的网络使用体验
         stub_status : 支出nginx 状态查询,新版已经没有 http_spdy_module ,新增 http_v2_module
         realip : 模块（将用户 IP 转发给后端服务器）
         ngx_cache_purge : 添加缓存清除扩展模块(文件和nginx同级目录)

    c.成功提示
        Configuration summary
          + using system PCRE library
          + using system OpenSSL library
          + using system zlib library

        nginx path prefix: "/usr/local/nginx"
        nginx binary file: "/usr/local/nginx/sbin/nginx"
        nginx modules path: "/usr/local/nginx/modules"
        nginx configuration prefix: "/usr/local/nginx/conf"
        nginx configuration file: "/usr/local/nginx/conf/nginx.conf"
        nginx pid file: "/usr/local/nginx/logs/nginx.pid"
        nginx error log file: "/usr/local/nginx/logs/error.log"
        nginx http access log file: "/usr/local/nginx/logs/access.log"
        nginx http client request body temporary files: "client_body_temp"
        nginx http proxy temporary files: "proxy_temp"
        nginx http fastcgi temporary files: "fastcgi_temp"
        nginx http uwsgi temporary files: "uwsgi_temp"
        nginx http scgi temporary files: "scgi_temp"

    d.编译

        make && make install
    e.
        启动服务:
            /usr/local/nginx/sbin/nginx
        关闭服务:
            /usr/local/nginx/sbin/nginx -s stop
        平滑重启:
            /usr/local/nginx/sbin/nginx -s reload
        查看进程对应的id:
            cat /usr/local/nginx/logs/nginx.pid
        查看nginx服务:
            ps -ef | grep nginx
            ps aux | grep nginx
        查看版本 :
            /usr/local/nginx/sbin/nginx -v
        查看版本和模块信息 :
            /usr/local/nginx/sbin/nginx -V
        配置文件检测 :
            /usr/local/nginx/sbin/nginx –t    //看到 ok 和 successful,说明配置文件没问题

    f.测试

         curl -s http://localhost | grep nginx.com

 #### 3.参数详解(部分参数由于版本原因可能已弃用,请参照官网进行配置)
         –prefix= 指向安装目录
         –sbin-path 指向（执行）程序文件（nginx）
         –conf-path= 指向配置文件（nginx.conf）
         –error-log-path= 指向错误日志目录
         –pid-path= 指向 pid 文件（nginx.pid）
         –lock-path= 指向 lock 文件（nginx.lock）（安装文件锁定，防止安装文件被别人利用，或自己误操作。）
         –user= 指定程序运行时的非特权用户
         –group= 指定程序运行时的非特权用户组
         –builddir= 指向编译目录
         –with-rtsig_module 启用 rtsig 模块支持（实时信号）
         –with-select_module 启用 select 模块支持（一种轮询模式,不推荐在高载环境下使用）禁用：–with-outselect_module
         –with-poll_module 启用 poll 模块支持（功能与 select 相同，与 select 特性相同，为一种轮询模式,不推荐在 高载环境下使用）
         –with-file-aio 启用 file aio 支持（一种 APL 文件传输格式）
         –with-ipv6 启用 ipv6 支持 –with-http_ssl_module 启用 ngx_http_ssl_module 支持（使支持 https 请求，需已安装 openssl）
         –with-http_realip_module 启用 ngx_http_realip_module 支持（这个模块允许从请求标头更改客户端的 IP 地 址值，默认为关）
         –with-http_addition_module 启用 ngx_http_addition_module 支持（作为一个输出过滤器，支持不完全缓冲， 分部分响应请求）
         –with-http_xslt_module 启用 ngx_http_xslt_module 支持（过滤转换 XML 请求）
         –with-http_image_filter_module 启用 ngx_http_image_filter_module 支持（传输 JPEG/GIF/PNG 图片的一个 过滤器）（默认为不启用。gd 库要用到）
         –with-http_geoip_module 启用 ngx_http_geoip_module 支持（该模块创建基于与 MaxMind GeoIP 二进制文件相 配的客户端 IP 地址的 ngx_http_geoip_module 变量）
         –with-http_sub_module 启用 ngx_http_sub_module 支持（允许用一些其他文本替换 nginx 响应中的一些文本）
         –with-http_dav_module 启用 ngx_http_dav_module 支持（增加 PUT,DELETE,MKCOL：创建集合,COPY 和 MOVE 方 法）默认情况下为关闭，需编译开启
         –with-http_flv_module 启用 ngx_http_flv_module 支持（提供寻求内存使用基于时间的偏移量文件）
         –with-http_gzip_static_module 启用 ngx_http_gzip_static_module 支持（在线实时压缩输出数据流）
         –with-http_random_index_module 启用 ngx_http_random_index_module 支持（从目录中随机挑选一个目录索 引）
         –with-http_secure_link_module 启用 ngx_http_secure_link_module 支持（计算和检查要求所需的安全链接网 址）
         –with-http_degradation_module  启用 ngx_http_degradation_module 支持（允许在内存不足的情况下返回 204 或 444 码）
         –with-http_stub_status_module 启用 ngx_http_stub_status_module 支持（获取 nginx 自上次启动以来的工作 状态）
         –without-http_charset_module 禁用 ngx_http_charset_module 支持（重新编码 web 页面，但只能是一个方向 –服务器端到客户端，并且只有一个字节的编码可以被重新编码）
         –without-http_gzip_module 禁用 ngx_http_gzip_module 支持（该模块同-with-http_gzip_static_module 功能 一样）
         –without-http_ssi_module 禁用 ngx_http_ssi_module 支持（该模块提供了一个在输入端处理处理服务器包含 文件（SSI）的过滤器，目前支持 SSI 命令的列表是不完整的）
         –without-http_userid_module 禁用 ngx_http_userid_module 支持（该模块用来处理用来确定客户端后续请求 的 cookies）
         –without-http_access_module 禁用 ngx_http_access_module 支持（该模块提供了一个简单的基于主机的访问 控制。允许/拒绝基于 ip 地址）
         –without-http_auth_basic_module 禁用 ngx_http_auth_basic_module（该模块是可以使用用户名和密码基于 http 基本认证方法来保护你的站点或其部分内容）
         –without-http_autoindex_module 禁用 disable ngx_http_autoindex_module 支持（该模块用于自动生成目录 列表，只在 ngx_http_index_module 模块未找到索引文件时发出请求。）
         –without-http_geo_module 禁用 ngx_http_geo_module 支持（创建一些变量，其值依赖于客户端的 IP 地址）
         –without-http_map_module 禁用 ngx_http_map_module 支持（使用任意的键/值对设置配置变量）
         –without-http_split_clients_module 禁用 ngx_http_split_clients_module 支持（该模块用来基于某些条件 划分用户。条件如：ip 地址、报头、cookies 等等）
         –without-http_referer_module 禁用 disable ngx_http_referer_module 支持（该模块用来过滤请求，拒绝报 头中 Referer 值不正确的请求）
         –without-http_rewrite_module 禁用 ngx_http_rewrite_module 支持（该模块允许使用正则表达式改变 URI，并 且根据变量来转向以及选择配置。如果在 server 级别设置该选项，那么他们将在 location 之前生效。如果在 location 还有更进一步的重写规则，location 部分的规则依然会被执行。如果这个 URI 重写是因为 location 部 分的规则造成的，那么 location 部分会再次被执行作为新的 URI。 这个循环会执行 10 次，然后 Nginx 会返回一 个 500 错误。）
         –without-http_proxy_module 禁用 ngx_http_proxy_module 支持（有关代理服务器）
         –without-http_fastcgi_module 禁用 ngx_http_fastcgi_module 支持（该模块允许 Nginx 与 FastCGI 进程交 互，并通过传递参数来控制 FastCGI 进程工作。 ）FastCGI 一个常驻型的公共网关接口。
         –without-http_uwsgi_module 禁用 ngx_http_uwsgi_module 支持（该模块用来医用 uwsgi 协议，uWSGI 服务器相 关）
         –without-http_scgi_module 禁用 ngx_http_scgi_module 支持（该模块用来启用 SCGI 协议支持，SCGI 协议是 CGI 协议的替代。它是一种应用程序与 HTTP 服务接口标准。它有些像 FastCGI 但他的设计 更容易实现。）
         –without-http_memcached_module 禁用 ngx_http_memcached_module 支持（该模块用来提供简单的缓存，以提 高系统效率）
         -without-http_limit_zone_module 禁用 ngx_http_limit_zone_module 支持（该模块可以针对条件，进行会话的 并发连接数控制）
         –without-http_limit_req_module 禁用 ngx_http_limit_req_module 支持（该模块允许你对于一个地址进行请 求数量的限制用一个给定的 session 或一个特定的事件）
         –without-http_empty_gif_module 禁用 ngx_http_empty_gif_module 支持（该模块在内存中常驻了一个 1*1 的 透明 GIF 图像，可以被非常快速的调用）
         –without-http_browser_module 禁用 ngx_http_browser_module 支持（该模块用来创建依赖于请求报头的值。 如果浏览器为 modern ，则$modern_browser 等于 modern_browser_value 指令分配的值；如 果浏览器为 old，则 $ancient_browser 等于 ancient_browser_value 指令分配的值；如果浏览器为 MSIE 中的任意版本，则 $msie 等 于 1）
         –without-http_upstream_ip_hash_module 禁用 ngx_http_upstream_ip_hash_module 支持（该模块用于简单的 负载均衡）
         –with-http_perl_module 启用 ngx_http_perl_module 支持（该模块使 nginx 可以直接使用 perl 或通过 ssi 调 用 perl）
         –with-perl_modules_path= 设定 perl 模块路径 –with-perl= 设定 perl 库文件路径 –http-log-path= 设定 access log 路径 –http-client-body-temp-path= 设定 http 客户端请求临时文件路径 –http-proxy-temp-path= 设定 http 代理临时文件路径 –http-fastcgi-temp-path= 设定 http fastcgi 临时文件路径 –http-uwsgi-temp-path= 设定 http uwsgi 临时文件路径 –http-scgi-temp-path= 设定 http scgi 临时文件路径
         -without-http 禁用 http server 功能
         –without-http-cache 禁用 http cache 功能
         –with-mail 启用 POP3/IMAP4/SMTP 代理模块支持
         –with-mail_ssl_module 启用 ngx_mail_ssl_module 支持 –without-mail_pop3_module 禁用 pop3 协议（POP3 即邮局协议的第 3 个版本,它是规定个人计算机如何连接到互 联网上的邮件服务器进行收发邮件的协议。是因特网电子邮件的第一个离线协议标 准,POP3 协议允许用户从服务 器上把邮件存储到本地主机上,同时根据客户端的操作删除或保存在邮件服务器上的邮件。POP3 协议是 TCP/IP 协 议族中的一员，主要用于 支持使用客户端远程管理在服务器上的电子邮件）
         –without-mail_imap_module 禁用 imap 协议（一种邮件获取协议。它的主要作用是邮件客户端可以通过这种协 议从邮件服务器上获取邮件的信息，下载邮件等。IMAP 协议运行在 TCP/IP 协议之上， 使用的端口是 143。它与 POP3 协议的主要区别是用户可以不用把所有的邮件全部下载，可以通过客户端直接对服务器上的邮件进行操 作。）
         –without-mail_smtp_module 禁用 smtp 协议（SMTP 即简单邮件传输协议,它是一组用于由源地址到目的地址传送 邮件的规则，由它来控制信件的中转方式。SMTP 协议属于 TCP/IP 协议族，它帮助每台计算机在发送或中转信件时 找到下一个目的地。）
         –with-google_perftools_module 启用 ngx_google_perftools_module 支持（调试用，剖析程序性能瓶颈）
         –with-cpp_test_module 启用 ngx_cpp_test_module 支持 –add-module= 启用外部模块支持
         –with-cc= 指向 C 编译器路径
         –with-cpp= 指向 C 预处理路径
         –with-cc-opt= 设置 C 编译器参数（PCRE 库，需要指定–with-cc-opt=”-I /usr/local/include”，如果使用 select()函数则需要同时增加文件描述符数量，可以通过–with-cc- opt=”-D FD_SETSIZE=2048”指定。）
         –with-ld-opt= 设置连接文件参数。（PCRE 库，需要指定–with-ld-opt=”-L /usr/local/lib”。）
         –with-cpu-opt= 指定编译的 CPU，可用的值为: pentium, pentiumpro, pentium3, pentium4, athlon, opteron, amd64, sparc32, sparc64, ppc64
         –without-pcre 禁用 pcre 库
         –with-pcre 启用 pcre 库
         –with-pcre= 指向 pcre 库文件目录
         –with-pcre-opt= 在编译时为 pcre 库设置附加参数
         –with-md5= 指向 md5 库文件目录（消息摘要算法第五版，用以提供消息的完整性保护）
         –with-md5-opt= 在编译时为 md5 库设置附加参数
         –with-md5-asm 使用 md5 汇编源
         –with-sha1= 指向 sha1 库目录（数字签名算法，主要用于数字签名）
         –with-sha1-opt= 在编译时为 sha1 库设置附加参数
         –with-sha1-asm 使用 sha1 汇编源
         –with-zlib= 指向 zlib 库目录
         –with-zlib-opt= 在编译时为 zlib 设置附加参数
         –with-zlib-asm= 为指定的 CPU 使用 zlib 汇编源进行优化，CPU 类型为 pentium, pentiumpro
         –with-libatomic 为原子内存的更新操作的实现提供一个架构
         –with-libatomic= 指向 libatomic_ops 安装目录
         –with-openssl= 指向 openssl 安装目录
         –with-openssl-opt 在编译时为 openssl 设置附加参数
         –with-debug 启用 debug 日志
#### 4.内核优化
     vi /etc/sysctl.conf
    参数如下 : 
        net.ipv4.netfilter.ip_conntrack_tcp_timeout_established = 1800
        net.ipv4.ip_conntrack_max = 16777216 #如果使用默认参数,容易出现网络丢包
        net.ipv4.netfilter.ip_conntrack_max = 16777216 # 如果使用默认参数,容易出现网络丢包
        net.ipv4.tcp_max_syn_backlog = 65536
        net.core.netdev_max_backlog = 32768
        net.core.somaxconn = 32768
        net.core.wmem_default = 8388608
        net.core.rmem_default = 8388608
        net.core.rmem_max = 16777216
        net.core.wmem_max = 16777216
        net.ipv4.tcp_timestamps = 0
        net.ipv4.tcp_synack_retries = 2
        #net.ipv4.tcp_syn_retries = 1
        net.ipv4.tcp_tw_recycle = 1
        net.ipv4.tcp_tw_reuse = 1
        net.ipv4.tcp_mem = 94500000 915000000 927000000
        net.ipv4.tcp_max_orphans = 3276800
        net.ipv4.ip_local_port_range = 1024 65535
    配置生效 :
        sysctl –p
    修改 iptables 启动脚本,在 star()函数里面加上(没有就不用操作)
        # vi /etc/init.d/iptables
        /sbin/sysctl -p

#### 5.配置PHP(详情PHP部分)

#### 6.nginx配置多个虚拟主机(多server,详见配置)

#### 7.location 配置
    语法规则： location [=|~|~*|^~] /uri/ { … }
    = 表示精确匹配,这个优先级也是最高的
    ^~ 表示 uri 以某个常规字符串开头，理解为匹配 url 路径即可。nginx 不对 url 做编码，因此请求为 /static/20%/aa，可以被规则^~ /static/ /aa 匹配到（注意是空格）。
    ~  表示区分大小写的正则匹配
    ~* 表示不区分大小写的正则匹配(和上面的唯一区别就是大小写)
    !~和!~*分别为区分大小写不匹配及不区分大小写不匹配的正则
    / 通用匹配，任何请求都会匹配到，默认匹配.

    示例 :
    location ~* .*\.(js|css)?$ {
          expires 7d; //7 天过期
          access_log off; //不保存日志
    }

    location ~* .*\.(png|jpg|gif|jpeg|bmp|ico)?$ {
          expires 7d;
          access_log off;
    }

    location ~* .*\.(zip|rar|exe|msi|iso|gho|mp3|rmvb|mp4|wma|wmv|rm)?$
    {
        deny all; //禁止这些文件下载，大家可以根据自己的环境来配置
    }

#### 8.文件路径配置 root 和 alias (两者分别以不同的方式将请求映射到 服务器文件上)
    [root]
        语法：root path
        默认值: root html
        配置段: http、server、location、if
        示例:
            [info]root 会根据完整的 URI 请求来映射，也就是/path/uri
    [alias]
        语法：alias path
        配置段：location
        注意 : 目录名后面一定要加 " / "


    eg:
        站点: tt.com
        访问路径 : tt.com/logs/error.log

            root : (目录)
            location ~ ^/logs/ {
                root /data/www/tt.com;
                autoindex on;
                auth_basic            "Restricted";
                auth_basic_user_file  passwd/weblogs;
            }
            root服务器地址:
            /data/www/tt.com/logs/error.log

            alias : (把当前匹配到的目录指向到指定的目录)
            location ~ ^/logs/ {
                alias /data/error/ ;
            }
            alias服务器地址:
            /data/error/logs/error.log

#### 9.日志切割
    1. 定义日志轮滚策略
        # vim nginx-log-rotate 添加如下内容
        /usr/local/nginx/logs/*.log {
        daily
        rotate 5
        missingok
        notifempty
        sharedscripts
        postrotate
            if [ -f /usr/local/nginx/logs/nginx.pid ]; then
                kill -USR1 `cat /usr/local/nginx/logs/nginx.pid`
            fi
        endscript
        }

    2.定时执行
        让logrotate每天进行一次滚动,在crontab中添加一行定时脚本。
        #crontab -e
        59 23 * * *  /usr/sbin/logrotate -f /[path:对应文件的地址]

    3.说明
        daily：日志文件每天进行滚动
        rotate：保留最5次滚动的日志
        notifempty：日志文件为空不进行滚动
        sharedscripts：运行postrotate脚本
        下面是一个脚本
        postrotate
            if [ -f /usr/local/nginx/logs/nginx.pid ]; then
                kill -USR1 `cat /usr/local/nginx/logs/nginx.pid`
            fi
        endscript
        脚本让nginx重新生成日志文件

#### 10.重写规则rewrite

#### 11.nginx 逻辑运算

#### 12.CDN调度

#### 13.PHP 安全配置

    a. 使用 open_basedir限制虚拟主机跨目录访问
        [HOST=www.ttlsa.com]
        open_basedir=/data/site/www.ttlsa.com/:/tmp/

        [HOST=test.ttlsa.com]
        open_basedir=/data/site/test.ttlsa.com/:/tmp/

        若没有 www.ttlsa.com 和 test.ttlsa.com 可相互访问,有木马风险!

    b.禁用不安全 PHP函数
        disable_functions = show_source,system,shell_exec,passthru,exec,popen,proc_open,proc_get_status,phpin

    c.php用户只读

    d.关闭 php错误日志
      display_errors = On 改为 display_errors = Off

    e.上传分离
      举个例子： php 站点 www.ttlsa.com，目录/data/site/www.ttlsa.com
      静态文件站点 static.ttlsa.com，目录/data/site/static.ttlsa.com
      文件直接被传到了/data/site/static.ttlsa.com，上传的文件无法通过 www.ttlsa.com 来访问，
      只能使用 static.ttlsa.com 访问，但是 static.ttlsa.com 不支持 php.

    f.关闭 php信息
      expose_php = On 改为 expose_php = Off

    g. 禁止动态加载链接库
      disable_dl = On; 改为 enable_dl = Off;

    h.禁用打开远程 url
      allow_url_fopen = On 改为 allow_url_fopen = Off

      file_get_contents("http://www.baidu.com/") 无法访问

#### 14.nginx tcp配置