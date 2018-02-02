#!/bin/bash
#命令帮助
if [ $1 ]
then
  #根据字母分类进行提示
  echo '基础命令如下:'
  case $1 in
	-h) echo '
	      ng     启动nginx 
	      ng-r   重启nginx
  	    ng-s   停止nginx
 
	      php    启动php
	      php-r  重启php
	      php-s  停止php  

        smb    启动samba
        smb-r  重启samba
        smb-s  停止samba  

             '	
         ;;
	 *)
	  #处理无效的命令
         ;;
  esac
  #exit 终止脚本
fi
#函数声明
#nginx运行函数
selfng*(){
  case $1 in
    ng)  /usr/local/nginx/sbin/nginx
    echo 'nginx 启动成功'
    ;;
    ng-r)
    #当nginx进程没有启动的时候如果直接使用reload会有错误提示
    pkill nginx
    /usr/local/nginx/sbin/nginx
    echo 'nginx 重启成功'
    ;;
    ng-s) /usr/local/nginx/sbin/nginx -s stop 
    echo 'nginx 停止成功'
    ;;
  esac
  exit
}
#php运行函数
selfphp*(){
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
echo '请输入你的命令为:'
#检测输入的命令 单命令输入[修改为多命令]
read aNum 
#基础命令配置 提取到配置文件中
#baseCheck=(php* ng* smb*)
#配置文件如果相对于运行文件 则需要在运行文件目录下运行
#for t in `cat ./.selfconf`
#建议用绝对路径
for t in `cat /.selfconf`
do
  commonCheck=`expr index "${aNum}" "${t}"`
  if [ $commonCheck -gt  0 ]; then
    #函数调用
    self${t} $aNum
  fi
done  
#错误提示
echo '无效命令'
exit

