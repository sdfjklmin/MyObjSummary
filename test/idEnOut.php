<?php
/**
* openssl 可以加密解密
* id en out
*/
class IdEnOut
{
	
    const enOutT    =  12  ; #相加数
    const enOutW    =  6  ; #位移个数
    const enOutSal  = ['X','e','V','K','T','q','m','u','w','B','A','c','n','C','l','O']  ;
    
    public static function enId($id)
    {
        $id = $id + self::enOutT;
        $str = base_convert($id,10,8) ;
        $strLen = strlen($str) ;
        $backStr = '' ;
        for ( $i=0; $i<$strLen ; $i++ ) {
            $s = substr($str,$i,1) ;
            $backStr .= self::enOutSal[$s+self::enOutW] ;
        }
        return urlencode($backStr) ;
    }


    public static function outId($str)
    {
        $str = urldecode($str) ;
        $strLen = strlen($str) ;
        $backNum = '' ;
        for ( $i=0; $i<$strLen ; $i++ ) {
            $s = substr($str,$i,1) ;
            $key = array_search($s,self::enOutSal)  ;
            $backNum .= $key - self::enOutW ;
        }
        $id = octdec($backNum); #解码进制和上面保持一致
        $id = $id - self::enOutT ;
        return (string)$id ;
    }

}

$t = '23' ;
$it = IdEnOut::enId($t) ;
$ot = IdEnOut::outId($it);
var_dump($t,$it,$ot);