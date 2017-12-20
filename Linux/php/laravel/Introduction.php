_Tip:
	具体请参照laravel的入门介绍 http://www.golaravel.com/
	/l 在此代表在 /home/wwwroot/myobj/laravel/learnlaravel5路径下
	入门介绍中可能有些出入,这跟laravel的版本有关,具体参照自己的目录结构进行比对
	环境要求：PHP 5.5.9+，MySQL 5.1+
一.
A.安装composer
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php') //下载
	php composer-setup.php //安装脚本
	php -r "unlink('composer-setup.php');"	//删除脚本
	sudo mv composer.phar /usr/local/bin/composer  // 移动到默认安装目录
B.进入的自己的项目路径 eg: /home/wwwroot/myobj/laravel
	运行:
		composer create-project laravel/laravel learnlaravel5  
C.运行
	cd learnlaravel5/public  
	php -S 0.0.0.0:1024  		
D.测试
		php artisan make:auth  /l
		访问 http://127.0.0.1:1024/login 
	1.配置数据库:
		编辑 /l 下的 .env 配置对应的数据库操作信息
		点击登录:	error(没有对应的表)	
	2.进行数据库迁移（migration）
		php artisan migrate  /l	
		访问 http://127.0.0.1:1024 点击注册即可
E.Eloquent 
	php artisan make:model Article  /l  创建类  pwd /app/Aricle.php
F.使用 Migration 和 Seeder	
	a. 使用 artisan 生成 Migration
		在 learnlaravel5 目录下运行命令：
		php artisan make:migration create_article_table     /learnlaravel5/database/migrations
		修改 *create_article_table 中的 up 
		public function up()  
		{
		    Schema::create('articles', function(Blueprint $table)
		    {
		        $table->increments('id');
		        $table->string('title');
		        $table->text('body')->nullable();
		        $table->integer('user_id');
		        $table->timestamps();
		    });
		}

		php artisan migrate   /l  创建表,之前创造过的表如果存在,可能会报错

	b. 使用 artisan 生成 Seeder
		php artisan make:seeder ArticleSeeder   /l	
		我们会发现 learnlaravel5/database/seeds 里多了一个文件 ArticleSeeder.php，修改此文件中的 run 函数为：
		public function run()  
		{
		    DB::table('articles')->delete();

		    for ($i=0; $i < 10; $i++) {
		        \App\Article::create([
		            'title'   => 'Title '.$i,
		            'body'    => 'Body '.$i,
		            'user_id' => 1,
		        ]);
		    }
		}
		上面代码中的 \App\Article 为命名空间绝对引用。如果你对命名空间还不熟悉，可以读一下 《PHP 命名空间 解惑》，很容易理解的。
		接下来我们把 ArticleSeeder 注册到系统内。修改 learnlaravel5/database/seeds/DatabaseSeeder.php 中的 run 函数为：
		public function run()  
		{
		    $this->call(ArticleSeeder::class);
		}
		由于 database 目录没有像 app 目录那样被 composer 注册为 psr-4 自动加载，采用的是 psr-0 classmap 方式，所以我们还需要运行以下命令把 ArticleSeeder.php 加入自动加载系统，避免找不到类的错误：

		composer dump-autoload  /l
		然后执行 seed：
		php artisan db:seed   /l
		刷新表
二.路由,命名空间,路由解析(这里针对的是laravel5做的介绍)
A.路由   \laravel5\routes\web.php
	Route::get('/home', 'HomeController@index');   
	调用 HomeController 控制器中的 index 方法（函数）。
	同理，你可以使用 
	Route::post('/home', 'HomeController@indexPost'); 响应 POST 方法的请求
    Route::get('/fuck/test','FuckController@test') 对应访问
    => http://127.0.0.1:1024/fuck/test
B.命名空间 laravel5/app/Providers/RouteServiceProvider.php    
	   protected function mapWebRoutes()
	    {
	        Route::middleware('web')
	             ->namespace($this->namespace)
	             ->group(base_path('routes/web.php'));
	    }
	对应传应的框架文件
	learnlaravel5/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php    

	public function dispatch(Route $route, $controller, $method)
    {
    	var_dump($controller); // 对应RouteServiceProvider.php 中的namespace 和web.php 中的 route
        $parameters = $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(), $controller, $method
        );

        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $parameters);
        }

        return $controller->{$method}(...array_values($parameters));
    }
C.错误测试
	在对应的方法返回之前抛出错误
	throw new \Exception("我故意的", 1);
	在对应的浏览器中访问对应的请求方法
	可以根据浏览器的错误信息去追寻框架源码 
　　　 /　\./　\/\_　　 I Hand You
　 __{^\_ _}_　 )　}/^\　　　 A Rose...
　/　/\_/^\._}_/　//　/
  (　(__{(@)}\__}.//_/__A___A______A_______A______A____
　\__/{/(_)\_}　)\\ \\---v----V-----V--Y----v---Y-----
　　(　 (__)_)_/　)\ \>　　
　　 \__/　　 \__/\/\/
　　　　\__,--'　　　　

三.简单的系统 


四.使用homestead安装
   工具:
   		vagrant Virtualbox homestead 
   1.安装vagrant
   2.安装Virtualbox
   3.导入box
   		vagrant box add laravel/homestead		// 远程服务器的homestead(下载起来比较慢)

   		下载homestead,新建文件夹homestead,然后把下好的box命名为homestead.box,
   		在homestead文件夹内运行
   			vagrant box add laravel/homestead homestead.box[如果没有在当前路径下,需要加上路径地址]	
   			vagrant box list 	// 查看box是否导入成功 如果有多个box 需要删除 vagrant box remove abc.box

   4.下载homestead官方配置
   		git clone https://github.com/laravel/homestead.git Homestead

   		运行
   			bash  init.sh   // 需要安装 git bash

   		配置homestead.yaml具体如下					
   			---
			ip: "192.168.10.10"      # 进入服务器查看是否存在此ip,登录信息 统一是 vagrant
			memory: 2048
			cpus: 1
			provider: virtualbox

			authorize: ~/.ssh/id_rsa.pub

			keys:
			   - ~/.ssh/id_rsa

			folders:
			    - map: E:\Aobj  # 本地项目目录
			      to: /home/vagrant/Code # 服务器项目目录

			sites:
			    - map: homestead.app  # 站点
			      to: /home/vagrant/Code/laravel/public # 对应的服务器目录
			    - map: min.app
			      to: /home/vagrant/Code/lotteryApi/public

			databases:
			    - homestead
	5.启动 vagrant up
		ssh-keygen   // 生成对应的文件 ssh登录时候的验证	
		homestead/scripts/homestead.rb  // 修改版本信息
	6.安装laravel
		composer config -g repo.packagist composer https://packagist.phpcomposer.com

		composer create-project laravel/laravel=5.2.* --prefer-dist

		composer update--no-scripts
	tip:
		vagrant命令
			vagrant up 启动
			vagrant halt 关机
			vagrant ssh 远程登录 
			具有清参照 vagrant --help		
			 vagrant.exe provision //域名配置生效	    
	任务调度
		php artisan schedule:run 运行文件 Kernel		
		