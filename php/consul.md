#### [下载安装](https://www.consul.io/downloads.html)

#### 定义一个服务`Defining a Service`

###### 创建文件夹`.d后缀表示此目录包含一组配置文件`    
    make consul.d
    
###### 生成服务文件
```
echo '{"service":
  {"name": "web",
   "tags": ["rails"],
   "port": 80
  }
}' > ./consul.d/web.json

```
###### 运行服务
     #-enable-script-checks安全检测，强烈建议带上。
     consul agent -dev -enable-script-checks -config-dir=./consul.d    