<?php

    /** 冒泡
     * @param $arr
     * @return mixed
     */
    function bubbleSort($arr)
    {
        //按照大小顺序一个一个地像气泡一样浮出水面
        //可以从左到右依次冒泡，把较大的数往右边挪动即可
        //最坏的情况的情况下，需要进行 n*(n-1)/2 次比较
        $count = count($arr);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i; $j < $count-1; $j++) {
                //左边大于右边
                if ($arr[$i] > $arr[$j+1]) {
                    //交换两者的位置
                    $temp      = $arr[$i];
                    $arr[$i]   = $arr[$j+1];
                    $arr[$j+1] = $temp;
                }
            }
        }
        return $arr;
    }


    /** 插入排序
     * @param $arr
     * @return mixed
     */
    function insertSort($arr)
    {
        //不断地将尚未排好序的数插入到已经排好序的部分。
        //获取需要排序的长度
        //最坏的情况的情况下，需要进行 n*(n-1)/2 次比较
        $length=count($arr);
        //假定第一个为有序的，所以从$i开始比较
        for ($i=1; $i <$length ; $i++) {
            //存放待比较的值
            $tmp=$arr[$i];
            for($j=$i-1;$j>=0;$j--){
                //若插入值比较小，则将后面的元素后移一位，并将值插入
                if($tmp<$arr[$j]){
                    $arr[$j+1]=$arr[$j];
                    $arr[$j]=$tmp;
                }else{
                    break;
                }
            }
        }
        return $arr;
    }
    
    
        /** 快速排序
     * @param $arr
     * @return array
     */
    function quickSort($arr)
    {
        //选取一个数值作为基数,把数据划分为左右两组数据,最后合并左中右的数据
        $count = count($arr);
        if($count <= 1) {
            return $arr;
        }
        $init  = $arr[0];
        $left  = [];
        $right = [];
        for ($i=1; $i<$count; $i++) {
            if($arr[$i] > $init) {
                $right[] = $arr[$i];
            }else{
                $left[]  = $arr[$i] ;
            }
        }
        $left  = $this->quickSort($left);
        $right = $this->quickSort($right);
        return array_merge($left,[$init],$right);
    }
