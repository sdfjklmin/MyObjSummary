<?php
# 输出格式
echo "<pre>"; 

# 模式简码
$arg = 'gc' ; 	

# 模式简码对应的文件
$mode = [
'dl'=>'Singleton',   # 单例模式
'gc'=>'Factory',     # 工厂模式
] ;

# 判断是否存在
if (empty($mode[$arg]) && !isset($mode[$arg])) {
	 exit('not find design mode');
}

# 引入文件
require_once './'.$mode[$arg] .'.php';

# 测试

