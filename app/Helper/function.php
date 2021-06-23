<?php

if(!function_exists('dd')) {
    /**
     * 打印信息
     */
    function dd()
    {
        if(PHP_SAPI === 'cli'){
            $symbol = "\n";
        }else{
            $symbol = "<br />" ;
            echo "<pre />";
        }
        if(func_get_args()) {
            foreach (func_get_args() as $key => $value) {
                echo "type: ".gettype($value).$symbol;
                echo "data: ";
                print_r($value) ;
                echo $symbol;
            }
        }
        exit();
    }
}

if(!function_exists('array_column_two')) {

    function array_column_two($array, $column): array
    {
        $value = [];
        array_map(
            function($element) use (&$value, $column){
                $temp = array_column($element,$column);
                $value = array_merge($value, $temp);
            },
            $array);
        return $value;
    }
}