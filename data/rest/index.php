<?php
$a = file_get_contents('log.txt');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>90秒倒计时</title>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/time_js.js"></script>

<link type="text/css" rel="stylesheet" href="css/time_css.css" />

</head>

<body>
<div class="game_time">

	<div class="hold">
		<div class="pie pie1"></div>
	</div>

	<div class="hold">
		<div class="pie pie2"></div>
	</div>

	<div class="bg"> </div>
	
	<div class="time"></div>
	
</div>
<script type="text/javascript">
	var a = "<?php echo $a;?>" ;
	countDown(a);
	var outTime = a*1000 +5 ;
	setTimeout(function(){
		window.close();
	},outTime) ;
</script>
<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';">
</div>
</body>
</html>
