<?php
class AClient
{
    private $client;

    /** init client
     * Client constructor.
     */
    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect() {
        //link and check this client
        if( !$this->client->connect("127.0.0.1", 9501 , 1) ) {
            echo "Link Error \n";exit();
        }

        fwrite(STDOUT, "请输入消息 Please input msg：");
        $msg = trim(fgets(STDIN));

        //send msg
        $this->client->send( $msg );

        //get msg response
        $message = $this->client->recv();

        echo "Get Message From Server:{$message}\n";
    }
}

/*$client = new AClient();
$client->connect();*/

$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
	exit("connect failed. Error: {$client->errCode}\n");
}
while (true) {
	fwrite(STDOUT, "自由人:");
	$msg = trim(fgets(STDIN));
	$client->send($msg."\n");
	echo "机器人:".$client->recv();
}
//$client->close();