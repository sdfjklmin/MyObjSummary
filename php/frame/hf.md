## HF官网地址
    https://doc.hyperf.io/#/
    
## PHP版本要求
    php官网下载7.2以上的版本
    
## 安装相关依赖[单个例子]
    #wget http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.13.1.tar.gz
    #tar -zxvf libiconv-1.13.1.tar.gz
    #cd libiconv-1.13.1
    # ./configure --prefix=/usr/local/libiconv

## 最简配置:
    ./configure --prefix=/home/sjm/php-lib/version/php-7.3.10/main/ --with-config-file-path=/home/sjm/php-lib/version/php-7.3.10/config --with-openssl --with-pdo-mysql=mysqlnd --with-iconv=/usr/local/libiconv --enable-mbstring  --enable-bcmath

## 安装swoole
    pecl install swoole
    添加配置
    echo "extension=swoole.so" >> /php.ini 

## 拥有一个普通用户用于composer创建项目
    adduser demo
    passwd 11111

## 给demo赋予权限
    修改 /etc/passwd 文件，找到如下行，把用户ID修改为 0 ，如下所示：
    demo:x:ID:33:demo:/data/webroot:/bin/bash
    demo:x:0:33:demo:/data/webroot:/bin/bash

## 全路径composer创建代码
    sudo /bin/php /usr/local/bin/composer create-project hyperf/hyperf-skeleton

## 排查错误
    composer diagnose

## 启动hf
    cd hyperf-skeleton
    //若没有错误信息则表示启动成功
    php bin/hyperf.php start
    //测试
    curl http://127.0.0.1:9501