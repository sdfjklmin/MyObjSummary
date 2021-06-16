<?php
//----------------------------
// 异常处理					 |
//----------------------------
/**
 * Class NameException
 * @author sjm
 */
class NameException extends Exception
{

	/**
	 * NameException constructor.
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->message = 'NameException: '.$message;
	}
}

/**
 * Class AgeException
 * @author sjm
 */
class AgeException extends Exception
{
	/**
	 * @param mixed $message
	 * @return AgeException
	 */
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

}

//php5中无法捕捉的异常,可以注册关闭函数来进行错误监控
//注册一个会在php终止时执行的函数
register_shutdown_function(function () {
	//获取最后发生的错误,此时正常代码已经执行
	$error = error_get_last();
	if (!empty($error)) {
		print_r($error);
	}
});

//我们还可以通过 set_error_handler() 把一些Deprecated、Notice、Waning等错误包装成异常，让 try {} catch 能够捕获到。
/*error_reporting(E_ALL);
//配置错误显示,生成关闭,一个未定义的参数会有两次报错,一个是底层自带一个是配置为on
ini_set('display_errors', 'on');
//捕获Deprecated、Notice、Waning级别错误
set_error_handler(function ($errno, $errstr, $errfile) {
	throw new \Exception($errno . ' : ' . $errstr . ' : ' . $errfile);
	//返回true，表示错误处理不会继续调用
});
*/

$ageException = new AgeException();
try {
	@var_dump($undefined);//这里虽然屏蔽了错误,但会走 register_shutdown_function()
	//throw new NameException('name error');
	throw $ageException->setMessage('我是父类设置的');
}catch (NameException $nameException) {
	echo $nameException->getMessage();//name异常
}catch (AgeException $ageException) {
	echo 'ageException : '.$ageException->getMessage(),"\n";
}catch (Exception $exception) {
	echo 'baseException',"\n"; #常用类 实现于 Throwable
}catch (Throwable $exception){
	echo 'base interface '; #基类接口
}finally {
	// 无论抛出什么样的异常都会执行，并且在正常程序继续之前执行
	var_dump(3232);
}
echo "我是正常程序,do something","\n";

try {
	throw $ageException->setMessage('我重新设置值了哦');
}catch (Exception $exception) {
	echo "我试试呐: {$exception->getMessage()}","\n";
}
