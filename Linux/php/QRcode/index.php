<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <meta name="Keywords" content="生成二维码"/>
    <style>
        html,body{margin:0;padding:0;font-size:14px;font-family:"microsoft yahei",arial;background-color:#F2F2F2;}
        ul{margin:0;padding:0;}
        li{list-style:none;}
        input{border:0 none;}
        input:focus{outline:none;}
        pre{font-size:14px;line-height:20px;}
        .tc{text-align:center;}
        .title{letter-spacing:3px;text-shadow:0 0 2px #999;margin:30px auto 20px;}
        #qrcode li{padding:10px 0;}
        .ipt{padding:8px 10px;width:280px;font-size:14px;border:1px solid #ccc;}
        #submit{width:300px;padding:10px 0;background-color:#0074A2;color:#fff;font-size:16px;border-radius:4px;cursor:pointer;letter-spacing:2px;}
        #toast{width:300px;position:fixed;top:2%;right:1%;z-index:999999;background-color:rgba(0,0,0,.7);border-radius:5px;color:#fff;padding:10px 0;text-align:center;-webkit-animation: zoomOut .4s ease both;animation: zoomOut .4s ease both;}
        @-webkit-keyframes zoomOut { 0% { opacity: 0; -webkit-transform: scale(.6); } 100% { opacity: 1; -webkit-transform: scale(1); } }
        @keyframes zoomOut { 0% { opacity: 0; transform: scale(.6); } 100% { opacity: 1; transform: scale(1); } }
    </style>
</head>
</html>
<?php
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


    if (isset($_REQUEST['data'])) {
//        exit($_REQUEST['data']);
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');
            
        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        
    } else {    

        //default data
        echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    }    

    //display generated file

   echo '<img  style="margin-left: 45%" src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';
?>

<body>
<h1  class="title tc">生成二维码</h1>
<form autocomplete="off" id="QR_Code" method="post" onsubmit="checkCode()">
    <ul id="qrcode" class="tc">
        <li>
            <input type="text" name="data" value="" placeholder="请输入二维码内容，文本／链接" class="ipt" required />
        </li>
        <li>
            <input type="text" value="" placeholder="请输入二维码尺寸，1-10之间" class="ipt" />
        </li>
        <li>
            <input type="text" value="" placeholder="请输入二维码白色边框尺寸，整数即可" class="ipt"  />
        </li>
        <li>
            <img src="" id="qrcodes"/>
        </li>
        <li>
            <input type="submit" value="生成二维码" id="submit"/>
        </li>
    </ul>
</form>
</body>




    