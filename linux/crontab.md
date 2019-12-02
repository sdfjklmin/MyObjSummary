## crontab配置文件
Linux下的任务调度分为两类：系统任务调度和用户任务调度。
Linux系统任务是由 cron (crond) 这个系统服务来控制的，这个系统服务是默认启动的。
用户自己设置的计划任务则使用crontab 命令。

在CentOS系统中

    cat /etc/crontab
    
配置文件可以看到如下解释：

	SHELL=/bin/bash
	PATH=/sbin:/bin:/usr/sbin:/usr/bin
	MAILTO=root
	HOME=/
	# For details see man 4 crontabs
	# Example of job definition:
	# .---------------- minute (0 - 59)
	# | .------------- hour (0 - 23)
	# | | .---------- day of month (1 - 31)
	# | | | .------- month (1 - 12) OR jan,feb,mar,apr ...
	# | | | | .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
	# | | | | |
	# * * * * * user-name command to be executed
	
前四行是用来配置crond任务运行的环境变量:
    
    第一行: SHELL变量指定了系统要使用哪个shell，这里是bash；
    第二行: PATH变量指定了系统执行命令的路径；
    第三行: MAILTO变量指定了crond的任务执行信息将通过电子邮件发送给root用户，如果MAILTO变量的值为空，则表示不发送任务执行信息给用户；
    第四行: HOME变量指定了在执行命令或者脚本时使用的主目录。

用户定期要执行的工作，比如用户数据备份、定时邮件提醒等。
用户可以使用 crontab 工具来定制自己的计划任务。
所有用户定义的crontab 文件都被保存在 /var/spool/cron目录中。其文件名与用户名一致。

## crontab文件含义

用户所建立的crontab文件中，每一行都代表一项任务，每行的每个字段代表一项设置，它的格式共分为六个字段，前五段是时间设定段，第六段是要执行的命令段，格式如下：

    minute  hour day month week command
      分     时   日   月    周   命令
    
    在以上各个字段中，还可以使用以下特殊字符：
    
    "*"代表所有的取值范围内的数字，如月份字段为*，则表示1到12个月；
    
    "/"代表每一定时间间隔的意思，如分钟字段为*/10，表示每10分钟执行1次。
    
    "-"代表从某个区间范围，是闭区间。如“2-5”表示“2,3,4,5”，小时字段中0-23/2表示在0~23点范围内每2个小时执行一次。
    
    ","分散的数字（不一定连续），如1,2,3,4,7,9。
    
    注：由于各个地方每周第一天不一样，因此Sunday=0（第一天）或Sunday=7（最后1天）。


## crontab命令详解


    crontab [-u user] file
    crontab [ -u user ] [ -i ] { -e | -l | -r }
• -u user：用于设定某个用户的crontab服务；

• file: file为命令文件名，表示将file作为crontab的任务列表文件并载入crontab；

• -e：编辑某个用户的crontab文件内容，如不指定用户则表示当前用户；

• -l：显示某个用户的crontab文件内容，如不指定用户则表示当前用户；

• -r：从/var/spool/cron目录中删除某个用户的crontab文件。

• -i：在删除用户的crontab文件时给确认提示。

## crontab注意点


1.crontab有2种编辑方式：直接编辑/etc/crontab文件与crontab –e，其中/etc/crontab里的计划任务是系统中的计划任务，而用户的计划任务需要通过crontab –e来编辑；

2.每次编辑完某个用户的cron设置后，cron自动在/var/spool/cron下生成一个与此用户同名的文件，此用户的cron信息都记录在这个文件中，这个文件是不可以直接编辑的，只可以用crontab -e 来编辑。

3.crontab中的command尽量使用绝对路径，否则会经常因为路径错误导致任务无法执行。

4.新创建的cron job不会马上执行，至少要等2分钟才能执行，可重启cron来立即执行。

5.%在crontab文件中表示“换行”，因此假如脚本或命令含有%,需要使用\%来进行转义。

## crontab配置实例

每一分钟执行一次command（因cron默认每1分钟扫描一次，因此全为*即可）

    *    *    *    *    *  command

每小时的第3和第15分钟执行command

    3,15   *    *    *    *  command

每天上午8-11点的第3和15分钟执行command

    3,15  8-11  *  *  *  command

每隔2天的上午8-11点的第3和15分钟执行command

    3,15  8-11  */2  *   *  command

每个星期一的上午8点到11点的第3和第15分钟执行command

    3,15  8-11   *   *  1 command

每晚的21:30重启smb

    30  21   *   *  *  /etc/init.d/smb restart

每月1、10、22日的4 : 45重启smb

    45  4  1,10,22  *  *  /etc/init.d/smb restart

每周六、周日的1 : 10重启smb

    10  1  *  *  6,0  /etc/init.d/smb restart

每天18 : 00至23 : 00之间每隔30分钟重启smb

    0,30  18-23  *  *  *  /etc/init.d/smb restart

每一小时重启smb

    *  */1  *  *  *  /etc/init.d/smb restart

晚上11点到早上7点之间，每隔一小时重启smb

    *  23-7/1  *   *   *  /etc/init.d/smb restart

每月的4号与每周一到周三的11点重启smb

    0  11  4  *  mon-wed  /etc/init.d/smb restart

每小时执行/etc/cron.hourly目录内的脚本

    0  1   *   *   *     root run-parts /etc/cron.hourly

## 参见问题和优化

* CRON: pam_unix(cron:session): session opened for user root by (uid=0)
            
  CRON: pam_unix(cron:session): session closed for user root
  
  `cron的日志信息会不停的记录上面的信息`
  
  `cat /var/log/auth.log`
  ~~~
  cd /etc/pam.d
  vi common-session-noninteractive
  #查找 session required        pam_unix.so
  #在它的上方添加如下内容
  session     [success=1 default=ignore] pam_succeed_if.so service in cron quiet use_uid
  #保存并退出
  service cron restart
  #systemctl restart cron.service
  ~~~