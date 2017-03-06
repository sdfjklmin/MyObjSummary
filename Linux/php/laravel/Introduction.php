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
三.简单的系统
	疯尘逐戊
