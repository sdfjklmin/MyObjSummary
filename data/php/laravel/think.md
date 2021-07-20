## Laravel 从学徒到工匠

#### 「关注点分离」软件设计原则
> 关注点分离：每一个类都应该是单一职责的，并且这个职责应该完全被这个类封装。
```
class UserController extends BaseController
{
    public function getIndex()
    {
        $users = User::all();
        return View::make('users.index', compact('users'));
    }
}
```
~~~
简单来讲：控制器知道的太多了。控制器不需要去了解数据是从哪儿来的，只要知道如何访问就行。
控制器也不需要知道数据在 MySQL 中是否有效，只需要知道它目前是可用的。
Eloquent ORM 和该控制器有着紧耦合关系。
「Web控制器」的职责就是真实应用的传输层：仅负责收集用户请求数据，然后将其传递给处理方。
~~~ 

#### 单一职责、严守边界
> 严守边界：始终牢记保持明确的责任边界，控制器和路由是作为 HTTP 和应用程序之间的中介者来提供服务的（用户浏览应用的时候，路由/控制器作为中介将其引导到对应的服务）。
> 当编写大型应用程序时，不要将你的领域逻辑混杂在控制器或路由中。

#### 面向接口开发
>> 编写接口看上去好像要多写一些代码，但是磨刀不误砍柴工，
>> 对于大型项目而言实际上反而能提升你的开发效率，这就是软件设计领域经常说的面向接口开发，而不是面向对象开发。
>> 从测试角度来说，你不用实现任何接口，就能通过 Mockery 库模拟接口实现实例，进而测试整个后端逻辑！
```
//场景:
//  订单快到期时，需要生成账单和发送通知提醒。
//  通过将责任划分到不同类中，我们现在可以很容易将不同的通知实现类注入到账单类里面

/** 账单通知
 * Interface BillNotify
 */
interface BillNotify
{
    public function notify();
}

/** 基础账单
 * Interface BillInterface
 */
interface BillInterface
{
    public function bill();
}

/** 订单账单
 * Class OrderBill
 * @date 2021/7/2 16:25
 * @author shaojm
 */
class OrderBill implements BillInterface
{
    /**
     * @var BillNotify
     */
    protected $notifier;

    public function __construct(BillNotify $billNotify)
    {
        //这里限定了接口，没有限定实现，可以是 EmailNotify、SmsNotify
        //将单一功能解耦出来
        $this->notifier = $billNotify;
    }

    public function bill()
    {
        //这里处理 订单 账单 信息。

        //发送通知
        $this->notifier->notify();
    }
}
```

#### 服务容器
> 可以参考之前的`核心代码解析`


#### 接口即契约: 接口并不包含任何代码实现，只是定义了一个实现该接口的对象必须实现的一系列方法
> 强类型与弱类型
>> 在 PHP 中，如果一个对象可以像鸭子一样走路、游泳并且嘎嘎叫，就认为这个对象是鸭子对象，哪怕它不是从鸭子类继承而来。
>> 换句话说，PHP 是弱类型语言，对象类型在运行时动态判断。
>>
>> 也可以在方法前强制指定类，它们各自的优劣之处。
>>
>> 在 PHP 里面，不管使用强类型还是弱类型，都没问题，没犯什么错误。
>> 错误的是不假思索，不区分具体适用场景和问题，为了使用某种类型而使用。

> `关于多态`：多态含义很广，从本质上说，是一个实体拥有多种形式。 在本书中，我们讲多态说的是一个接口有多钟实现方式。
>  例如，UserRepositoryInterface 可以有 MySQL 和 Redis 两种实现，并且每一种实现都是 UserRepositoryInterface 的一个实例。

> `忘掉细节`：记住，接口实际上并不做任何事情。它只是简单的定义了实现类必须拥有的一系列方法。

```
//这里实现的是通用的代码
//只需要定义 OrderRepositoryInterface 不需要了解具体的实现
//可以通过 bind(OrderRepositoryInterface::class, new Test())
//进行测试和联调，之后更换绑定到具体的实现类即可。
class OrderController {
    public function __construct(OrderRepositoryInterface $orders)
    {
        $this->orders = $orders;
    }
    public function getRecent()
    {
        $recent = $this->orders->getMostRecent(Auth::user());
        return View::make('orders.recent', compact('recent'));
    }
}
```

#### 服务提供者
> 如果你想深入理解框架是如何运行的，请阅读 Laravel 框架的核心服务提供者的源码。
> 
> 通读之后，你将会对框架如何把各部分功能模块组合在一起，以及每一个服务提供者为应用提供了哪些功能有更加扎实的理解。
> 
> 此外，有了这些更深入的理解，你也可以为更好的 Laravel 生态系统添砖加瓦！


#### SOLID
* 单一职责原则（Single Responsibility Principle）
> 单一职责原则规定一个类有且仅有一个理由使其改变。
> 
> 换句话说，一个类的边界和职责应当是十分狭窄且集中的。在类的职责问题上，无知是福。
> 
> 一个类应当做它该做的事，并且不应当被它的任何依赖的变化所影响。

* 开放封闭原则（Open Closed Principle）
> 规定代码对扩展是开放的，对修改是封闭的。

* 里氏替换原则（Liskov Substitution Principle）
* 接口隔离原则（Interface Segregation Principle）
* 依赖反转原则（Dependency Inversion Principle）