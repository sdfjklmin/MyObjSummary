<?php


namespace app\vdsns\controller\v1;


use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\Request;

class Gii extends Controller
{
	/** 生成TP模型
	 * @param string $namespace 命名空间
	 * @param string $table  表名
	 * @param string $fix 前缀
	 */
	public function createModel()
	{
		if(Request::post()) {
			$fix       = Request::post('fix');
			$table     = Request::post('table');
			$namespace = Request::post('namespace');
			$modelPath = Request::post('model_path');
			if(!$fix || !$table || !$namespace || !$modelPath) {
				$this->tipMsg('Error Params');
				exit();
			}
			$path      = \think\facade\App::getRootPath() . $modelPath;
			//完整表名
			$initTable = trim($fix.$table);
			try {
				//获取表信息
				$tableColumns = Db::query("show full columns from {$initTable}");
			}catch (\Exception $e) {
				$this->tipMsg($e->getMessage());
				exit();
			}
			//将表名转换为类名
			$className      = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
			//文件名称
			$file           = $path . $className . '.php';
			//头部信息
			$title          = "<?php" . "\n" . "\n";
			$title         .= 'namespace ' . $namespace . ';' . "\n";
			//属性方法内容
			$title         .= "\n" . 'use think\Model;' . "\n";
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
				$this->tipMsg('File already exists, please overwrite');
				var_dump($class) ;
				die();
			}
			//文件填充
			file_put_contents($file,$class,FILE_APPEND);
			//更改文件权限
			chmod($file, 0777);
			$this->tipMsg('Success');
			exit();
		}
		$config = Config::pull('gii');
		if(!$config) {
			$this->tipMsg('Please add config file :  gii.php, content: <br/>
return [ <br/>
	//开关 <br/>
	\'switch\'     => true,<br/>
	//表前缀 <br/>
	\'fix\'        => \'pyjy_\', <br/>
	//model namespace <br/>
	\'namespace\'  => \'app\vdsns\model\v2\', <br/>
	//model path <br/>
	\'model_path\' => \'application/vdsns/model/v2/\', <br/>
];
');
		exit();
		}
		if(!$config['switch']) {
			$this->tipMsg('Gii Switch is close');exit();
		}
		$fix       = $config['fix'];
		$namespace = $config['namespace'];
		$modelPath = $config['model_path'];
		return '
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<div style="padding: 24px 48px;width:50%">
    <p style="line-height: 1.6em;font-size: 42px"> ThinkPHP V5.1 Expand Gii Model <br/> <span style="font-size:30px">Gii Model Create</span></p>
	<form method="post" action="">
	  <div class="form-group">
		<label for="tablePrefix">Table Prefix</label>
		<input type="text" name="fix" value="'.$fix.'" class="form-control" id="tablePrefix">
	  </div>
	  <div class="form-group">
		<label for="modelNamespace">Model Namespace</label>
		<input type="text" name="namespace" value="'.$namespace.'"  class="form-control" id="modelNamespace">
	  </div> 
	  <div class="form-group">
		<label for="modelPath">Model Path</label>
		<input type="text" name="model_path" value="'.$modelPath.'"  class="form-control" id="modelPath">
	  </div>
	  <div class="form-group">
		<label for="tableName">Table Name</label>
		<input type="text" name="table" value=""  class="form-control" id="tableName">
	  </div>
	  <button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
';
	}

	/**
	 * @param $msg
	 */
	private function tipMsg($msg)
	{
		echo '
			<div style="padding: 24px 48px">
    		<p style="line-height: 1.6em;font-size: 30px">
    		 	<span style="color: grey"> '.$msg.' </span> <br/> 
				<span style="font-size:20px">By Gii Model Create</span> <br/>
				<a style="text-decoration: none;font-size: 18px" href="javascript:history.go(-1)">Back</a>
    		</p>
    		</div>
			';
	}
}