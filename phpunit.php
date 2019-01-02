<?php 

use PHPUnit\Framework\TestCase;
/**
* 
*/
class TestUnit extends TestCase
{
	
	public function testOne($value='')
	{
		# code...
        echo "\n";
		echo "one";
	}
}
(new TestUnit())->testOne();
//php phpunit.phar phpunit.php
?>