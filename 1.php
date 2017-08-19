<?php 
ob_start('assert');
echo "file_put_contents('1.txt','test')";
ob_end_flush();