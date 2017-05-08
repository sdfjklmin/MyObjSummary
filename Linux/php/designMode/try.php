<?php
# 输出格式
echo "<pre>"; 

# 模式简码
$arg = 'gcz' ; 	

# 模式简码对应的文件
$mode = [
'dl'=>'Singleton',  		# 单例模式
'gc'=>'Factory',     		# 工厂模式
'gcz'=>'Observerable',      # 观察者模式
] ;

# 判断模式简码是否存在
if (empty($mode[$arg]) && !isset($mode[$arg])) {
	 exit('not find design mode');
}

# 判断对应简码文件是否存在
if (!file_exists($mode[$arg].'.php')) {
	exit('no file match');	
}
# 引入文件
require_once './'.$mode[$arg] .'.php';

# 测试

