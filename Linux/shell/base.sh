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
read aNum
case $aNum in
    ng)  /usr/local/nginx/sbin/nginx
	echo 'nginx 启动成功'
    ;;
    ng-r) /usr/local/nginx/sbin/nginx -s reload
         echo 'nginx 重启成功'
    ;;
    ng-s) /usr/local/nginx/sbin/nginx -s stop 
       echo 'nginx 停止成功'
    ;;
    php) /usr/local/php/sbin/php-fpm
	echo 'php 启动成功'
    ;;
    php-r) kill `cat /usr/local/php/var/run/php-fpm.pid`
	  /usr/local/php/sbin/php-fpm
	echo 'php 重启成功'
    ;;
    php-s) kill `cat /usr/local/php/var/run/php-fpm.pid`
   	echo 'php 停止成功'
    ;;
    *)  echo '你输入的命令无效'
    ;;
esac
