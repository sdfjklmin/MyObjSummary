<?php

/** 创建一个服务器
 * Class AServer
 * @author sjm
 * @link https://wiki.swoole.com/wiki/page/274.html
 * @stepImg AServer.png
 * @desc 一定要认真理解示意图,做到了然于胸
 * @other
 * 		`taskUse:`	task使用标注
 */
class AServer
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
		//设置对应回调,回调可设置4中,类+方法,匿名,类静态,函数
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
		//taskUse:task 任务
		$this->serv->on('Task',array($this,'onTask'));
		$this->serv->on('Finish',array($this,'onFinish'));
		$this->serv->set(array(
			 'worker_num' => 4, #worker进程数
			 'daemonize' => false, #进程守护,若为true将转入后台作为守护进程运行
			 'task_worker_num' => 2, //taskUse: 设置启动2个task进程
     	));

        $this->serv->start();
    }

	/**
	 * @param $serv swoole_server swoole_server对象
	 * @param $task_id int 任务id
	 * @param $from_id int 投递任务的worker_id
	 * @param $data string 投递的数据
	 */
	public  function onTask(swoole_server $serv, $task_id, $from_id, $data)
	{
		echo 'onTask',"\n";
		sleep(5);
		echo "do something {$data}\n";
		return true;
	}

	/**
	 * @param $serv swoole_server swoole_server对象
	 * @param $task_id int 任务id
	 * @param $data string 任务返回的数据
	 */
	public function onFinish(swoole_server $serv, $task_id, $data)
	{
		echo 'onTaskFinish',"\n";
	}

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onConnect($serv, $fd, $from_id ) {
    	/**@var \Swoole\Server $serv */
        //$serv->send( $fd, "Hello {$fd}!" );
    }

	/** 接收到消息
	 * @param swoole_server $serv
	 * @param $fd
	 * @param $from_id
	 * @param $data
	 */
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";

        //taskUse: -1代表不指定task进程
		$serv->task('task data' , -1 );

        $tipMap = [
        	'你好' => '你好啊!','你是'=>'我是Swoole Server','现在时间'=>date('Y-m-d H:i:s'),
			'再见' => '下次再来哦,亲(づ￣3￣)づ╭❤～'
		];
        $initData = trim($data);
        $msg = $tipMap[$initData] ?? '你说什么?';
        $serv->send($fd, $msg."\n");


    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}
// 启动服务器 Start the server
$server = new AServer();