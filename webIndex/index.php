<?php
$menus = require '../conf.php';
define('APP_PATH','../');
/**
 * Class PDOConnect
 * @author sjm
 */
class PDOConnect
{
	private $link;

	protected $config = [
		// 数据库类型
		'type'            => '',
		// 服务器地址
		'hostname'        => '',
		// 数据库名
		'database'        => '',
		// 用户名
		'username'        => '',
		// 密码
		'password'        => '',
		// 端口
		'port'        => '',
		// 连接dsn
		'dsn'             => '',
		// 数据库连接参数
		'params'          => [],
		// 数据库编码默认采用utf8
		'charset'         => 'utf8',
		// 数据库表前缀
		'prefix'          => '',
		// 数据库调试模式
		'debug'           => false,
	];

	/**
	 * PDOConnect constructor.
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		/* Connect to a MySQL database using driver invocation */
		$dsn      = 'mysql:dbname=tt;host=127.0.0.1';
		$user     = 'root';
		$password = 'root123';
		try {
			/** @var \PDO $dbh */
			$dbh = new \PDO($dsn, $user, $password);
			$this->link = $dbh;
		} catch (\PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();die();
		}
	}

	/** 查询
	 * @param $sql
	 * @return array
	 */
	public function query($sql)
	{
		$data = [];
		foreach ($this->link->query($sql,\PDO::FETCH_ASSOC) as $row) {
			$data[] = $row;
		}
		return $data;
	}
}

/**
 * Class SimpleRoute
 * @author sjm
 */
class SimpleRoute
{
	/** 允许的后缀
	 * @var string[]
	 */
    private $allowExt = ['md','php','html','conf'];

	/** 允许访问PDO的路由
	 * @var string[]
	 */
	private $allowPDO = ["/try/tt?ext=html"];

	/** 格式化代码
	 * @var string[]
	 */
	private $allowCodePre = ['php','conf'];

	/**
	 * SimpleRoute constructor.
	 */
    public function __construct()
	{
        if(PHP_SAPI == 'cli') {
            exit('Please use web!');
        }
	}

	/**
	 * @return mixed|string
	 */
	public function getCurrentUrl(): string
    {
		return $_SERVER['PATH_INFO'] ?? '';
	}

	/**
	 * @return mixed|string
	 */
	public function getCurrentURI(): string
    {
		return $_SERVER['REQUEST_URI'] ?? '';
	}

	/**
	 * @return array|false
	 */
	private function getExt()
    {
		if (isset($_GET['ext']) && !empty($_GET['ext'])) {
			$ext       = strtolower($_GET['ext']);
		} else {
			$ext       = 'md';
		}
		if(!in_array($ext,$this->allowExt)) {
		    return false;
        }
		switch ($ext) {
			case "md":
				$lineBreak = '\n';
				break;
			case "html":
				$lineBreak = '';
				break;
			default :
				$lineBreak = '<br />';
				break;
		}
		return [$ext,$lineBreak];
    }

	/** 设置代码格式
	 * @param $content
	 * @param $ext
	 * @return string
	 */
    private function codePre($content, $ext): string
    {
        if(in_array($ext,$this->allowCodePre)) {
            return "<pre style='font-size: 18px'>{$content}</pre>";
        }
        return $content;
    }

	/** 页面渲染
	 * @param mixed $data
	 * @param $file
	 * @return false|string
	 */
    protected function displayWithObCache($data, $file)
    {
        // 解析变量
        extract($data);
		// 页面缓存
		ob_start();
		ob_implicit_flush(0);
		// 渲染输出
		try {
			require $file;
		} catch (\Exception $e) {
			ob_end_clean();
			throw $e;
		}
		// 获取并清空缓存
		return ob_get_clean();
    }

	/** 获取内容
	 * @param $file
	 * @return false|string
	 * @throws Exception
	 */
    private function getContent($file)
    {
        /*if(in_array($this->getCurrentURI(),$this->allowPDO)) {
			$link = new PDOConnect();
			$list = $link->query("select * from english_word limit 10");
			$content = $this->displayWithObCache(['title' => 'English Word','list' => $list],$file);
        }else{
			$content = file_get_contents($file);
		}*/
        return file_get_contents($file);
    }

    /**
     * @return false|string|string[]
     * @throws Exception
     */
	public function getCurrentContent()
    {
	    $action = $this->getCurrentUrl();
	    if(!$action) {
			//$action    = 'README';
			$action      = 'index';
			$_GET['ext'] = 'html';
	    }
	    $action = ltrim($action,'/');
	    $extLine = $this->getExt();
	    if($extLine === false) {
			return "#### Errors : Ext Not Permission";
        }
		list($ext,$lineBreak) = $extLine;
	    $file =  APP_PATH.$action.'.'.$ext;
	    if(!file_exists($file)) {
	        return "#### Errors : File Not Find";
        }
	    $content = $this->getContent($file);
	    $content = $this->codePre($content,$ext);
		//添加反斜杠
		$content = addslashes($content);
		//替换
		$search  = array('
','../webIndex');
		$replace = array($lineBreak,'');
		return str_replace($search,$replace,$content);
	}
}

$model     = new SimpleRoute();
$activeUrl = $model->getCurrentURI();
try {
    $content = $model->getCurrentContent();
} catch (Exception $e) {
    $content = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>Sokmin</title>

  <link id="el_favicon" rel="shortcut icon" href="/dist/logo/round.png">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="/dist/front/family.css" rel="stylesheet">

  <!--marked高亮css和js-->
  <!--<link href="/marked.min/monokai_sublime.min.css" rel="stylesheet">
  <script src="/marked.min/highlight.min.js"></script>-->

</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </nav>

  <!-- Main Sidebar Container :　sidebar-dark-primary 通过样式换肤 -->
  <!--<aside class="main-sidebar sidebar-light-olive elevation-4">-->
  <aside class="main-sidebar elevation-4 sidebar-light-teal">
  <!--<aside class="main-sidebar elevation-4 sidebar-dark-primary">-->
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="/dist/img/avatar6.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Sokmin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <!-- 侧边栏菜单 -->
        <!--nav-child-indent 子类缩进-->
        <ul class="nav nav-pills nav-sidebar flex-column  nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li></li>
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <!--大标题-->
          <!--<li class="nav-header">EXAMPLES</li>-->
          <!--has-treeview:有子类,menu-open:菜单打开 -->
          <!--<li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index2.html" class="nav-link ">
                  <p>Dashboard v2</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./index2.html" class="nav-link active">
                  <p>Dashboard v2</p>
                </a>
              </li>

            </ul>
          </li>
          <li class="nav-item">
            <a href="widgets.html" class="nav-link">
              <p>
                Widgets
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>-->
            <?php
                foreach ($menus as $intro) {
                    if(!empty($intro['intro'])) {
						$header = '<li class="nav-header"> <h6><b>'.$intro['intro'].'</b></h6></li>';
						echo $header;
                    }
					foreach ($intro['menu'] as $menu) {
						if(!empty($menu['menu'])) {
						    $haveOpen = '';
						    $pActive = '';
							$urls = array_column($menu['menu'],'url');
						    if(in_array($activeUrl,$urls)) {
								$haveOpen = 'menu-open';
								$pActive = 'active';
							}
							$prefix = '<li class="nav-item has-treeview '.$haveOpen.'">
                <a href="#" class="nav-link /*'.$pActive.'*/">
                <i>'.$menu['title'].'</i>
                    <p>
                        <!--这里可以拼接标题-->
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                                <ul class="nav nav-treeview">
                ';
							foreach ($menu['menu'] as $son) {
							    $isActive = '';
							    if($son['url'] == $activeUrl) {
							        $isActive = 'active';
                                }
								$prefix .= '
                <li class="nav-item">
                        <a href="'.$son['url'].'" class="nav-link '.$isActive.'">
                            <!--这里 i 变成 p 即可成普通样式-->
                            <i class="text-sm">'.$son['title'].'</i>
                        </a>
                    </li>';
							}
							$leftMenu = $prefix .'
                </ul>
            </li>';
						}else{
							$isActive = '';
							if($menu['url'] == $activeUrl) {
								$isActive = 'active';
							}
							$leftMenu = '
                           <li class="nav-item">
                            <a href="'.$menu['url'].'" class="nav-link '.$isActive.'">
                              <p>
                               '.$menu['title'].'
                              </p>
                              <!-- <span class="right badge badge-danger">New</span>-->
                            </a>
                           </li>';
						}
						echo $leftMenu;
					}
				}
            ?>
            <li style="height: 100px"></li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="card card-solid" style="margin-top: 62px">
            <div style="margin: 27px" id="marked_content">

            </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.IO</a></strong>
   <!-- All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.5
    </div>-->
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- overlayScrollbars -->
<script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/adminlte.js"></script>
<!--./ REQUIRED SCRIPTS -->

<script src="/marked.min/marked.min.js"></script>
<script>
    const content = "<?php echo $content; ?>";
    /*高亮设置可省*/
   /* hljs.initHighlightingOnLoad();
    const rendererMD = new marked.Renderer();
    marked.setOptions({
        renderer: rendererMD,
        gfm: true,
        tables: true,
        breaks: false,
        pedantic: false,
        sanitize: false,
        smartLists: true,
        smartypants: false
    });
    marked.setOptions({
        highlight: function (code) {
            return hljs.highlightAuto(code).value;
        }
    });*/
    /*.高亮部分*/
    document.getElementById('marked_content').innerHTML = marked(content);
</script
</body>
</html>
