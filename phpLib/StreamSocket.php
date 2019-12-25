<?php


namespace lib;

/** socket server
 * Class SocketServer
 * @author sjm
 */
class SocketServer
{
    protected $responseHttp = "HTTP/1.1 200 OK\r";
    protected $responseContentText = "Content-Type: text/plain\r";
    protected $responseContentLength = 'Content-Length: $length \r';
    protected $responseConnection = "Connection: close\r";

    /**
     * SocketServer constructor.
     * @param string $type
     * @param int $port
     */
    public function __construct($type = 'tcp', $port = 40000)
    {
        echo "Starting server at port 40000...\n";

        //创建服务端
        $socket = @stream_socket_server("{$type}://localhost:{$port}", $errNo, $errStr);

        if (!$socket) exit($errStr);

        //设置模式
        stream_set_blocking($socket, 0);

        while (true) {
            //接受数据请求
            $clientSocket = @stream_socket_accept($socket, 0);
            if($clientSocket) {
                echo "Received request ... \n";
                $this->handleClient($clientSocket);
            }
        }
    }

    protected function handleClient($socket)
    {
        $requestData = fread($socket, 8192);
        echo "[Request data start] ".date('Y-m-d H:i:s').".....................\n";
        //数据信息，格式为 http 请求头格式，此处没有与PHP的系统变量进行绑定，所以不能使用系统变量
        var_dump($requestData);
        echo "[Request data end]   ".date('Y-m-d H:i:s').".......................\n";
        /*$response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg

RES;*/
        //携带响应头
        fwrite($socket, $this->linkResponse('this is test'));
        //简单的响应
        //fwrite ($socket, "OK\n");
        fclose($socket);
        echo "Response request ... \n";
    }

    protected function linkResponse($responseData)
    {
        return <<<RES
$this->responseHttp
$this->responseContentText
$this->responseConnection
\r
$responseData
RES;
    }
}

/** socket client
 * Class SocketClient
 * @author sjm
 * @package lib
 */
class SocketClient
{

    public function __construct($message, $type = 'tcp', $port = 40000)
    {
        //建立连接
        $fp = stream_socket_client("tcp://$type:$port", $errno, $errstr);
        if (!$fp) {
            echo "ERROR : $errno - $errstr<br />\n";
        } else {
            //写入数据
            fwrite($fp,"$message\n");
            //读取响应
            $response =  fread($fp, 4);
            //根据响应判断
            if ($response != "OK\n"){
                echo 'The command couldn\'t be executed...\ncause :'.$response;
            } else{
                echo 'Execution successfull...';
            }
            fclose($fp);
        }
    }
}