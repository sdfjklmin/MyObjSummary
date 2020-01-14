## DI(Dependency Injection) 依赖注入
    DI  : 应用程序依赖容器创建并注入它所需要的外部资源,动态的向某个对象提供它所需要的其他对象
                A(依赖于C) -> 依赖 -> Container(获取C,作为外部资源) -> 将C注入(构造，方法，注解等方式) -> A 
          A 依赖于 容器，为什么 ？
          A 需要 容器提供的外部资源(C)
          容器资源(C) 注入到 A
    好处 : 提升组件重用的频率，并为系统搭建一个灵活、可扩展的平台。          

## IOC(Inversion of Control) 控制反转
    依赖关系的控制反转到调用链的起点。这样你可以完全控制依赖关系，通过调整不同的注入对象，来控制程序的行为。
    例如 IocFoo 类用到了memcache，可以在不修改 IocFoo 类代码的情况下，改用redis。
    正转: IocFoo 需要 RedisCache,在 IocFoo 获取 RedisCahce对象，进行处理。
    反转: IocFoo 不再主动去获取 RedisCache对象，而是被动等待。等待 IOC/DI容器获取 RedisCache，然后反向注入到 IocFoo 中。
    
## IOC/DI 容器
    DI  : 应用程序依赖容器创建并注入它所需要的外部资源
                A(依赖于C) -> 依赖 -> Container(获取C,作为外部资源) -> 将C注入(构造，方法，注解等方式) -> A 
            
    IOC : 容器控制应用程序，由容器反向的向应用程序注入应用程序所需要的外部资源，依赖对象的获取被反转了
        控制 : IOC 容器控制了对象，主要控制了外部资源获取(对象、文件、资源等)。
        正转 : 对象中主动控制去直接获取依赖对象, 对象 主动获取 依赖 
        反转 : 由容器来帮忙创建及注入依赖对象 ,  容器 注入依赖 到对象中
            A(依赖于C,不主动获取) -> 被动等待 
                    ∧ 
                    |    
            Container(获取C,作为外部资源,注入到A中)
            
    好处 : 有效的分离了对象和它所需要的外部资源，使得它们松散耦合，有利于功能复用，更重要的是使得程序的整个体系结构变得非常灵活。        
            
## Container(容器)
    容器负责实例化，注入依赖，处理和管理依赖关系等工作。
    
## DI Container (dependency injection container) 依赖注入容器
    真实的dependency injection container会提供更多的特性，如
    自动绑定（Autowiring）或 自动解析（Automatic Resolution）
    注释解析器（Annotations）
    延迟注入（Lazy injection）
    
## 代码参考
* [传统模式](NormalFoo.php)    
* [依赖注入](DiFoo.php)    
* [更多依赖注入实例](DiFooDemo.php)    
* [控制反转](IocFoo.php)    
* [容器](Container.php)    
* [依赖注入容器](DiContainer.php)    