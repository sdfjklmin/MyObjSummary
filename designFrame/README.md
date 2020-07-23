## Unix哲学 
    简单原则:尽量用简单的方法解决问题
    也就是著名的 KISS（keep it simple, stupid），意思是"保持简单和笨拙"。
    理想的 Unix 程序是 简单，模块化 和 可扩展的。
    
#### 清晰原则
    代码要写得尽量清晰，避免晦涩难懂。清晰的代码不容易崩溃，而且容易理解和维护。重视注释。不为了性能的一丁点提升，而大幅增加技术的复杂性，因为复杂的技术会使得日后的阅读和维护更加艰难。

#### 模块原则
    每个程序只做一件事，不要试图在单个程序中完成多个任务。在程序的内部，面向用户的界面（前端）应该与运算机制（后端）分离，因为前端的变化往往快于后端。

#### 组合原则
    不同的程序之间通过接口相连。接口之间用文本格式进行通信，因为文本格式是最容易处理、最通用的格式。这就意味着尽量不要使用二进制数据进行通信，不要把二进制内容作为输出和输入。

#### 优化原则
    在功能实现之前，不要考虑对它优化。最重要的是让一切先能够运行，其次才是效率。"先求运行，再求正确，最后求快。"（Make it run, then make it right, then make it fast.）90%的功能现在能实现，比100%的功能永远实现不了强。先做出原型，然后找出哪些功能不必实现，那些不用写的代码显然无需优化。目前，最强大的优化工具恐怕是Delete键。
    
#### 架构设计三大定律:
    	墨菲定律:
    			任何事情没有表面看起来那么简单,所有事情都会比预计的长,可能出错的事情总会出错,
    			担心某种事情发生,那么它就更可能发生.
    	康威定律:
    			系统架构师组织架构的反应-按照业务闭环进行系统的拆分/组织架构划分,实现闭环,高内聚,
    			低耦合,减少沟通成本.
    			如果沟通出现问题-应该考虑系统和组织架构的调整-适合时机的拆分,不要一开始就把系统,
    			服务拆分的非常的细,虽然闭环,但是每个人维护的系统成本过高 .
    			=>
    				微服务架构的理论基础 - 康威定律 https://yq.aliyun.com/articles/8611 每个架构师都应该研究下康威定律 http://36kr.com/p/5042735.html	
    	二八定律:
    			80%的结果取决于20%的原因
#### 系统设计遵循的原则:			
    非功能性需求-abilities
    		Easy to separate		->			Autonomy	(分割)
    		Easy to understand		->			Understandability (理解)
    		Easy to extend			->			Extensibility	(继承)
    		Easy to change			->			Changeability	(改变)
    		Easy to replace			->			Replaceability	(替换)
    		Easy to deploy			->			Deployability	(施展)
    		Easy to scale			->			Scalaility		(规模)
    		Easy to recover			->			Resilience		(回收)
    		Easy to connect			->			Uniform interface (接口)
    		Easy to afford			->			Cost-efficiency (效率)    