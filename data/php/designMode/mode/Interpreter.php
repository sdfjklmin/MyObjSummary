<?php
/**
 * 解释器
 * 定义一些基础的方法 用于参数匹配
 */
echo <<<DES
    <h2>解释器 : 定义一些基础的方法 用于参数匹配</h2>
DES;
/** 解释器错误类
 * Class Expression
 */
class Expression
{
    public function interpreter($str)
    {
        var_dump($str) ;
        return $str;
    }
}

/** 解释器基础类 数字处理
 * Class ExpressionNum
 */
class ExpressionNum extends Expression
{
    public function interpreter($str)
    {
        switch($str)
        {
            case "0": return "零";
            case "1": return "一";
            case "2": return "二";
            case "3": return "三";
            case "4": return "四";
            case "5": return "五";
            case "6": return "六";
            case "7": return "七";
            case "8": return "八";
            case "9": return "九";
        }
    }
}

/** 解释器基础类 字符串处理
 * Class ExpressionCharater
 */
class ExpressionCharater extends Expression
{
    public function interpreter($str)
    {
        return strtoupper($str);
    }
}

/** 测试
 *  一般提供入口方法的 很少再当中写公共方法
 * Class Interpreter
 */
class Interpreter
{
    public function execute($string)
    {
        $expression = null;
        for($i = 0;$i<strlen($string);$i++) {
            $temp = $string[$i];
            switch(true)
            {
                case is_numeric($temp): $expression = new ExpressionNum(); break;
                default: $expression = new ExpressionCharater();
            }
            echo $expression->interpreter($temp);
        }
    }
}

// 将输入的数组或者字母 转换成 大写

// 将 数字 和 字母 拆分成两个实现类 一个处理数字 一个处理字母
// 根据输入的信息 对应处理
$obj = new Interpreter();
$obj->execute("12345abc");
echo "<br />";

//对比测试
class ComInterpreter
{
    public function execute($string)
    {
        $returnStr = '' ;
        for ($i = 0 ;$i < strlen($string) ; $i++) {
            $temp = $string[$i] ;
            switch ($temp) {
                case is_numeric($temp) :
                    $returnStr .= $this->numToChar($temp) ;
                    break ;
                default :
                    $returnStr .= $this->strToUpper($temp);
            }
        }
        echo $returnStr ;
    }

    public function strToUpper($str)
    {
        return strtoupper($str) ;
    }

    public function numToChar($str)
    {
        switch($str)
        {
            case "0": return "零";
            case "1": return "一";
            case "2": return "二";
            case "3": return "三";
            case "4": return "四";
            case "5": return "五";
            case "6": return "六";
            case "7": return "七";
            case "8": return "八";
            case "9": return "九";
        }

    }
}

$test = new ComInterpreter();
$test->execute('123abc');
?>