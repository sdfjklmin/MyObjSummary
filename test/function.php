<?php
namespace MyObjSummary;
if(!function_exists('pr')) {
	function pr()
	{
		if(func_get_args()) {
			foreach (func_get_args() as $key => $value) {
				# code...
				echo "type: ".gettype($value)."<br />";
				echo "data: ";
					var_dump($value) ;
			}
		}
		exit();
	}
}