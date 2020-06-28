<?php
$menus = require '../conf.php';
define('APP_PATH','../');
class SimpleRoute
{
    public function __construct()
	{
        if(PHP_SAPI == 'cli') {
            exit('Please use web!');
        }
	}

	public function getCurrentUrl()
	{
		return $_SERVER['PATH_INFO'] ?? '';
	}

	public function getCurrentURI()
	{
		return $_SERVER['REQUEST_URI'] ?? '';
	}

	public function getCurrentContent()
    {
	    $action = $this->getCurrentUrl();
	    if(!$action) {
            $action = 'README';
	    }
	    $action = ltrim($action,'/');
		if (isset($_GET['ext']) && !empty($_GET['ext'])) {
			$ext       = $_GET['ext'];
			$lineBreak = '<br />';
		} else {
			$ext       = 'md';
			$lineBreak = '\n';
		}
	    $file =  APP_PATH.$action.'.'.$ext;
	    if(!file_exists($file)) {
	        return "#### errors : file not find";
        }
	    $content = file_get_contents($file);
		//添加转义符
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
$content   = $model->getCurrentContent();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>SokminYo | Dashboard 2</title>

  <!--<link id="el_favicon" rel="shortcut icon" href="/dist/img/icons.png">-->
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="/dist/front/family.css" rel="stylesheet">

  <!--marked高亮css和js-->
  <link href="/marked.min/monokai_sublime.min.css" rel="stylesheet">
  <script src="/marked.min/highlight.min.js"></script>

</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <!-- Main Sidebar Container :　sidebar-dark-primary 通过样式换肤 -->
  <!--<aside class="main-sidebar sidebar-light-olive elevation-4">-->
  <aside class="main-sidebar elevation-4 sidebar-light-teal">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="/dist/img/avatar04.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">SokminYo</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <!-- 侧边栏菜单 -->
        <ul class="nav nav-pills nav-sidebar flex-column <!--nav-flat-->" data-widget="treeview" role="menu" data-accordion="false">
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
                    <p>
                        '.$menu['title'].'
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
                            <p class="text-sm">'.$son['title'].'</p>
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
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-top: 8px">
    <!-- Main content -->
    <section class="content" >
        <div class="card card-solid" >
            <div style="margin: 57px" id="marked_content">

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
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.5
    </div>
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
<!--marked.min高亮加载-->
<script>hljs.initHighlightingOnLoad();</script>
<script>
    const content = "<?php echo $content; ?>";
    /*高亮设置可省*/
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
    });
    /*.高亮部分*/
    document.getElementById('marked_content').innerHTML = marked(content);
</script
</body>
</html>
