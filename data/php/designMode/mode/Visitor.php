<?php
/**
 * 访问者模式
 *
 * 表示一个作用于某对象结构中的各元素的操作,可以在不改变各元素的类的前提下定义作用于这些元素的新操作
 *
 */
echo <<<DES
    <h2>访问者模式 : 表示一个作用于某对象结构中的各元素的操作,可以在不改变各元素的类的前提下定义作用于这些元素的新操作</h2>
DES;

/** 访问者
 * Class Visitor
 */
abstract class Visitor
{
    abstract public function visitCroncreteElementA($element);
    abstract public function visitCroncreteElementB($element);
}

/** 具体的访问控制 1
 * Class ConcreteVisitor1
 */
class ConcreteVisitor1 extends Visitor
{
    public function visitCroncreteElementA($element)
    {
        echo get_class($element)." visit 1A<br/>";
    }
    public function visitCroncreteElementB($element)
    {
        echo get_class($element)." visit 1B<br/>";
    }
}

/** 具体的访问控制 2
 * Class ConcreteVisitor2
 */
class ConcreteVisitor2 extends Visitor
{
    public function visitCroncreteElementA($element)
    {
        echo get_class($element)." visit 2A<br/>";
    }
    public function visitCroncreteElementB($element)
    {
        echo get_class($element)." visit 2B<br/>";
    }
}
abstract class Element
{
    abstract public function accept($visitor);
}
class ConcreteElementA extends Element
{
    public function accept($visitor)
    {
        $visitor->visitCroncreteElementA($this);
    }
}
class ConcreteElementB extends Element
{
    public function accept($visitor)
    {
        $visitor->visitCroncreteElementB($this);
    }
}
class ObjectStructure
{
    private $_elements = array();
    public function attach($element)
    {
        $this->_elements[] = $element;
    }
    public function detach($element)
    {
        if($key = array_search($element,$this->_elements) !== false) unset($this->_elements[$key]);
    }
    public function accept($visitor)
    {
        foreach($this->_elements as $element)
        {
            $element->accept($visitor);
        }
    }
}

$objOS = new ObjectStructure();
$objOS->attach(new ConcreteElementA());
$objOS->attach(new ConcreteElementB());
$objCV1 = new ConcreteVisitor1();
$objCV2 = new ConcreteVisitor2();
$objOS->accept($objCV1);
$objOS->accept($objCV2);