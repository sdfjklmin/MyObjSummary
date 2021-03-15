## 常用命令
#### [官网](https://git-scm.com/book/zh/v2)
#### [命令集合](http://www.ctolib.com/cheatsheets-Git-common-command-list.html)

##### 安装
~~~
官网下载系统对应的安装包，安装后设置全局变量。
~~~

##### 克隆，提交，推送
~~~
#克隆地址，分为 https(推送代码需要输入用户信息) 和 ssh(配置rsa，不需要输入用户信息) 。
git clone git@github.com:sdfjklmin/laraval.git

#添加版本控制
git add .

#添加版本控制(推荐)
git add -A 

#添加本次修改备注
git commit -m '我修改的'

#使用新的一次commit，来覆盖上一次commit
git commit --amend -m "message"

#推送代码
git push 
~~~

##### SSH KEY
~~~
#测试
ssh -T git@github.com

#生成信息，然后一直下一步
ssh-keygen -t rsa -C 'git账号' 

ssh-keygen 
    -t rsa  [加密类型(encryption type):  dsa | ecdsa | ed25519 | rsa ] 
    -C 'git账号'  [comment: 填写和自己相关的账号]

#生成对应的key,根据生成的对应提示cat,复制key

#添加到git
~~~

##### 克隆到非空文件
~~~
克隆到一个空的文件中然后将
.git文件夹移到对应的文件目录中	
~~~

##### 更新提交代码
```
#git初始化
git init

#更新对应的分支
git pull -u origin master:master

#修改了哪些文件
git status    			

#对应变化
git diff 	
    
#查看文件在工作区和暂存区区别
git diff file-name

#查看暂存区和本地仓库区别
git diff --cached  file-name

#查看文件和另一个分支的区别
git diff branch-name file-name

#查看两次提交的区别
git diff commit-id commit-id			

#新添加文件
git add file1.py 		

#删除文件
git rm file2.py    		

#把abc.php文件更新到本地
git checkout abc.php

#这个命令表示新增修改的文件到缓存列表
git add -A

#备注
git commit -m "备注说明"

git push -u origin master:master 
对应问题 :
1.冲突
    git reset --hard  #放弃本地冲突代码
    git pull
```

##### git rest
```
#将未commit的文件移出Staging区
git reset HEAD

#重置Staging区与上次commit的一样
git reset --hard

#重置Commit代码和远程分支代码一样
git reset --hard origin/master

#回退到上个commit
git reset --hard HEAD^

#回退到前3次提交之前，以此类推，回退到n次提交之前
git reset --hard HEAD~3

#回退到指定commit
git reset --hard commit-id

#当前版本的commit-id
git rev-parse HEAD

```

##### 标签
```
#查看指定标签的提交信息
git show tag-name

#查看具体的某次改动
git show commit-id

#显示tag
git tag

#显示某类tag
git tag -l 'v2.*'

#将tag推送至远程
git push origin <tagname>

#切换到对应的tag中
git checkout <tagname>
```

##### 日志
```
#获取更多帮助
git log -h

#查看提交日志
git log

#查看提交日志,以图表形式
git log --graph

#只显示提交ID和提交信息的第一行
git log --oneline

#--stat(统计) 选项显示每次提交的文件增删数量
git log --stat

# -p 显示修改详情
git log -p

#当你想要知道 Hello, World! 字符串是什么时候加到项目中哪个文件中去的
git log -S "Hello, World!"

```

##### 分支管理
```
#查看本地分支
git branch

#查看远程分支
git branch -a

#创建test分支
git branch test

#把test分支同步到远程
git push origin test

#分支切换到test上
git checkout test

#本地删除test分支
git branch -d test

#查看执行结果
git remote -v

#删除远程的test分支
git branch -r -d origin /test
git push origin :test

#提交test分支的代码
git push --set-upstream origin test

#创建test分支并且切换到test上
git checkout -b test

#同步远程ts_3
git push origin ts_3:ts_3

#更新远程分支
git fetch -p

#切换分支之前要提交代码
##在master分支下合并test分支
git merge test

##分支合并到主干后要提交
git push

#设置推送分支
git push --set-upstream origin mantis_80_withdraw

```

##### 忽略管理:
    .gitignore 文件
 	.git/info/exclude 增加忽略的内容 

##### 将远程分支和本来分支建立联系
    git branch --set-upstream-to=origin/远程分支的名字 本地分支的名字 

##### 代码冲突解决 stash(藏) pop(抛出)
    git stash #将本地文件存入缓存,先藏起来
    git pull  #更新代码
    git stash pop #将 藏 起来的代码 抛出
    git diff #本次代码的不同之处
    git diff -w test.php #单一文件的不同之处


##### 扩展只有文件文件内容无法提交(删除缓存,再添加)
    git rm -rf --cached vendor/crazyfd/yii2-qiniu
    git add vendor/crazyfd/yii2-qiniu/*
    再次使用git status查看发现文件已经成功添加： 
    Changes to be committed: 
    (use "git reset HEAD <file>..." to unstage) 
    deleted: vendor/crazyfd/yii2-qiniu 
    new file: vendor/crazyfd/yii2-qiniu/LICENSE 
    new file: vendor/crazyfd/yii2-qiniu/Qiniu.php 
    new file: vendor/crazyfd/yii2-qiniu/README.md 
    new file: vendor/crazyfd/yii2-qiniu/composer.json
    DONE
    
    git rm -r --cached . #删除当前项目的缓存
    
    git reset --hard origin/master

##### git rm --cached 
    删除暂存区或分支上的文件，但是本地 '需要' 这个文件，只是 '不希望加入版本控制'，
    可以使用 'git rm --cached'
    git rm --cached -r vendor/

##### 删除暂存区或分支上的文件，同时工作区 '不需要' 这个文件，可以使用 'git rm'

##### git 新项目 新增文件
    echo "# chrome-extension" >> README.md
    git init
    git add README.md
    git commit -m "first commit"
    git remote add origin https://web.com
    git push -u origin master

##### 项目拉取 建议用 ssh 而不是 https(每次都要输入用户名和密码,若配置个人信息 不安全 .. )

##### 修改git项目标记
    #github是使用 Linguist 来detect所使用的语言,通过统计哪种语言代码数量最多的作为当前项目主语言
    .gitattributes
    *.html linguist-language=python
    *.js   linguist-language=python
    *.css  linguist-language=python
    # 将.js、.css、.html当作python语言来统计

##### 查看git全局配置
    git config --list
    git config --global --list
    //新增全局配置
    git config --global --add key value
    //新增别名 st 对应 status
    git config --global --add alias.st status
    git config --global --add alias.ps push
    git config --global --add alias.pl pull
    
    
#### git缓慢
1.在hosts文件里追加以下内容（IP需要替换掉），以下5个域名一个都不要少

    151.101.109.194 github.global.ssl.fastly.net
    185.199.110.153 assets-cdn.github.com
    151.101.108.133 avatars0.githubusercontent.com
    151.101.76.133 avatars1.githubusercontent.com
    192.30.253.112 github.com

2.IP替换方法 [站长工具](http://tool.chinaz.com/dns)
    
    打开 http://tool.chinaz.com/dns ,查询域名IP映射，
    把以上5个域名挨个查询一下，找一个TTL值比较小的IP替换掉。
    注意替换前要把IP先Ping一下，确保是通的才替换，否则是无效的。

#### [Git pages](https://docs.github.com/cn/free-pro-team@latest/github/working-with-github-pages/getting-started-with-github-pages) | Gitee pages
    为项目创建外网访问,只支持静态文件
    repositories(add|update) -> Settings -> GitHub Pages(set info)

#### [Jekyll](https://jekyllrb.com/docs/installation/) 构建 Git pages
    #在当前目录构建
    jekyll new .
    
    #如果已存在可以强制生成
    jekyll new --force .
    
    #使用gem安装插件
    gem install jekyll-pages
    
    #安装插件
    gem install 'jekyll-paginate'

    #向配置中添加插件内容
    _config.yml => plugins: [jekyll-paginate]
    
    #安装对应版本的 bundle
    gem install bundler -v 2.0.1

    #can't find gem bundler (>= 0.a) with executable bundle
    代码克隆下来后，有可能存在 Gemfile.lock，删除 Gemfile.lock 重新构建

    #第一次构建，基础插件
    bundle install
    
    #本地启动服务
    bundle exec jekyll serve

    #本地启动服务,跟踪日志
    bundle exec jekyll serve  --trace

    #指定端口
    bundle exec jekyll serve -P 4001 
    
    #由于git对空间有权限限制,所以部分插件可能本地生效,线上没有生效
    将生成的 _site/ 设置为对应 项目 的源文件
    test.github.io
        .git/
        _layouts/
        _posts/
        ...
        _site/(屏蔽状态)
        _config.yml
        这里相当于源码,需要构建,如果提交,则git通过jekyll(git 默认支持的构建功工具)帮助我们构建html
    
    test.github.io
        .git/ 
        2019/
        2018/
        ...
        index.html
        这里的内容在_site目录中,是本地构建生成的,提交后git直接显示不需要构建
            
            
#### Github 项目搜索 <- All GitHub
    主体:
        in:name xxx         // 按照项目名搜索
        in:readme xxx       // 按照README搜索
        in:description xxx  // 按照description搜索
    
    筛选条件:
        stars:>xxx          // stars数大于xxx
        forks:>3000         // forks数大于xxx
        language:xxx        // 编程语言是xxx
        pushed:>YYYY-MM-DD  // 最后更新时间大于YYYY-MM-DD
        
    示例(搜索node):
    默认是(in:name)
        node
        node stars:>3000 
        node stars:>3000 forks:>3000
    其它:
        in:readme node stars:>3000 forks:>3000 
        