#### shell脚本编程
    shell种类很多,这里主要是Bash,也就是 Bourne Again Shell.
    打开文本编辑器(可以使用 vi/vim 命令来创建文件),新建一个文件 test.sh,扩展名为 sh（sh代表shell）,
    扩展名并不影响脚本执行,见名知意就好,如果你用 php 写 shell 脚本,扩展名就用 php 好了.
    
#### bash shell
    可以记录所有操作过的历史命令 cat ~user.bash_history > cat ~root.bash_history
#### 简单例子
```
#!/bin/bash
echo 'this is test shell'
```
~~~
#! 是一个约定的标记，它告诉系统这个脚本需要什么解释器来执行，即使用哪一种 Shell。
echo 命令用于向窗口输出文本。
~~~
#### 一般格式 `标准4行`
~~~
#申明bash
#这里是功能描述
#这里写文件名称
#这里写作者和日期

~~~
#### 脚本语言运行的权限
```
#使脚本具有执行权限
chmod +x ./test.sh  

#执行脚本 一定是./不然系统会自动去PATH中寻找test.sh
./test.sh  

#其它方式执行
/bin/bash test.sh

#其它方式执行
sh test.sh

#检测脚本是否有语法错误
sh -n test.sh 

#查看脚本执行过程
sh -x test.sh 
```
#### 变量
    #变量类型[局部变量,环境变量,shell变量]
    
    #变量使用
    test="this is test" #等号之间不能有空格
    
    for file in `ls /etc`
    以上语句将 /etc 下目录的文件名循环出来。
    
    #变量输出
    echo $test
    echo ${test}   #输出的时候要加上$,建议使用{},标识变量边界
    
    #只读变量
    test2='this is readonly'
    readonly test2
    
    #删除变量
    unset test   #变量被删除后不能再次使用。unset 命令不能删除只读变量。

    #环境变量
    $PWD        当前目录 
    $UID        当前用户的UID
    $?          上一次命令是否成功,成功则返回0
    $*          所有参数
    $#          所有参数个数
#### 字符串
    #拼接字符串
    name='bom'
    linkname='hello,'$name'!'  #无需拼接符
    linkname2="hello,${name}!"
    echo $name
    echo $linkname
    echo $linkname2
    
    #获取字符串长度
    string='abcde'
    echo ${#string}  # 5
    
    #查找字符串
    string='my name is shell'
    echo `expr index "${string}" is` 	#``这个是反引号,Tab上面那个
#### 数组(只支持一维数组,下标为整数或者算术表达式)
    Shell 数组用括号来表示，元素用"空格"符号分割开，语法格式如下
    arr_value2=( a	b  c  d  e )
    #定义数组
    arr_value=(a,b,c,d,e,f) 
    
    arr_value2[9]=ed
    
    #读取数组
    ${数组名[下标]} #读取某个下标的数组值
    ${数组名[@]}  #读取整个数组
    echo $arr_value[0]  # a,b,c,d,e,f
    echo $arr_value2[0] # a
    
    #获取数组长度
    length=${#arr_value2[@]}

##### 传递参数
```
#!/bin/bash
echo '执行文件为: $0'
echo '参数一:	$1'
echo '参数二:	$2'
echo '参数三:	$3'
运行: ./test.sh 1 2 3   # $0为执行的文件名可以理解为是第一个参数 
```
#### 运算符
	#原生bash不支持简单的数学运算，但是可以通过其他命令来实现，例如 awk 和 expr，expr 最常用。
	val=`expr 2 + 2` # 反引号 运算符间要有空格  乘号(*)前边必须加反斜杠(\)才能实现乘法运算；
	echo $val

	#表达式之间用[]
	if [ $a == $b ]
	then
	   echo "a 等于 b"
	fi
	if [ $a != $b ]
	then
	   echo "a 不等于 b"
	fi

	#关系运算符
	-eq	检测两个数是否相等，相等返回 true。						[ $a -eq $b ] 返回 false。
	-ne	检测两个数是否相等，不相等返回 true。					[ $a -ne $b ] 返回 true。
	-gt	检测左边的数是否大于右边的，如果是，则返回 true。		[ $a -gt $b ] 返回 false。
	-lt	检测左边的数是否小于右边的，如果是，则返回 true。		[ $a -lt $b ] 返回 true。
	-ge	检测左边的数是否大于等于右边的，如果是，则返回 true。	[ $a -ge $b ] 返回 false。
	-le	检测左边的数是否小于等于右边的，如果是，则返回 true。	[ $a -le $b ] 返回 true。

	#布尔运算符
	!	非运算，表达式为 true 则返回 false，否则返回 true。	[ ! false ] 				返回 true。
	-o	或运算，有一个表达式为 true 则返回 true。			[ $a -lt 20 -o $b -gt 100 ] 返回 true。
	-a	与运算，两个表达式都为 true 才返回 true。			[ $a -lt 20 -a $b -gt 100 ] 返回 false。

	#逻辑运算符
	&&	逻辑的 AND	[[ $a -lt 100 && $b -gt 100 ]] 返回 false
	||	逻辑的 OR	[[ $a -lt 100 || $b -gt 100 ]] 返回 true

	#字符串运算符
	=	检测两个字符串是否相等，相等返回 true。		[ $a = $b ] 	返回 false。
	!=	检测两个字符串是否相等，不相等返回 true。	[ $a != $b ] 	返回 true。
	-z	检测字符串是否为空，为0返回 true。		[ -z $a ] 		返回 false。
	-n	检测字符串长度是否为0，不为0返回 true。		[ -n $a ] 		返回 true。
	str	检测字符串是否为空，不为空返回 true。		[ $a ] 			返回 true。

	#文件测试运算符
	-b file	检测文件是否是块设备文件，如果是，则返回 true。			[ -b $file ] 返回 false。
	-c file	检测文件是否是字符设备文件，如果是，则返回 true。		[ -c $file ] 返回 false。
	-d file	检测文件是否是目录，如果是，则返回 true。				[ -d $file ] 返回 false。
	-f file	检测文件是否是普通文件（既不是目录，也不是设备文件），如果是，则返回 true。	[ -f $file ] 返回 true。
	-g file	检测文件是否设置了 SGID 位，如果是，则返回 true。		[ -g $file ] 返回 false。
	-k file	检测文件是否设置了粘着位(Sticky Bit)，如果是，则返回 true。	[ -k $file ] 返回 false。
	-p file	检测文件是否是有名管道，如果是，则返回 true。			[ -p $file ] 返回 false。
	-u file	检测文件是否设置了 SUID 位，如果是，则返回 true。		[ -u $file ] 返回 false。
	-r file	检测文件是否可读，如果是，则返回 true。					[ -r $file ] 返回 true。
	-w file	检测文件是否可写，如果是，则返回 true。					[ -w $file ] 返回 true。
	-x file	检测文件是否可执行，如果是，则返回 true。				[ -x $file ] 返回 true。
	-s file	检测文件是否为空（文件大小是否大于0），不为空返回 true。	[ -s $file ] 返回 true。
	-e file	检测文件（包括目录）是否存在，如果是，则返回 true。		[ -e $file ] 返回 true。
#### 流程控制
```shell script
num=$1
#统一按照这个格式,空格也是
if [ $1 -gt 80 ]; then
    echo 'good'
fi

#if elseif else
if [  ]; then
    
elif [  ]; then
    
else 

fi

#while
while [  ]; do
    
done

#case
case $x in
pattern1)
  echo 'pat 1'
  ;;
pattern2)
  echo 'pat 2'
  ;;
*)
 echo 'default' 
  ;;
esac

#for
for (( i = 0; i < n; i++ )); do
    
done

#for in
for i in {1..5} ; do
    
done
```
```shell script
#eg
num=1
numStr=abc123
#数字判断
if [ $num -eq 1 ]; then
    
fi

#字符串要用 = 判断
if [ "$numStr" = "abc123" ]; then
    
fi
```
~~~
#这里空格用 span 表示,方便解释
    #if
    if span [span (条件判断,也用空格) span]; span then
        这里一般使用TAB
    fi

	#while
		while condition
		do
		    command
		done	

	#case
		case 值 in
		模式1)
		    command1
		    command2
		    ...
		    commandN
		    ;;
		模式2）
		    command1
		    command2
		    ...
		    commandN
		    ;;
		esac	

		eg:
		echo '输入 1 到 4 之间的数字:'
		echo '你输入的数字为:'
		read aNum		#终端输入的信息
		case $aNum in
		    1)  echo '你选择了 1'
		    ;;
		    2)  echo '你选择了 2'
		    ;;
		    3)  echo '你选择了 3'
		    ;;
		    4)  echo '你选择了 4'
		    ;;
		    *)  echo '你没有输入 1 到 4 之间的数字'
		    ;;
		esac

	#for
	for var in item1 item2 ... itemN
	do
	    command1
	    command2
	    ...
	    commandN
	done	

	eg:
	item=(1 2 3 4 5 6 7)
	for t in ${item[@]}
	do
		echo $t
	done	

	eg:
	for ((i=0;i<=3;i++))
	do
		echo $i
	done
~~~
	
#### 函数
```shell script
#函数需要提前声明
abc() {
  #函数内部的运行体,这里的 $1 属于 abc
  echo "params is ${1}"    
}

#调用
abc 1
abc "im params"
```
#### 文件引入
```shell script
#### 引入方式 #source或者.加上空格再加上文件的绝对路径
#### 这里建议使用 . 不同的系统可能会有 source命令,而不会解析成shell引入文件

####文件引入1
#source 对应shell的绝对路径


####文件引入2
. 对应shell的绝对路径
```
#### 其他:
	把命令运行结果赋给变量
	a=`/usr/local/php/sbin/php-fpm`	#使用反引号
	echo `date +%Y%m%d`
	
	让命令停止几秒
	sleep 1

	查看一条命令的执行结果[这里指最近的一条执行命令]
	echo $? #0为成功,其他为失败
	
	输出颜色,
	32m是颜色,具体的数字对应不同的颜色
	\033是颜色标识,
	0m表示只针对当前生效,1m则对下面文字也生效,并且是使用1m所对应的颜色
	echo "\033[32m 这里是你的文字 \033[0m"
	
	#等待用户输入
	read aNum 
	#打印用户输入的值
    echo $aNum
