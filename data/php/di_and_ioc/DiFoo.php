<?php

namespace php\diIoc;
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 使用依赖注入的思路是应用程序用到Foo类，Foo类需要Bar类，
// Bar类需要Bim类，那么先创建Bim类，再创建Bar类并把Bim注入，再创建Foo类，并把Bar类注入，
// 再调用Foo方法，Foo调用Bar方法，接着做些其它工作。
// 先创建依赖体
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/**
 * Bim2
 */
class Bim2
{

    public function doSomething($value='')
    {
        # code...
        echo 'Bim2 doSomething','<br />' ;
    }

}

/**
 *  Bar2
 */
class Bar2
{

    private $bim2 ;

    public function __construct(Bim2 $bim2)
    {
        # code...
        $this->bim2 = $bim2 ;
    }

    public function doSomething($value='')
    {
        # code...
        $this->bim2->doSomething();
        echo "Bar2 doSomething",'<br />';
    }
}


/**
 * Foo
 */
class Foo2
{

    private $bar2 ;
    public function __construct(Bar2 $bar2)
    {
        # code...
        $this->bar2 = $bar2 ;
    }

    public function doSomething($value='')
    {
        # code...
        $this->bar2->doSomething();
        echo "Foo2 doSomething",'<br />';
    }
}

$doSomething2 = new Foo2(new Bar2(new Bim2()));
$doSomething2->doSomething();

echo "-------------------------------------------------","<br />";