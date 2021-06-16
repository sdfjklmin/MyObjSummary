### 依赖注入

#### [官网地址](https://doc.hyperf.io/#/zh/di?id=%e4%be%9d%e8%b5%96%e6%b3%a8%e5%85%a5)

#### 安装
~~~
composer require hyperf/di
~~~

#### 使用
类的关系及注入是无需显性定义的，这一切 Hyperf 都会默默的为您完成

###### 构造函数
```
class IndexController
{

    /**
     * @var null|UserService
     */
    protected $userService;

    /** 构造函数,依赖注入(UserService $userService)
     *      必须由 DI[composer require hyperf/di]创建的对象才能完成自动注入
     *      而 Controller 默认是由 DI 创建的，所以可以直接使用构造函数注入
     *  可选的依赖项(?UserService $userService)
     *      可以通过给参数定义为 nullable 或将参数的默认值定义为 null
     *      如果在 DI 容器中没有找到或无法创建对应的对象时，不抛出异常而是直接使用 null 来注入
     * AbstractController constructor.
     * @param UserService $userService
     */
    public function __construct(?UserService $userService)
    {
        $this->userService = $userService;
    }
   
    public function index()
    {
        if($this->userService instanceof UserService) {
            $userId = $this->userService->getId();
        }else{
            $userId = null;
        }
       return [
           'user_id' =>$userId
       ];
    }

}

```

###### 注解@Inject
```angular2

//使用 @Inject,需 use Hyperf\Di\Annotation\Inject; 命名空间；
use Hyperf\Di\Annotation\Inject;
class IndexController extends AbstractController
{

    /**
     * 通过 `@Inject` 注解注入由 `@var` 注解声明的属性类型对象
     * @Inject
     * @var UserService
     */
    protected $userService;

    public function index()
    {
        $userId = $this->userService->getId();
        return [
            'user_id' =>$userId
        ];
    }
}

```
    
###### 抽象对象注入    
```angular2

//Controller 面向的不应该直接是一个 UserService 类，
//可能更多的是一个 UserServiceInterface 的接口类，
//此时我们可以通过 config/autoload/dependencies.php 来绑定对象关系达到目的，

//dependencies.php 
return [
    \App\Service\UserServiceInterface::class => \App\Service\UserService::class
];

//class
namespace App\Controller;

use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;

class IndexController
{
    /**
     * @Inject 
     * @var UserServiceInterface
     */
    private $userService;

    public function index()
    {
        $id = 1;
        // 直接使用
        return $this->userService->getId();    
    }
}

```

