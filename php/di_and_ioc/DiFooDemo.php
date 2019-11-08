<?php

namespace php\diIoc;

class DiFooDemoTest
{
    public function __construct($value='')
    {
        # code...
    }
    public function test($value='')
    {
        # code...
        echo "Test doSomething","<br />" ;
    }
}

/**
 *  Use
 */
class DiFooDemoUse
{
    private $baseC ;
    private $baseA ;
    public  function __construct($baseC = '' ,$baseA = '')
    {
        if(!$baseA) {

            echo 'NoA' ;
            exit();
        }
        if(!$baseC) {
            echo 'NoC' ;
            exit();
        }
        # code...
        if ($baseC && $baseA) {
            # code...
            $this->baseC = $baseC;
            $this->baseA = $baseA;
        }else{
            echo "参数不足" ;
            return false ;
        }

    }

    public function doSomething($value='')
    {
        # code...
        $a = $this->baseA;
        $this->baseC->$a();
        echo "Use doSomething" ;
    }
}

 //注入 DiFooDemoTest 和 对应类的方法名称
 $use = new DiFooDemoUse(new DiFooDemoTest(),'test');
 $use->doSomething();

