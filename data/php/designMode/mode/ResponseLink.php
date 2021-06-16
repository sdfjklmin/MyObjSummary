<?php
/**
 * 职责链模式
 *
 * 为解除请求的发送者和接收者之间的耦合,而使用多个对象都用机会处理这个请求,将这些对象连成一条链,并沿着这条链传递该请求,直到有一个对象处理它
 *
 */
namespace MyObjSummary\php\designMode ;

echo <<<DES
    <h2>职责链模式 : 为解除请求的发送者和接收者之间的耦合,而使用多个对象都有机会处理这个请求,
    将这些对象连成一条链,并沿着这条链传递该请求,直到有一个对象处理它</h2>
DES;
abstract class Handler
{
    protected $_handler = null;
    public function setSuccessor($handler)
    {
        $this->_handler = $handler;
    }
    abstract function handleRequest($request);
}
class ConcreteHandlerZero extends Handler
{
    public function handleRequest($request)
    {
        if($request == 0)
        {
            echo "0<br/>";
        } else {
            $this->_handler->handleRequest($request);
        }
    }
}
class ConcreteHandlerOdd extends Handler
{
    public function handleRequest($request)
    {
        if($request % 2)
        {
            echo $request." is odd<br/>";
        } else {
            $this->_handler->handleRequest($request);
        }
    }
}
class ConcreteHandlerEven extends Handler
{
    public function handleRequest($request)
    {
        if(!($request % 2))
        {
            echo $request." is even<br/>";
        } else {
            $this->_handler->handleRequest($request);
        }
    }
}
// 实例一下
$objZeroHander = new ConcreteHandlerZero();
$objEvenHander = new ConcreteHandlerEven();
//$objOddHander = new ConcreteHandlerOdd();
$objZeroHander->setSuccessor($objEvenHander);
//$objEvenHander->setSuccessor($objOddHander);

foreach(array(2,3,4,5,0) as $row)
{
    $objZeroHander->handleRequest($row);
}