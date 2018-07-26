<?php
/**
* id en out
*/
class IdEnOut
{
	
    const enOutT    =  12  ; #相加数
    const enOutLink = ':'; #连接符
    const enOutW    =  6  ; #位移个数
    const enOutSal  = ['X','e','V','K','T','q','m','u','w','B','c','+','n','-','l','.']  ;
    
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
        return urlencode(self::enOutLink.$backStr) ;
    }


    public static function outId($str)
    {
        $str = urldecode($str) ;
        $str = explode(self::enOutLink,$str)[1] ;
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