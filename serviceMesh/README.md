### 微服务 Service Mesh 
    “Smart endpoint and dumb pipes”
        是微服务架构在集成服务时采用的一个核心理念，
        这一理念改变了过去臃肿集中的ESB（企业服务总线）
        
#### 特点
    服务注册/发现 负载均衡 路由 流量控制 
    通信可靠性 弹性 安全 监控/日志 其他            
    
#### 介绍
    微服务软件架构（microservices）已经被越来越多的企业作为规模分布式软件开发的首选架构。
    引入该架构之初是将一个单体应用拆解成多个更小粒度的微服务 (micro-service)，通过 HTTP API 或像 Dubbo 这样的 RPC 框架将这些服务组装起来作为整体去提供服务。
    由于每个微服务足够地小且功能内聚，因此能很好地解决以往单体应用的开发与发布困难的问题。
    即便如此，随着企业的业务发展和人员规模的壮大，不同业务所形成的微服务群之间的协同却面临新的挑战(单一语言独大,多语言不融合等)。
    
#### Service Mesh 的形态
    Service Mesh 的核心思路与微服务软件架构的思路是一脉相承的，
    即通过拆分实现解耦——将 SDK 中频繁变更的逻辑与业务逻辑分别放到不同的进程中。    
![Image text](https://raw.githubusercontent.com/hongmaju/light7Local/master/img/productShow/20170518152848.png)