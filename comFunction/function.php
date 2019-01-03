<?php
//公共方法composer引入不需要命名空间,否则找不到
/** 方法说明
 * @function dd()           打印
 * @function dda()          数据打印,输出PHP格式数据,可复用(主要用于Array)
 * @function gerRand()      抽奖
 * @function getNum2()      获取两位小数
 * @function repeatRank()   相同数据排名
 * @function sortRank()     快速排序
 * @function arrFirst()     获取数组第一个元素
 * @function pwdHash()      获取数据hash
 * @function ckPwd()        验证数据hash
 * @function getDirTree()   获取目录树结构 配合static的样式
 * @function getIp()        获取ip地址
 * @function inputClean()   输入清除(避免注入)
 * @function curlPost()     原生curl post请求
 * @function curlGet()      原生curl get请求
 * @function buildXml()     构建Xml数据
 * @function decodeXml()    解析Xml数据
 */

if(!function_exists('dd')) {
    /**
     * 打印信息
     */
	function dd()
	{
        if(PHP_SAPI == 'cli'){
            $symbol = "\n";
        }else{
            $symbol = "<br />" ;
            echo "<pre />";
        }
        $exitSwitch  = true;
		if(func_get_args()) {
			foreach (func_get_args() as $key => $value) {
                /*if( is_string($value) &&
                    ($value === 'exit-0') &&
                    ($key+1) ===func_num_args() ) {
                    $exitSwitch = false ;
                    continue ;
                }*/
				# code...
				echo "type: ".gettype($value).$symbol;
				echo "data: ";
					print_r($value) ;
                echo $symbol;
			}
		}
		if($exitSwitch) {
            exit();
        }
	}
}

if(!function_exists('dda')) {
    /**
     * 打印数据,以PHP格式返回(主要针对数组)
     */
    function dda()
    {
        if(PHP_SAPI == 'cli'){
            $symbol = "\n";
        }else{
            $symbol = "<br />" ;
            echo "<pre />";
        }
        if(func_get_args()) {
            foreach (func_get_args() as $key => $value) {
                # code...
                echo "type: ".gettype($value).$symbol;
                echo "data: ";
                    var_export($value) ;
                echo $symbol;
            }
        }
        exit();
    }
}

if(!function_exists('gerRand')) {
    /** 随机获取物品
     * @param array $proArr 物品 => 权重 eg :   [1=>99,2=>10,3=>0]
     * @return int|string  eg: 1
     */
     function getRand($proArr = []) {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}

if(!function_exists('getNum2')) {
    /** 获取两位小数
     * @param $money float 金额
     * @return string
     */
    function getNum2($money)
    {
        //保留三位小数 截取最后一位 保留两位小数
        return sprintf("%.2f",substr(sprintf("%.3f", $money), 0, -1));
    }
}

if(!function_exists('repeatRank')) {
    /** 相同数据排名
     * @param $getData array 数据源
     * @param $datKey string 数据依据key
     * @param $unKey string  唯一性的key
     * @param $raKey string  设置排名的key
     * @return mixed
     */
    function repeatRank($getData,$datKey,$unKey,$raKey='rank')
    {

        $volume  = array_column($getData,$datKey);
        $edition = array_column($getData,$unKey);
        // 将数据根据 volume 降序排列，根据 edition 升序排列
        // 把 $getData 作为最后一个参数，以通用键排序
        array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $getData);
        $randBrokerage = array_column($getData,$datKey,'index') ;
        $randBrokerage = array_unique($randBrokerage) ;
        $unData = array_column($getData,$datKey) ;
        if(empty($unData) || empty($randBrokerage)) {
            //无效数据
            return [] ;
        }
        foreach ($unData as $k => $v) {
            if(isset($randBrokerage[$k])) {
                $getData[$k][$raKey] = $k+1;
            }else{
                $coKey = array_search($v,$randBrokerage) ;
                $getData[$k][$raKey] = $coKey+1;
            }
        }
        //相同数据并列排名
        return $getData ;
    }
    /*$tRankDat = [
          ['user_id' =>3,'money'=>12,],['user_id' =>4,'money'=>12,],
          ['user_id' =>5,'money'=>8,],['user_id' =>12,'money'=>21,],
          ['user_id' =>6,'money'=>8,],['user_id' =>17,'money'=>5,],
          ['user_id' =>11,'money'=>21,],['user_id' =>2,'money'=>30,],
      ] ;
    $tRank = repeatRank($tRankDat,'money','user_id');
    var_dump($tRank) ;*/
}

if(!function_exists('sortRank')) {
    /** 快速排序
     * @param $getData
     * @return array
     */
    function sortRank($getData)
    {
        //先判断是否需要继续进行
        $length = count($getData);
        if($length <= 1) {
            return $getData;
        }
        //选择一个元素
        $base_num = $getData[0];
        //初始化两个数组
        $left_array = array();//小于的
        $right_array = array();//大于的
        for($i=1; $i<$length; $i++) {
            if($base_num > $getData[$i]) {
                //放入左边数组
                $left_array[] = $getData[$i];
            } else {
                //放入右边
                $right_array[] = $getData[$i];
            }
        }
        //再分别对 左边 和 右边的数组进行相同的排序处理方式
        //递归调用这个函数,并记录结果
        $left_array = sortRank($left_array);
        $right_array = sortRank($right_array);
        //合并左边 标尺 右边
        return array_merge($left_array, array($base_num), $right_array);
    }
    /*$tSortData = [0,1,3,5,7,8,9,2,4,6,10,12,19,25,13,16,17] ;
    sort($tSortData) ;
    var_dump($tSortData) ;
    $tSort = sortRank($tSortData) ;
    var_dump($tSort) */;
}

if(!function_exists('arrFirst')) {
    /** 获取数组第一个元素
     * @param $arr
     * @return mixed
     */
    function arrFirst($arr)
    {
        return current($arr) ;
    }
}

if(!function_exists('pwdHash')) {
    function pwdHash($password, $cost = 13)
    {
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
        }
        exit('there is not system password hash');
        $salt = getSalt($cost) ;
        //crypt为单向算法 不可逆
        $hash = crypt($password, $salt);
        return $hash;
    }
}

    function getSalt($cost)
    {
        return (string)$cost;
    }

if(!function_exists('ckPwd')) {
    function ckPwd($password,$pwdHash,$cost = 13)
    {
        if(function_exists('password_verify')) {
           return password_verify($password,$pwdHash) ;
        }
        exit('there is not system password verify');
        $salt = getSalt($cost) ;
        //crypt为单向算法 不可逆
        $hash = crypt($password, $salt);
        return $hash === $pwdHash ;
    }
}

if(!function_exists('getDirTree')) {
    function getDirTree( $directory ,$label = [] ,$parentDir ='')
    {
        $dirs  = scandir($directory) ;
        foreach ($dirs as $dir) {
            if( $dir[0] === '.' ||  in_array($dir,NOT_LINK) )  continue ;
            if(is_dir($directory.$dir)) {
                //$label[$dir]
                $label[] = [
                    'name' => $dir ,
                    'code' => $dir ,
                    'icon' => $parentDir ? 'icon-minus-sign' : 'icon-th' ,
                    'parentCode' => $parentDir ,
                    'href' =>'',
                    'child' => getDirTree($directory.$dir.'/',[],$dir)  ,
                ] ;
            }else{
                // $parentDir ? 'icon-minus-sign' : 'icon-th' ,
                // 目前不开放根目录文件 可通过icon样式显示
                $label[] = [
                    'name'=>$dir ,
                    'icon'=>'icon-minus-sign' ,
                    'code'=>$dir ,
                    'parentCode'=>$parentDir ,
                    'href' => $directory.$dir ,
                    'child'=>[] ,
                ]  ;
            }
        }
        return $label ;
    }
}

if(!function_exists('getIp')) {
    function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if(!function_exists('inputClean')) {
    function inputClean($input)
    {
        if(is_array($input)){
            foreach ($input as $key => $val)
            {
                $output[$key] = inputClean($val);
            }
        } else {
            $output = (string) $input;
            if (get_magic_quotes_gpc()){
                $output = stripslashes($output);
            }
            $output = htmlentities($output, ENT_QUOTES, 'UTF-8');
            return $output;
        }
    }
}

if(!function_exists('curlPost')) {
    /** 发送post请求
     * @param $url  string 访问地址
     * @param array $data  请求数据
     * @param bool $realData   是否解析数据
     * @return mixed|string
     */
    function curlPost($url, $data =[],$realData = false)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 40); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            var_dump(curl_error($curl)) ;exit() ;
            #return 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        if($realData) {
            $responseData = json_decode($tmpInfo,true) ;
            if($responseData['code'] == '200') {
                return $responseData['data'] ?? true;
            }
        }
        return $tmpInfo; // 返回数据
    }
}

if(!function_exists('curlGet')) {

    function curlGet($url, $data=[],$realData=false)
    {
        $str = '?' ;
        if($data) {
            foreach ($data as $k =>$v) {
                $str .=$k.'='.$v.'&' ;
            }
            $str = trim($str,'&') ;
            $url = $url.$str ;
        }
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_TIMEOUT, 40); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            return '' ;
            # return 'Errno'.curl_error($curl);//捕抓异常
        }
        if($realData) {
            $responseData = json_decode($tmpInfo,true) ;
            return $responseData ;
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}

if(!function_exists('buildXml')){
    /** 构建xml
     * @param $data
     * @param string $t
     * @param bool $bast
     * @return string
     */
    function buildXml(array $data,string $t='Request',bool $bast=true)
    {
        $str = '' ;
        if($bast)
            $str  = '<?xml version="1.0" encoding="utf-8"?>';
        $str .= '<'.$t.'>';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $child = buildXml($v,$k,false);
                $str .= $child;
            } else {
                $str .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
        }
        $str .='</'.$t.'>';
        return $str;
    }
}

if(!function_exists('decodeXml')){
    /** 获取xml数据
     * @param $x string|array xml对应的标签
     * @param $str string 数据
     * @param bool $only 是否唯一
     * @return array
     */
    function decodeXml($x,$str,$only =true)
    {
        /*$a = simplexml_load_string($str);
        $a = json_encode($a);
        var_dump(json_decode($a, true));*/
        $array = [] ;
        if(is_array($x)) {
            foreach ($x as $v) {
                preg_match_all("/<".$v.">.*<\/".$v.">/", $str, $temp);
                $strData = $only ?current($temp[0]):  $temp[0];
                if($strData) {
                    $strDataOne = str_replace('<'.$v.'>','',$strData);
                    $strDataEnd = str_replace('</'.$v.'>','',$strDataOne);
                    $array[$v] = $strDataEnd ;
                }else{
                    $array[$v] = '' ;
                }
            }
        }else{
            preg_match_all("/<".$x.">.*<\/".$x.">/", $str, $temp);
            $strData = $only ?current($temp[0]):  $temp[0];
            if($strData) {
                $strDataOne = str_replace('<'.$x.'>','',$strData);
                $strDataEnd = str_replace('</'.$x.'>','',$strDataOne);
                $array[$x] = $strDataEnd ;
            }else{
                $array[$x] = '' ;
            }
        }
        return $array ;
    }
}