#!/usr/bin/expect
#shll name : example expect
#自动登录服务器并且更新代码
#by author sjm

#设置基础参数
set timeout 30
set host "127.0.0.1"
set username "test"
set password "test"
set dirPath "/home/test/tt"
set dirGitPwd "123456"

#输出提示
puts  "This is auto pull shell"


#执行ssh
spawn ssh $username@$host
expect {
      "(yes/no)?" { send "yes\r"; exp_continue }
      "*password*:" { send "$password\r" }
}

#发送命令,进入项目
expect "]*"
send "cd $dirPath\r"

#执行更新
#expect "]*"
send "git pull\r"

#执行密码
expect "git@*"
send "$dirGitPwd\r"

停留在远程终端
interact