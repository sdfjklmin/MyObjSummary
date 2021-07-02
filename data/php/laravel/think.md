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