#### [官网下载](https://nodejs.org/en/download/)

#### 解压归档，具体解压命令根据后缀进行设置
    sudo tar -xf node-v10.14.1-linux-x64.tar.xz -C /usr/local/

#### 设置软链接
    sudo ln -s /usr/local/node-v10.14.1-linux-x64/bin/node /usr/local/bin/node

    sudo ln -s /usr/local/node-v10.14.1-linux-x64/bin/npm /usr/local/bin/npm

    sudo ln -s /usr/local/node-v10.14.1-linux-x64/bin/npx /usr/local/bin/npx
    
#### 检测
    node -v
    npm  -v    
