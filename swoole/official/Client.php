<?php
class Client
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
            echo "Error: {$this->client->errMsg}[{$this->client->errCode}]\n";
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

$client = new Client();
$client->connect();