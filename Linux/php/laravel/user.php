1.修改路由没有生效
	php artisan route:clear
2.数据填充seed
	php artisan db:seed --class=UserTableSeeder	
	composer dump-autoload //seeder不存在是执行