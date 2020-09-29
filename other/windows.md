#### 屏蔽更新
    +++++++++++++++++++++++++++
    + Windows Update          +
    +++++++++++++++++++++++++++
    1.Windows Update Medic Service: REG add "HKLM\SYSTEM\CurrentControlSet\Services\WaaSMedicSvc" /v "Start" /t REG_DWORD /d "4" /f
        4 关闭  ,  2 开启
    2. 运行“regedit”，打开注册表编辑器，定位到 “HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WaaSMedicSvc”，
       a.右侧找到“Start”键，右键点击“修改”，将数值改为“4”；默认为 3 ;   设置启动类型 (禁用|手动|自动)
       b.再找到“FailureActions”键，右键点击“修改”，修改该键的二进制数据，
         将“0010”、“0018”行的左起第5个数值由原来的“01”改为“00”，修改完成保存关闭；  设置恢复关系(无操作|重启服务|...)
