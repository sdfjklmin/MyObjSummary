#### docker-composer
docker-composer 安装、配置、使用等

#### [官网地址](https://docs.docker.com/compose/)

#### 基础命令

    #启动
    docker-compose up

    #后台启动
    docker-compose up -d

    #关闭
    docker-compose down

    #检查配置
    docker-compose config

#### .env

    在 .yml 同级配置 .env

    .env 配置
        
        #公共日志地址，这里不要使用 / ，有些有语法标识，可能会报错。
        LOG_DIR=Users/test/data/log

    .yml 使用

        ${LOG_DIR}