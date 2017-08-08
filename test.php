<?php 
/**
* RPC
*/
class TestYar
{
	
	public function __construct()
	{
		
	}

    public function test($value='')
	{
		# code...
		echo "this is test";
	}
}

$service = new Yar_Server(new TestYar());
$service->handle();