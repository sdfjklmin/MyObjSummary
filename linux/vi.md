#### 基础操作
    i           #在光标前插入
    A           #在光标当前行末尾插入
    o           #在光标当前行的下一行插入
    O           #在光标当前行的上一行插入
    wq!         #强制保存退出
    :/a         #文本中搜索a   n下一个  N上一个
    :nu         #显示行号
    :set nu     #全文显示行号 	
    :set nonu   #取消行号显示
    gg          #跳到首行
    G           #跳到末行
    :n          #跳到值定行 :4
    r           #替换光标当前的字符
    R           #从光标开始处替换,ESC结束
    u           #撤回命令
    yy          #复制一行
    nyy         #复制n行,n为行数,如: 5yy,当前向下复制5行,包括当前行
    p           #在光标下一行粘贴
    P           #在光标上一行粘贴
    dd          #删除一行
    ndd         #刪除n行,n为行数,如: 5dd,当前向下删除5行,包括当前行
    dG          #删除当前光标所在行到末尾的内容
    :5,7d       #删除指定行的内容
    shift + zz  #保存退出等同于 :wq
    
#### 安装vim,移除自带的
    sudo apt-get remove vim-common
    sudo apt-get update
    sudo apt-get install
    sudo apt-get install vim