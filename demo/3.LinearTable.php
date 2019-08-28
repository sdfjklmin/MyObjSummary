<?php
function dd()
{
    if(PHP_SAPI === 'cli'){
        $symbol = "\n";
    }else{
        $symbol = "<br/>" ;
        echo "<pre/>";
    }
    if(func_get_args()) {
        foreach (func_get_args() as $key => $value) {
            echo "type: ".gettype($value).$symbol;
            echo "data: ";
            print_r($value) ;
            echo $symbol;
        }
    }
    exit();
}
/**
 * 线性表(List) ADT (抽象数据类型)
 * @define １.线性表定义：零个或多个数据元素的有限序列。
 *         2.线性表抽象的数据类型 ：
 *          Data:
 *              第一个元素无前驱，最后一个元素无后续，其他每个元素有且只有一个前驱和后续，
 *              每个数据类型都为 DataType，元素直接的关系是一一对应。
 *          Operation:
 *              InitList(*L):初始化操作，建立一个空的线性表L
 *              ListEmpty(L):若线性表为空，返回 true，不为空返回 false 。
 *              ClearList(*L):清空线性表
 *              GetElem(L,i,*e):将线性表 L 中的第 i 个元素返回给 e　。
 *              LocateElem(L,e):在线性表Ｌ中查找与定值ｅ想等的元素，如果成功，返回回元素在Ｌ中的序号；否则，放回失败false 。
 *              ListInsert(*L,i,e):在线性表Ｌ中的第ｉ个位置插入新元素ｅ。
 *              ListDelete(*L,i,e):删除线性表Ｌ中第ｉ个元素，并用ｅ作为返回值。
 *              ListLength(L):返回线性表的Ｌ的元素个数。
 * @example
 *          1 2 3 4 5 6 7 8 9
 *          1 2 ... i-1 i i+1 ... n
 *          当n为０时成为空集，ｎ为线性表的长度
 * Class BaseLinearTable
 */
abstract class BaseLinearTable
{
    /** 线性表集合，php中没有明确的集合，这里使用数组
     * @var array
     */
    public $linear_table;

    /** @var int 最大数组长度 */
    public $max_size;

    /** 设置最大数组长度 */
    public function setMaxsize()
    {
        //目前没有分配内存的方法，这里可以先设置array的长度
    }

    /** 公有方法为对应线性表的基本operation，实际的线性表更为复杂，可以使用基本操作的组合来实现 */

    /************************************************************/
    /*********************** operation start*********************/
    /************************************************************/
    /**　InitList(*L):初始化操作，建立一个空的线性表L
     * @param $list
     * @return array
     */
    public function InitList($list = [])
    {
        sort($list);
        $this->linear_table = $list;
        return $this->linear_table;
    }

    /** ListEmpty(L):若线性表为空，返回 true，不为空返回 false 。
     * @return bool
     */
    public function ListEmpty()
    {
        return empty($this->linear_table);
    }

    /**
     * ClearList(*L):清空线性表
     */
    public function ClearList()
    {
        $this->linear_table = [];
    }

    /** GetElem(L,i,*e):将线性表 L 中的第 i 个元素返回给 e　。
     * @param $index
     * @return mixed
     */
    abstract function GetElem($index);

    /** 定位元素
     * 　LocateElem(L,e):在线性表Ｌ中查找与定值ｅ想等的元素，如果成功，返回回元素在Ｌ中的序号；否则，放回失败false 。
     * @param $value
     * @return bool|int
     */
    public function LocateElem($value)
    {
        return array_search($value,$this->linear_table);
    }

    /** ListInsert(*L,i,e):在线性表Ｌ中的第ｉ个位置插入新元素ｅ。
     *  复杂度：O(1)
     * @param $index
     * @param $value
     * @return bool|array
     */
    abstract function ListInsert($index, $value);

    /** ListDelete(*L,i,e):删除线性表Ｌ中第ｉ个元素，并用ｅ作为返回值。
     *  复杂度：O(1)
     * @param $index
     * @return mixed
     */
    abstract function ListDelete($index);

    /** ListLength(L):返回线性表的Ｌ的元素个数。
     * @return int
     */
    public function ListLength()
    {
        return count($this->linear_table);
    }

    /************************************************************/
    /************************operation end**********************/
    /************************************************************/

}

/** 3.线性表的顺序存储结构：用一段地址连续的存储单元依次存储线性表的数据元素。
 * @desc
 *       线性表的长度不能超过存储容量，当你申请了１０个存储单元，你的线性表长度ｎ不能超过１０。
 *       由于线性表中可以进行插入和删除操作，因此分配的数组空间要大于等于当前线性表的长度。
 *       存储器中的每个存储单元都有自己的编号，这个编号称为地址。这里指的是index
 *       顺序存储结构的属性 : 存储空间的起始位置，线性表的最大存储容量，线性表的当前长度
 *       LOC存储位置的函数 ，　ａ为线性表，ｉ为地址，ｃ为存储单元个数
 *       总共５个存储单位，ａ[1]在５这个单元位置，求第３个地址的单元位置 ；７，５－９
 *       LOC(a[i]) = LOC(a[1]) + (i-1)*C　复杂度为：O(1)
 *       随机存取结构：每个线性表位置的存入和取出时间相等，也就是一个常数。复杂度为：O(1)
 * Class LinearTableOrder
 */
class LinearTableOrder extends BaseLinearTable
{
    /** 4.顺序存储结构的插入和删除 */
    /** 获取元素　GetElem() */
    /** 插入　ListInsert */
    /** 删除　ListDelete */
    /** 线性表的顺序存储结构的优缺点*/
    /** 优点:无须为表中元素之间的逻辑关系而增加额外的存储空间 、可以快速地存取表中任一位置的元素*/
    /** 缺点:插入和删除操作需要移动大量的元素、当线性表长度变大时，难以确定存储空间的容量、容易造成存储空间的‘碎片’*/

    /** 获取元素
     * @param $index
     * @return bool|mixed
     */
    public function GetElem($index)
    {
        if($this->ListLength() === 0 || $index < 0 || $index >$this->ListLength())
            return false;
        return $this->linear_table[$index];
    }

    /** 插入
     * @param $index
     * @param $value
     * @return array|bool
     */
    public function ListInsert($index, $value)
    {
        //位置不合理，返回错误
        //线性表长度不能大于数组长度（即申请内存空间的长度）　|| $index > $this->ListLength()
        if($index < 0 || $this->ListLength() == 0)
            return false;
        //从最后一个元素向前遍历到位置第$index个位置，分别将它们向后移动一个位置
        for ($i = $this->ListLength()-1;$i>=$index;$i--) {
            $this->linear_table[$i+1] = $this->linear_table[$i];
        }
        //将元素插入i位置
        //长度加１
        $this->linear_table[$index] = $value;
        //return $this->linear_table;
    }

    /** 删除
     * @param $index
     * @return bool|mixed
     */
    public function ListDelete($index)
    {
        //操作步骤同Insert，这里简写
        if(!isset($this->linear_table[$index]))
            return false;
        $value = $this->linear_table[$index];
        unset($this->linear_table[$index]);
        return $value;
    }
}
/** 更复杂的线性表应用
 * Class LinearTableOrderComplex
 */
class LinearTableOrderComplex extends LinearTableOrder
{
    /**
     * @param LinearTableOrder $ListOther
     */
    public function unionList(LinearTableOrder $ListOther)
    {
        //合并的时候应该考虑当前线性表的数组长度，也可以是动态扩容。
        $lengthA = $this->ListLength();
        $lengthB = $ListOther->ListLength();
        /**  循环$ListOther，获取每个值，判断值是否存在于当前List，如果不存在则插入 */
        for ($i = 0; $i < $lengthB; $i ++) {
            $tempData = $ListOther->GetElem($i);
            if($this->LocateElem($tempData) === false) {
                $this->ListInsert(++$lengthA,$tempData);
            }
        }
    }
}
//测试:合并Ａ，Ｂ两个集合
//集合Ａ
$ListA = new LinearTableOrderComplex();
$ListA->InitList([1,2,3,4]);
//集合Ｂ
$ListB = new LinearTableOrderComplex();
$ListB->InitList([4,6,8,7,10,11]);
//合并
$ListA->unionList($ListB);
//var_dump($ListA);

/**
 * 顺序存储结构的插入和删除需要移动大量元素，耗费大量的时间。　
 * 5.线性表的链式存储结构：用任意一组存储单元存储数据元素，这组可以是连续的，也可以是不连续的。
 * 　数据域：存储数据元素。
 * 　指针域：存储后继元素的位置，也称指针或者链。　　
 *   结点：数据元素（包含数据域和指针域）的存储映像。
 * 　ｎ个结点链结成一个链表，即为线性表的链式存储结构，因为此链表中的只包含一个指针域，所以叫单链表。
 *   头指针：链表中第一个结点存储的位置叫做头指针。必有。
 *   线性链表的最后一个结点指针为‘空’(通常用NULL或者^表示)
 *   为了方便操作链表，会在单链表的第一个结点前附设一个结点，称为头结点。可有。
 *   头结点的数据域可以不存储任何信息，也可以存储其他附近信息。头结点的指针域存储指向第一个结点的指针。
 * @example
 *    头结点             结点
 *                 (数据域|指针域)
 *  null|0xxx0     (n-1|0xxx1)  (n|0xxx5)    (n+1|0xxx7)
 *   头指针         第一个节点
 *                 地址0xxx0
 *
 *
 * Class LinearTableChain
 */
class LinearTableChain extends BaseLinearTable
{
    /** @var array 线性表 */
    public $linear_table;

    /** @var $head_needle NodeDef 头指针 */
    public $head_needle;

    public function __construct()
    {
        $this->linear_table = [
            //0xxx1 : 地址　　　　　　　　　数据域　　　 指针域
            '0xxx1' => new NodeDef(null,'0xxx5'),
            '0xxx6' => new NodeDef('2','0xxx2'),
            '0xxx2' => new NodeDef('3','0xxx4'),
            '0xxx3' => new NodeDef('5', null),
            '0xxx4' => new NodeDef('4','0xxx3'),
            '0xxx5' => new NodeDef('1','0xxx6'),
        ] ;

        $this->head_needle = $this->linear_table['0xxx1'];
    }

    /** 生成一个存储地址
     * @return string
     */
    private function createAddress()
    {
        return '0xxx'.(count($this->linear_table)+1);
    }

    /** 获取头结点地址
     * @return string
     */
    protected function getHeadAddress()
    {
        return '00000';
    }

    /***********Operation Start****************/

    /** 获取线性表的某个元素
     * @param $i
     * @param bool $is_node
     * @return null
     */
    public function GetElem($i, $is_node = false)
    {
        //获取链表第i个数据的步骤
        //1.申明一个节点p指向链表的第一个节点，初始化j从1开始；
        //2.当j<i时，遍历链表；让p指针向后移动，不断指向下一个节点，j累加到i；
        //3.若到链表的末尾p为空，则i不存在；
        //4.若查找成功，返回节点p的数据或者节点。
        //5.可以自行编码下上面的逻辑。
        /** @var $p NodeDef */
        $p = $this->head_needle;
        $j = 0; //php数组是以0开始。
        $e = null;
        //O(1)
        if($p->next && $j == $i) {
            return $is_node ? $p : $p->data;
        }
        //O(n)，这里不能使用for来进行循环。核心思想为‘工作指针后移’
        while ($p->next && $j < $i) {
            $p = $this->linear_table[$p->next];
            $j++;
            if(!$p->next) {
                break;
            }
            if($j == $i) {
                $e = $p;
                break;
            }
        }
        return $is_node ? $e : $e->data;
    }

    /** 数据插入
     * @param $i int 插入到第几个元素
     * @param $v string 数据
     * @return array|bool
     */
    public function ListInsert($i,$v)
    {
        // p   p->next
        //   s
        //s->next = p->next ;
        //p->next = s;
        //尚未判断头尾节点，可做自行练习和补充。
        /** @var  $beforeNode NodeDef */
        $beforeNode    = $this->GetElem($i-1,true);
        /** @var  $nowNode NodeDef */
        $nowNode       = $this->GetElem($i,true);
        if(!$beforeNode || !$beforeNode->next || !$nowNode || !$nowNode->next) {
            return false;
        }
        $address    = $this->createAddress();
        $oldAddress = $nowNode->next;
        $this->linear_table[$address] = new NodeDef($v,$oldAddress);
        $nowNode->next = $address;
        $this->linear_table[$beforeNode->next] = $nowNode;
        return $this->linear_table;
    }

    /** 删除节点
     * @param $index
     * @return mixed
     */
    public function ListDelete($index)
    {
        //删除q节点: p  q(p->next)  q->next(p->next->next)
        //q = p->next;
        //p->next = q->next;
        //操作步骤同Insert，可自行练习。C语言free(q)，系统回收一个节点q，释放内存。
    }

    /** 相比于顺序存储结构来说，插入和删除数据越频繁的操作，单链表的效率优势就越明显。 */
    /** 查找元素都为O(n)，单顺序存储需要依次移动即O(n)，单链表只需置换即O(1) */

    /** 顺序存储结构的创建：其实就是一个数组的初始化，即声明一个类型和大小的数组并赋值的过程。 */
    /** 单链表：它可以很散，是一种动态结构。空间和位置不需要提前规划，可根据需求即时生成。 */
    public function createLinearChain($n)
    {
        //1.声明一结点p和计数器变量i
        //2.初始化一空链表L
        //3.让L的头结点的指针指向null，即建立一个带头结点的单链表
        //4.循环： 生成一新结点赋值给p；随机生成一数字赋值给p的数据域p->data；将p插入到头结点与前一新结点之间。
        //5.头插法：始终让新结点在第一的位置；尾插法：每次新结点都插入到尾端结点的后面；
        //6.自行练习编码，后续有答案。
    }

    /** 在内存中释放链表 */
    public function ClearList()
    {
        //1.声明一结点p和q
        //2.将第一个结点赋值给q
        //3.循环：将下一个结点赋值给q；释放p；将q赋值给p;
        //C代码
        /*Status ClearList(LinkList *L)
        {
            LinkList p,q;
            p = (*L)->next;
            while (p) {
                q = p->next;
                free(p);
                p = q;
            }
            (*L)->next = NULL;
            return ok;
        }*/
    }

    /** 总结 ： 多查询少插入和删除使用顺序，多插入和删除少查询使用链式。具体根据业务使用。 */
    /** 对比 ：
     *   存储方式 ：
     *      顺序 ： 用一段连续的存储单元依次存储线性表的数据元素
     *      链式 ： 用一组任意的存储单元存放线性表的元素
     *   时间性能 ：
     *      查找 ： 顺序 O(1)；链式 O(n)
     *      插入和删除 ： 顺序 O(n)， 链式 O(1)
     *   空间性能 ：
     *      顺序 ： 需要预分配存储空间，分大了，浪费，分小了容易溢出。
     *      链式 ： 不需要分配存储空间，只要有就可以分配，元素个数不受限制。
     */
    /** 顺序存储结构 ： 获取较快，插入和删除复杂度为O(n)。 */
    /** 链式存储结构 ： 获取较慢，插入和删除复杂度为O(1)。 */

}
/** 节点定义(一个节点包括数据域和指针域[下一个元素的地址])
 * Class NodeDef
 */
class NodeDef
{
    /** @var null 节点数据域 */
    public $data;

    /** @var null 节点指针域，指向i+1个元素地址 */
    public $next;

    /** 构建节点数据，data应该是同一datatype。
     * NodeDef constructor.
     * @param null $data
     * @param null $next
     */
    public function __construct($data = null, $next = null)
    {
        $this->data = $data;
        $this->next = $next;
    }
}

$linearChain = new LinearTableChain();
dd($linearChain->createLinearChain());