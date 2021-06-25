<?php

require "../vendor/autoload.php";

$app        = new \App\Application(realpath(__DIR__ . '/../'));
$uri        = $app->getRequest()->getUri();
$current    = 'Dashboard';
$baseConfig = $app->getConfig()->getConfig('config_hugo');
$config     = $baseConfig['two_level'];
$moreConfig = $baseConfig['more_level'];
$content    = $app->getFile()->getContent();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="Hogo– Creative Admin Multipurpose Responsive Bootstrap4 Dashboard HTML Template" name="description">
    <meta content="Spruko Technologies Private Limited" name="author">
    <meta name="keywords" content="html admin template, bootstrap admin template premium, premium responsive admin template, admin dashboard template bootstrap, bootstrap simple admin template premium, web admin template, bootstrap admin template, premium admin template html5, best bootstrap admin template, premium admin panel template, admin template"/>

    <!-- Favicon -->
    <link rel="icon" href="/hugo/assets/images/brand/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/x-icon" href="/hugo/assets/images/brand/favicon.ico" />

    <!-- Title -->
    <title>YH</title>

    <!--Bootstrap.min css-->
    <link rel="stylesheet" href="/hugo/assets/plugins/bootstrap/css/bootstrap.min.css">

    <!-- Dashboard css -->
    <link href="/hugo/assets/css/style.css" rel="stylesheet" />

    <!-- Custom scroll bar css-->
    <link href="/hugo/assets/plugins/scroll-bar/jquery.mCustomScrollbar.css" rel="stylesheet" />

    <!-- Horizontal-menu css -->
    <link href="/hugo/assets/plugins/horizontal-menu/dropdown-effects/fade-down.css" rel="stylesheet">
    <link href="/hugo/assets/plugins/horizontal-menu/horizontalmenu.css" rel="stylesheet">

    <!--Daterangepicker css-->
    <link href="/hugo/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />

    <!-- Sidebar Accordions css -->
    <link href="/hugo/assets/plugins/accordion1/css/easy-responsive-tabs.css" rel="stylesheet">

    <!-- Rightsidebar css -->
    <link href="/hugo/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

    <!-- Morris  Charts css-->
    <link href="/hugo/assets/plugins/morris/morris.css" rel="stylesheet" />

    <!---Font icons css-->
    <link href="/hugo/assets/plugins/iconfonts/plugin.css" rel="stylesheet" />
    <link href="/hugo/assets/plugins/iconfonts/icons.css" rel="stylesheet" />
    <link  href="/hugo/assets/fonts/fonts/font-awesome.min.css" rel="stylesheet">

    <!--marked pre code size-->
    <style>
        pre > code {
            font-size: 15px;
        }
    </style>

    <!--marked高亮css和js-->
    <!--<link href="/marked.min/monokai_sublime.min.css" rel="stylesheet">
    <script src="/marked.min/highlight.min.js"></script>-->
</head>

<body class="app sidebar-mini rtl">

<!--Global-Loader-->
<div id="global-loader">
    <img src="/hugo/assets/images/icons/loader.svg" alt="loader">
</div>

<div class="page">
    <div class="page-main">
        <!--app-header-->
        <div class="app-header header hor-topheader d-flex">
            <div class="container">
                <div class="d-flex">
                    <a class="header-brand" href="index.html">
                        YanHan
                    </a>
                    <a id="horizontal-navtoggle" class="animated-arrow hor-toggle"><span></span></a>
                    <!--<a href="#" data-toggle="search" class="nav-link nav-link  navsearch"><i class="fa fa-search"></i></a>--><!-- search icon -->

                    <div class="d-flex order-lg-2 ml-auto header-rightmenu">
                        <div class="dropdown">
                            <a  class="nav-link icon full-screen-link" id="fullscreen-button">
                                <i class="fe fe-maximize-2"></i>
                            </a>
                        </div><!-- full-screen -->
                        <div class="dropdown header-notify">
                            <a class="nav-link icon" data-toggle="dropdown" aria-expanded="false">
                                <i class="fe fe-bell "></i>
                                <span class="pulse bg-success"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow ">
                                <a href="#" class="dropdown-item text-center">4 New Notifications</a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item d-flex pb-3">
                                    <div class="notifyimg bg-green">
                                        <i class="fe fe-mail"></i>
                                    </div>
                                    <div>
                                        <strong>Message Sent.</strong>
                                        <div class="small text-muted">12 mins ago</div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item d-flex pb-3">
                                    <div class="notifyimg bg-pink">
                                        <i class="fe fe-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <strong>Order Placed</strong>
                                        <div class="small text-muted">2  hour ago</div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item d-flex pb-3">
                                    <div class="notifyimg bg-blue">
                                        <i class="fe fe-calendar"></i>
                                    </div>
                                    <div>
                                        <strong> Event Started</strong>
                                        <div class="small text-muted">1  hour ago</div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item d-flex pb-3">
                                    <div class="notifyimg bg-orange">
                                        <i class="fe fe-monitor"></i>
                                    </div>
                                    <div>
                                        <strong>Your Admin Lanuch</strong>
                                        <div class="small text-muted">2  days ago</div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-center">View all Notifications</a>
                            </div>
                        </div><!-- notifications -->
                        <div class="dropdown header-user">
                            <a class="nav-link leading-none siderbar-link"  data-toggle="sidebar-right" data-target=".sidebar-right">
										<span class="mr-3 d-none d-lg-block ">
											<span class="text-gray-white"><span class="ml-2">Alison</span></span>
										</span>
                                <span class="avatar avatar-md brround"><img src="/dist/img/avatar6.png" alt="Profile-img" class="avatar avatar-md brround"></span>
                            </a>
                        </div><!-- profile -->

                    </div>
                </div>
            </div>
        </div>
        <!--app-header end-->

        <!-- Horizontal-menu -->
        <div class="horizontal-main hor-menu clearfix">
            <div class="horizontal-mainwrapper container clearfix">
                <nav class="horizontalMenu clearfix">
                    <ul class="horizontalMenu-list">
                        <!--单独的子菜单-->
                        <?php foreach($config as $menu) { ?>
                        <li aria-haspopup="true"><a href="#" class="sub-icon <?php
                            if (in_array($uri,array_column($menu['menus'], 'url'))) {
                                echo 'active';
                            }
                            ?>"><i class="<?php echo $menu['icon']; ?>"></i> <?php echo $menu['name']; ?> <i class="fa fa-angle-down horizontal-icon"></i></a>
                            <?php if(isset($menu['menus'])) { ?>

                            <ul class="sub-menu">
                                <?php foreach ($menu['menus'] as $son) { ?>
                                <?php if($son['url'] == $uri) {
                                        $current = $menu['name'] . '&nbsp;&nbsp; : &nbsp;&nbsp;' .$son['name'];
                                    }
                                ?>
                                <li aria-haspopup="true"><a href="<?php echo $son['url']; ?>"><?php echo $son['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php } ?>

                        <!--列表子菜单-->
                        <?php foreach ($moreConfig as $moreMenu) { ?>
                        <li aria-haspopup="true">
                            <a href="#" class="sub-icon  <?php
                            if(in_array($uri, array_column_two($moreMenu['menus'], 'url'))) {
                                echo 'active' ;
                            }
                            ?>">
                                <i class="<?php echo $moreMenu['icon']; ?>"></i>
                                <?php echo $moreMenu['name']; ?>
                                <i class="fa fa-angle-down horizontal-icon"></i>
                            </a>
                            <div class="horizontal-megamenu clearfix">
                                <div class="container">
                                    <div class="mega-menubg">
                                        <div class="row">
                                            <?php foreach ($moreMenu['menus'] as $oneLevel) { ?>

                                             <div class="col-lg-3 col-md-12 col-xs-12 link-list">
                                                <ul>
                                                <?php foreach ($oneLevel as $value) { ?>
                                                    <?php if($value['url'] == $uri) {
                                                        $current = $moreMenu['name'] . '&nbsp;&nbsp; : &nbsp;&nbsp;' .$value['name'];
                                                    }
                                                    ?>
                                                    <li aria-haspopup="true"><a href="<?php echo $value['url']; ?>"><?php echo $value['name']; ?></a></li>
                                                <?php } ?>

                                                </ul>
                                            </div>

                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </nav>
                <!--Nav end -->
            </div>
        </div>
        <!-- Horizontal-menu end -->

        <!--Header submenu -->
        <div class="bg-white p-3 header-secondary header-submenu">
            <div class="container ">
                <div class="row">
                    <div class="col">
                        <div class="d-flex">
                            <a class="btn btn-danger" href="#"><i class="fe fe-rotate-cw mr-1 mt-1"></i> Refresh </a>
                        </div>
                    </div>
                    <div class="col col-auto">
                        <a class="btn btn-light mt-4 mt-sm-0" href="#"><i class="fe fe-help-circle mr-1 mt-1"></i>  Support</a>
                        <a class="btn btn-success mt-4 mt-sm-0" href="#"><i class="fe fe-plus mr-1 mt-1"></i> Add New</a>
                    </div>
                </div>
            </div>
        </div><!--End Header submenu -->

        <!-- app-content-->
        <div class="container content-area">

            <div class="side-app">

                <!-- page-header -->
                <div class="page-header">
                    <ol class="breadcrumb"><!-- breadcrumb -->
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $current; ?></li>
                    </ol><!-- End breadcrumb -->
                    <div class="ml-auto">
                        <div class="input-group">
                            <a  class="btn btn-primary text-white mr-2"  id="daterange-btn">
										<span>
											<i class="fa fa-calendar"></i> Events Settings
										</span>
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <a href="#" class="btn btn-secondary text-white" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Rating">
										<span>
											<i class="fa fa-star"></i>
										</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End page-header -->

                <!--marked.js id-->
                <div class="row" style="display: none" id="marked_content_div">
                    <div class="col-12 col-sm-12">
                        <div class="card" >
                            <div class="card-body" id="marked_content">
                            </div>
                        </div>
                    </div>
                </div>
                <!--End marked.js id-->

                <!--table-->
                <div class="row" style="display: none" id="table_data">
                    <div class="col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header custom-header">
                                <div>
                                    <h3 class="card-title">Products Details</h3>
                                    <h6 class="card-subtitle">Overview products information</h6>
                                </div>
                                <div class="card-options d-none d-sm-block">
                                    <div class="btn-group btn-sm">
                                        <button type="button" class="btn btn-light btn-sm">
                                            <span class="">Today</span>
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm">
                                            <span class="">Month</span>
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm">
                                            <span class="">Year</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0">
                                        <thead>
                                        <tr>
                                            <th>Product ID</th>
                                            <th>Product</th>
                                            <th>Product Cost</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>C234</td>
                                            <td>Mi LED Smart TV 4A 80</td>
                                            <td>$14,500</td>
                                            <td>2,977</td>
                                            <td><span class="badge badge-success">Available</span></td>
                                        </tr>
                                        <tr>
                                            <td>C389</td>
                                            <td>Thomson R9 122cm (48 inch) Full HD LED TV </td>
                                            <td>$30,000</td>
                                            <td>678</td>
                                            <td><span class="badge badge-primary">Limited</span></td>
                                        </tr>
                                        <tr>
                                            <td>C936</td>
                                            <td>Vu 80cm (32 inch) HD Ready LED TV</td>
                                            <td>$13,200</td>
                                            <td>4,922</td>
                                            <td><span class="badge badge-warning">Avilable</span></td>
                                        </tr>
                                        <tr>
                                            <td>C493</td>
                                            <td>Micromax 81cm (32 inch) HD Ready LED TV</td>
                                            <td>$15,100</td>
                                            <td>1,234</td>
                                            <td><span class="badge badge-secondary">Limited</span></td>
                                        </tr>
                                        <tr>
                                            <td>C729</td>
                                            <td>HP 200 Mouse &amp; Wireless Laptop Keyboard </td>
                                            <td>$5,987</td>
                                            <td>4,789</td>
                                            <td><span class="badge badge-danger">No Stock</span></td>
                                        </tr>
                                        <tr>
                                            <td>C529</td>
                                            <td>Digisol DG-HR3400 Router </td>
                                            <td>$11,987</td>
                                            <td>938</td>
                                            <td><span class="badge badge-success">Limited</span></td>
                                        </tr>
                                        <tr>
                                            <td>C367</td>
                                            <td>Dell WM118 Wireless Optical Mouse</td>
                                            <td>$4,700</td>
                                            <td>5,876</td>
                                            <td><span class="badge badge-secondary">Available</span></td>
                                        </tr>
                                        <tr>
                                            <td>C529</td>
                                            <td>Digisol DG-HR3400 Router </td>
                                            <td>$11,987</td>
                                            <td>938</td>
                                            <td><span class="badge badge-success">Limited</span></td>
                                        </tr>
                                        <tr>
                                            <td>C367</td>
                                            <td>Dell WM118 Wireless Optical Mouse</td>
                                            <td>$4,700</td>
                                            <td>5,876</td>
                                            <td><span class="badge badge-secondary">Available</span></td>
                                        </tr>
                                        <tr>
                                            <td>C298</td>
                                            <td>Dell 16 inch Laptop Backpack </td>
                                            <td>$678</td>
                                            <td>2,539</td>
                                            <td><span class="badge badge-cyan">Available</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- col end -->
                </div>

            </div><!--End side app-->
            <!--footer-->
            <footer class="footer">
                <div class="container">
                    <div class="row align-items-center flex-row-reverse">
                        <div class="col-lg-12 col-sm-12   text-center">
                            禅宗悟道、知易行难、给与得失、至死方休
                        </div>
                    </div>
                </div>
            </footer>
            <!-- End Footer-->

        </div>
        <!-- End app-content-->
    </div>
</div>
<!-- End Page -->

<!-- Back to top -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

<!-- Jquery js-->
<script src="/hugo/assets/js/vendors/jquery-3.2.1.min.js"></script>

<!--Bootstrap.min js-->
<script src="/hugo/assets/plugins/bootstrap/popper.min.js"></script>
<script src="/hugo/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!--Jquery Sparkline js-->
<!--<script src="/hugo/assets/js/vendors/jquery.sparkline.min.js"></script>-->
<!-- Chart Circle js-->
<!--<script src="/hugo/assets/js/vendors/circle-progress.min.js"></script>-->

<!-- Star Rating js-->
<script src="/hugo/assets/plugins/rating/jquery.rating-stars.js"></script>

<!--Moment js-->
<script src="/hugo/assets/plugins/moment/moment.min.js"></script>

<!-- Daterangepicker js-->
<script src="/hugo/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Horizontal-menu js -->
<script src="/hugo/assets/plugins/horizontal-menu/horizontalmenu.js"></script>

<!-- Sidebar Accordions js -->
<script src="/hugo/assets/plugins/accordion1/js/easyResponsiveTabs.js"></script>

<!--Time Counter js-->
<script src="/hugo/assets/plugins/counters/jquery.missofis-countdown.js"></script>
<script src="/hugo/assets/plugins/counters/counter.js"></script>

<!-- Custom scroll bar js-->
<script src="/hugo/assets/plugins/scroll-bar/jquery.mCustomScrollbar.concat.min.js"></script>

<!-- Rightsidebar js -->
<script src="/hugo/assets/plugins/sidebar/sidebar.js"></script>

<!-- ECharts js -->
<script src="/hugo/assets/plugins/echarts/echarts.js"></script>

<!--Morris  Charts js-->
<script src="/hugo/assets/plugins/morris/raphael-min.js"></script>
<script src="/hugo/assets/plugins/morris/morris.js"></script>

<!-- Custom-charts js-->
<script src="/hugo/assets/js/index5.js"></script>

<!-- Custom js-->
<script src="/hugo/assets/js/custom.js"></script>

<script src="/marked.min/marked.min.js"></script>
<script>
    const content = "<?php echo $content; ?>";
    if(content) {
        $('#marked_content_div').show();
        /*高亮设置可省*/
        /*hljs.initHighlightingOnLoad();
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
    }else {
        $('#table_data').show();
    }
</script>
</body>
</html>