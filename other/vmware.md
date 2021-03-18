1. Could not open /dev/vmmon: ???. Please make sure that the kernel module `vmmon’ is loaded.
   
   sudo /etc/init.d/vmware strt 或 ystemctl start vmware

2. he Vm can't run. please check you vm.log

3. This host supports Intel VT-x,but Intel VT-x is disabled

    解决方法：
    进入BIOS设置实用程序，VirtualizationTechnology改成Enabled，保存并退出!
    然后重新启动计算机，Intel的虚拟化技术开启成功。