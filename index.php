<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/11/22
 * Time: 11:41
 */
//重定向页面 非index入口重定向到index
//header('location:/index.php');
//以index为入口,根据入口访问路由做路由分配
//echo phpinfo();

//目录入口
defined('APP_ROOT') or define('APP_ROOT','./');
//不需要生成的 后缀
defined('NOT_LINK') or define('NOT_LINK', ['.','frame','html']) ;
defined('NOT_SUFFIX') or define('NOT_SUFFIX', ['png','md','jpg','zip']) ;

$label = getLink();
function getLink( $label = '',$directory = APP_ROOT ,$link = '')
{
    $dirs  = scandir($directory) ;
    if(!empty($link)) {
        $link .= ' - ' ;
    }
    foreach ($dirs as $dir) {
        if( $dir[0] === '.' ||  in_array($dir,NOT_LINK) ) {
            continue ;
        }
        if(is_dir($directory.$dir)) {
            if($link) {
                $label .= '<h2>'.$link .' - '.$dir.'</h2>';
            }else{
                $label .= '<h2>'.' - '.$dir.'</h2>';
            }
            $label  = getLink($label,$directory.$dir.'/',' - ') ;
        }else{
            $dirArr = explode('.',$dir) ;
            if( count($dirArr) == 2 && !in_array($dirArr[1],NOT_SUFFIX) ) {
                $label .= "<li><a href='".$directory.$dir."'> $link  $dir </a></li>";
            }else{
                $label .= "<li><a> $link  $dir </a></li>";
            }
        }
    }
    return $label ;
}
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="icon" href="/favicon.png" type="image/png">
        <title>PHP</title>
    </head>
    <style>
        /*去横线,去点*/
        li,a{
            text-decoration:none;
            list-style-type:none;
        }
    </style>
    <ul>
        <?php echo $label ; ?>
    </ul>
</html>
