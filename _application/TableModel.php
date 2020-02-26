<?php


class TableModel
{
	/**
	 * @param string $namespace 命名空间
	 * @param string $table  表名
	 * @param string $fix 前缀
	 */
	public function createModel($table, $namespace = 'app\vdsns\model\v2', $fix = 'pyjy_')
	{
		$path = \think\facade\App::getRootPath().'application/vdsns/model/v2/';
		//完整表名
		$initTable = trim($fix.$table);
		//获取表信息
		$tableColumns = Db::query("show full columns from {$initTable}");
		//将表名转换为类名
		$className =  str_replace(' ','',ucwords(str_replace('_',' ',$table)));
		//文件名称
		$file = $path.$className.'.php';
		//头部信息
		$title = "<?php"."\n"."\n";
		$title .= 'namespace '.$namespace.';'."\n";
		$title .= "\n".'use think\Model;'."\n";
		//属性方法内容
		$tableAttribute = 'return [';
		//类属性注释
		$tableAttr = '
/**
 * Class '.$className;
		foreach ($tableColumns as $attr) {
			$tableAttr .= "\n".' * @property '.'$'.$attr['Field'].($attr['Comment'] ? ' '.$attr['Comment'] : '');
			$tableAttribute .= "\n".'               "'.$attr['Field'].'" => "'.ucwords(str_replace('_',' ',$attr['Field'])).'",';
		}
		$tableAttr .= "\n".' */'."\n";
		$tableAttribute .= "\n".'           ];';
		$class = $title.$tableAttr.'class '.$className.' extends Model'."\n";
		$class .= '{'."\n".'    protected $name = "'.$table.'";'."\n"."
    /** table attr
     * @return array
     */    
    public function attributeLabels()
    {
        {$tableAttribute}
    }"."\n";
		$class .= '}';
		if(file_exists($file)) {
			echo '文件已经存在,请自行进行覆盖';
			var_dump($class) ;
			die();
		}
		//文件填充
		file_put_contents($file,$class,FILE_APPEND);
		//更改文件权限
		chmod($file, 0777);
		echo '创建成功';exit();
	}
}