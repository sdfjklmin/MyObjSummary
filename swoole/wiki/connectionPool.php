<?php

use Swoole\Coroutine;
use Swoole\Database\PDOConfig;
const N = 1024;

\Swoole\Runtime::enableCoroutine();
$s = microtime(true);
Swoole\Coroutine\run(function () {
	$pool = new \Swoole\Database\PDOPool(
							(new PDOConfig)
							->withHost('127.0.0.1')
							->withPort(3306)
							// ->withUnixSocket('/tmp/mysql.sock')
							->withDbName('pp_sns')
							->withCharset('utf8mb4')
							->withUsername('root')
							->withPassword('root123')
	);
	for ($n = N; $n--;) {
		Coroutine::create(function () use ($pool) {
			$pdo       = $pool->get();
			$statement = $pdo->prepare('SELECT ? + ?');
			if (!$statement) {
				throw new RuntimeException('Prepare failed');
			}
			$a      = mt_rand(1, 100);
			$b      = mt_rand(1, 100);
			$result = $statement->execute([$a, $b]);
			if (!$result) {
				throw new RuntimeException('Execute failed');
			}
			$result = $statement->fetchAll();
			if ($a + $b !== (int)$result[0][0]) {
				throw new RuntimeException('Bad result');
			}
			$pool->put($pdo);
		});
	}
});
$s = microtime(true) - $s;
echo 'Use ' . $s . 's for ' . N . ' queries' . PHP_EOL;
