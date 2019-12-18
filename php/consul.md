## GettingStart

### [下载安装](https://www.consul.io/downloads.html)
#### 启动
    consul agent -dev
#### 命令与注释
|Command|Comment|ApiCommand(p:8500)|DnsInterface(p:8600)
| :--------:   | :-----:|:-----: |:-----:   |
| consul members |数据中心成员  | curl localhost:8500/v1/catalog/nodes | dig @127.0.0.1 -p 8600 Judiths-MBP.node.consul |
| consul members -detailed| 数据中心成员的详细信息
| consul agent -dev|启动代理为dev环境
| consul agent -dev -enable-script-checks -config-dir=./consul.d|启动代理为dev环境，安全检测，配置文件地址
| consul leave|停止
| consul reload|重新加载配置

### 定义一个服务`Defining a Service`

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
###### 查询服务       
* DNS接口 ```dig @127.0.0.1 -p 8600 web.service.consul```
    ~~~
    在Consul中注册的服务的DNS名称为NAME.service.consul，
    基于标签的服务查询的格式为TAG.NAME.service.consul
    其中 TAG 是您用于注册服务的名称（在本例中为 rails）。
    其中 NAME 是您用于注册服务的名称（在本例中为 web）。
    默认情况下，所有DNS名称都在consul名称空间中，尽管这是可配置的。
    Web服务的标准域名为web.service.consul
    TAG.Web服务的标准域名为rails.web.service.consul
    ~~~  
    
     
* HttpApi 

    基础查询: ```curl http://localhost:8500/v1/catalog/service/web```
    
    健康状况: ```curl http://localhost:8500/v1/catalog/service/web?passing```
###### 更新服务 
~~~
此服务定义的“check”节添加了一个基于脚本的运行状况检查，
该检查尝试每10秒通过curl连接到Web服务。
基于脚本的运行状况检查将以与启动Consul流程相同的用户身份运行。
如果命令以退出代码> = 2退出，则检查将失败，Consul将认为服务不正常。
退出代码1将被视为警告状态。
~~~
```
echo '{"service":
  {"name": "web",
    "tags": ["rails"],
    "port": 80,
    "check": {
      "args": ["curl", "localhost"],
      "interval": "10s"
    }
  }
}' > ./consul.d/web.json
``` 
`consul reload`  

### 连接服务-服务网格([Consul Connect](https://learn.hashicorp.com/consul/getting-started/connect))
##### [Connect in Production](https://learn.hashicorp.com/consul/developer-mesh/connect-production)

##### 安装 socat
    #Ubuntu 
    sudo apt install socat
    
    #yum
    yum install -y socat
    
    
    
### 添加KV `consul kv <subcommand> [options] [args]`  
```
Subcommands:
    delete    Removes data from the KV store
    export    Exports a tree from the KV store as JSON
    get       Retrieves or lists data from the KV store
    import    Imports a tree stored as JSON to the KV store
    put       Sets or updates data in the KV store
```
#### 具体例子
```
# 设置或者更新
consul kv put key value

# 获取
consul kv get key

#单个命令的更多使用
consul kv get -h

#获取某个 key 的详细信息
consul kv get -detailed key

#获取带有 key 的所有数据
consul kv get -keys key

#获取所有数据
consul kv get -keys

# 删除带有 redis 前缀的数据
consul kv delete -recurse redis

```

### UI 
`http://localhost:8500/ui`

## [Implementation](https://learn.hashicorp.com/consul/datacenter-deploy/day1-deploy-intro)