### 压力测试
* [Apache ab test](https://httpd.apache.org/docs/current/programs/ab.html)


#### [Apache ab test](https://httpd.apache.org/docs/current/programs/ab.html)
    常用命令介绍:
        -A auth-username:password
            向服务器提供BASIC身份验证凭据。用户名和密码之间用单个分隔，:并通过编码为base64的网络发送。
        -b windowsize
            TCP发送/接收缓冲区的大小，以字节为单位。
        -c concurrency
            一次执行的多个请求的数量。默认值为一次一个请求。
        -C cookie-name=value
            Cookie:在请求中添加一行。该参数通常为一 对形式。该字段是可重复的。name=value
        -h
            显示使用情况信息。
        -k
            启用HTTP KeepAlive功能，即在一个HTTP会话中执行多个请求。默认为no KeepAlive。
        -l
            如果响应的长度不是恒定的，请不要报告错误。这对于动态页面很有用。在2.4.7及更高版本中可用。
        -m HTTP-method
            请求的自定义HTTP方法。在2.4.10及更高版本中可用。
        -n requests
            为基准测试会话执行的请求数。默认设置是仅执行一个请求，这通常会导致非代表性的基准测试结果。
    输出介绍:
        #并发等级,并发数量 -c 设置
        Concurrency Level:      20
        #完成时间
        Time taken for tests:   3.217 seconds
        #完成请求数
        Complete requests:      5000
        #失败请求数
        Failed requests:        0
        #总传输
        Total transferred:      92265000 bytes
        #HTML传输
        HTML transferred:       91435000 bytes
        #每秒请求数( 请求总数 / 完成时间 )
        Requests per second:    1554.47 [#/sec] (mean)
        #单个请求的时间
        Time per request:       12.866 [ms] (mean)
        #单个请求的时间 (1s / Time per request =  Requests per second)
        Time per request:       0.643 [ms] (mean, across all concurrent requests)
        #传输速率( 总传输 / 完成时间 )
        Transfer rate:          28012.32 [Kbytes/sec] received
