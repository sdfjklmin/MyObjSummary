<?php
//1、创建画布
$im = imagecreatetruecolor(300,200);//新建一个真彩色图像，默认背景是黑色，返回图像标识符。另外还有一个函数 imagecreate 已经不推荐使用。
//2、加载外部图片
$im_new = imagecreatefromjpeg("./1.jpg");//返回图像标识符
$im_new_info = getimagesize("./1.jpg");//取得图像大小，返回一个数组。该函数不需要用到gd库。

imagecopy($im,$im_new,30,30,0,0,$im_new_info[0],$im_new_info[1]);//返回布尔值


//3、输出图像
header("content-type: image/png");
imagepng($im);//输出到页面。如果有第二个参数[,$filename],则表示保存图像
//4、销毁图像，释放内存
imagedestroy($im);