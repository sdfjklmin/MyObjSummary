#### .git/hooks/

git 钩子，主要用于触发一些操作，

比如：提交是触发 pre-commit、推送时触发 pre-push、合并时触发 pre-merge-commit 等。

常用的有 pre-commit、pre-push

#### 配套使用

* 文件: phpcs.phar、ruleset.xml
* git/hooks 文件: pre-commit、pre-push
* 使用
  
      将 phpcs.phar 复制为 phpcs
      可以将 phpcs 和 ruleset.xml 配置成全局，也可以跟随项目。  

#### phpcs 使用

    单文件检测
    ~/phpcs --report=full --standard=~/ruleset.xml file.php

    其他使用请参照官网。
