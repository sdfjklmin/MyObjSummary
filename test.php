<?php 
echo "<pre>" ;
// $test = [] ;
// for ($t=1; $t < 101; $t++) { 
// 	# code...
// 	$test[] = $t;
//  《青:游衫》
//  游衫
$test = 
[
     1,2,3,4,5,6,7,8,9,10,
     11,12,13,14,15,16,17,18,19,20,
     21,22,23,24,25,26,27,28,29,30,
     31,32,33,34,35,36,37,38,39,40,
     41,42,43,44,45,46,47,48,49,50,
     51,52,53,54,55,56,57,58,59,60,
     61,62,63,64,65,66,67,68,69,70,
     71,72,73,74,75,76,77,78,79,80,
     81,82,83,84,85,86,87,88,89,90,
     91,92,93,94,95,96,97,98,99,100
    ] ;

$sum = 101 ; 
sort($test);
$c  = count($test) ;
$m  = $c-1 ;
$d  = 5050 - array_sum($test) ;
foreach ($test as $key => $value) {
	# code...
	if(($check = $value + $test[$m-$key]) != $sum) {
          if(!in_array($value+1, $test)) {
               echo $value+1 ,'<br>',$d-($value+1);
               break ;
          }
          if(!in_array($value-1, $test)) {
               echo $value-1 ,'<br>',$d-($value-1);
               break ;
          }
          if(!in_array($test[$m-$key]-1, $test)) {
               echo $test[$m-$key]-1 ,'<br>',$d-($test[$m-$key]-1);
               break ;
          }
          if(!in_array($test[$m-$key]+1, $test)) {
               echo $test[$m-$key]+1 ,'<br>',$d-($test[$m-$key]+1);
               break ;
          }
	}
}
exit();


