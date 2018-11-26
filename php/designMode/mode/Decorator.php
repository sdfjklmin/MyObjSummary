<?php
# 动态的给一个对象添加一些额外的职责,就扩展功能而言比生成子类方式更为灵活
# 全局说明 : 留言板信息处理
# 设想 :  留言板基类 留言板信息处理 留言板处理(装饰)  过滤html 过滤敏感词
# 示例 :  过滤HTML 通过 装饰扩展 过滤敏感词&留言板信息处理 输出信息
/** 装饰模式
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/11/23
 * Time: 10:06
 */
namespace MyObjSummary\php\designMode ;
echo <<<DES
    <h2>装饰模式 : 动态的给一个对象添加一些额外的职责,就扩展功能而言比生成子类方式更为灵活</h2>
DES;
/** 留言板基类
 * Class MessageBoardHandler
 * @package MyObjSummary\php\designMode
 */
abstract class MessageBoardHandler
{
    public function __construct(){}
    abstract public function filter($msg);
}

/** 留言板处理
 * Class MessageBoard
 * @package MyObjSummary\php\designMode
 */
class MessageBoard extends MessageBoardHandler
{
    public function filter($msg)
    {
        return "处理留言板上的内容|".$msg;
    }
}
$obj = new MessageBoard();
echo $obj->filter("装饰模式!<br/>");

/** 留言板处理 装饰模式
 * Class MessageBoardDecorator
 * @package MyObjSummary\php\designMode
 */
class MessageBoardDecorator extends MessageBoardHandler
{
    /**
     * @var $_handler MessageBoardDecorator
     */
    private $_handler = null;

    public function __construct($handler)
    {
        parent::__construct();
        $this->_handler = $handler;
    }

    public function filter($msg)
    {
        return $this->_handler->filter($msg);
    }
}

/** 过滤html
 * Class HtmlFilter
 * @package MyObjSummary\php\designMode
 */
class HtmlFilter extends MessageBoardDecorator
{
    public function __construct($handler)
    {
        parent::__construct($handler);
    }

    public function filter($msg)
    {
        return "过滤掉HTML标签|".parent::filter($msg); // 过滤掉HTML标签的处理 这时只是加个文字 没有进行处理
    }
}


/** 过滤敏感词
 * Class SensitiveFilter
 * @package MyObjSummary\php\designMode
 */
class SensitiveFilter extends MessageBoardDecorator
{
    public function __construct($handler)
    {
        parent::__construct($handler);
    }

    public function filter($msg)
    {
        return "过滤掉敏感词|".parent::filter($msg); // 过滤掉敏感词的处理 这时只是加个文字 没有进行处理
    }
}
//此时 HtmlFilter 已经处理了其它操作 (过滤敏感词,处理留言板信息)
//动态扩展 HtmlFilter 的功能
$obj = new HtmlFilter(new SensitiveFilter(new MessageBoard()));
echo $obj->filter("我是最后的装饰!<br/>");

//执行顺序 过滤HTML <- 过滤敏感词 <- 留言板信息处理
// new HtmlFilter(new SensitiveFilter()) -> new SensitiveFilter(new MessageBoard()) -> new MessageBoard() ;
// 依次调用对象的 filter 最后输出 msg

//对比
class BoardCommonHand
{
    //过滤入口
    public function filter($msg)
    {
        return '我是基础过滤的!'.$msg ;
    }

    //留言板数据处理
    public function boardFilter($msg)
    {
        return '我是处理留言板的!'.$msg;
    }

    //HTML标签过滤
    public function htmlFilter($msg)
    {
        return '我是处理HTML标签的!'.$msg;
    }

    //敏感词过滤
    public function sensitiveFilter($msg)
    {
        return '我是敏感词过滤的!'.$msg;
    }
}
$compared = new BoardCommonHand();
echo $compared->filter('我是消息!');
echo $compared->boardFilter('我是消息!');
//相比于上面的装饰模式 单独的类处理 需要外部手动调用 或者内部调用,相对而言没有那么灵活
