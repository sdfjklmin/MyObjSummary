<?php
namespace php\diIoc;
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 传统的思路是应用程序用到一个Foo类 就会创建Foo类并调用Foo类的方法
// 假如这个方法内需要一个Bar类 就会创建Bar类并调用Bar类的方法
// 而这个方法内需要一个Bim类 就会创建Bim类 接着做些其它工作。
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/**
 *  Foo
 */
class Foo
{

    public function doSomething($value='')
    {
        # code...
        $Bar = new Bar();
        $Bar->doSomething();
        echo 'Foo doSomething','<br />' ;
    }
}

/**
 *  Bar
 */
class Bar
{


    public function doSomething($value='')
    {
        # code...
        $Bim = new Bim();
        $Bim->doSomething();
        echo 'Bar doSomething','<br />' ;
    }
}

/**
 * Bim
 */
class Bim
{


    public function doSomething($value='')
    {
        # code...
        echo 'Bim doSomething','<br />'  ;
    }
}

$doSomething = new Foo();
$doSomething->doSomething();

echo "-------------------------------------------------","<br />";
