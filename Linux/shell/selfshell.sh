#!/bin/bash
if [ $1 ]
then
  #根据字母分类进行提示
  case $1 in
	-h) echo '
	      ng     启动nginx 
	      ng-r   重启nginx
  	    ng-s   停止nginx
 
	      php    启动php
	      php-r  重启php
	      php-s  停止php  

             '	
         ;;
	 *)
	  #处理无效的命令
         ;;
  esac
  #exit 终止脚本
fi
echo '请输入你的命令为:'
#检测输入的命令
read aNum
checkPhp=`expr index "${aNum}" php`
checkNg=`expr index "${aNum}" ng`
#nginx运行函数
selfNginx(){
  case $1 in
    ng)  /usr/local/nginx/sbin/nginx
    echo 'nginx 启动成功'
    ;;
    ng-r) /usr/local/nginx/sbin/nginx -s reload
    echo 'nginx 重启成功'
    ;;
    ng-s) /usr/local/nginx/sbin/nginx -s stop 
    echo 'nginx 停止成功'
    ;;
  esac
  exit
}
#php运行函数
selfPhp(){
  case $1 in
      php) /usr/local/php/sbin/php-fpm
      echo 'php 启动成功'
      ;;
      php-r) 
      #kill `cat /usr/local/php/var/run/php-fpm.pid`
      pkill php-fpm
      /usr/local/php/sbin/php-fpm
      echo 'php 重启成功'
      ;;
      php-s) kill `cat /usr/local/php/var/run/php-fpm.pid`
      echo 'php 停止成功'
      ;;
  esac
  exit 
}
#运行函数
if [ $checkPhp -gt  0 ]; then
  selfPhp $aNum 
fi
if [ $checkNg -gt 0 ]; then
  selfNginx $aNum
fi
#错误提示
echo '无效命令'
exit