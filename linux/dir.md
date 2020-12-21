

    / 根目录
    ├── bin(binaries)               二进制可执行命令
    ├── sbin(super user binaries)   存放二进制可执行文件，只有root才能访问,超级管理命令，
    ├── dev(devices)                设备特殊文件
    ├── lib(library)                标准程序设计库，又叫动态链接共享库，作用类似windows里的.dll文件
    ├── tmp(temporary)              公共的临时文件存储点
    ├── etc(etcetera)               系统管理和配置文件
    │   ├── rc.d                    启动的配置文件和脚本
    │   ├── bashrc|bash.bashrc      自定义别名
    │   ├── hosts                   hosts文件
    │   ├── profile                 可以将安装命令配置成系统命令
    │   ├── init.d                  系统服务的管理（启动与停止）脚本
    │       ├── dns-clean start     清除dns:修改hosts后操作
    │       ├── networking restart  重启网络:修改hosts后操作
    ├── home                        用户主目录的基点,比如用户user的主目录就是/home/user,可以用~user表示
    ├── root                        系统管理员的主目录
    ├── snap                        ubunut软件包
    ├── mnt(mount)                  系统提供这个目录是让用户临时挂载其他的文件系统
    ├── lost+found                  这个目录平时是空的，系统非正常关机而留下“无家可归”的文件
    ├── proc                        虚拟的目录，是系统内存的映射。可直接访问这个目录来获取系统信息。
    │   ├── cpuinfo                 `cat /proc/cpuinfo`
    │   ├── meminfo                 `cat /proc/meminfo`
    ├── var(variable)               用于存放运行时需要改变数据的文件,某些大文件的溢出区，比方说各种服务的日志文件
    ├── usr(unix shared resources)  最庞大的目录，要用到的应用程序和文件几乎都在这个目录
    │   ├── local                   用户级的程序目录
    │       ├── bin                 全局可执行命令,自定义的命令需要 chmod +x command

