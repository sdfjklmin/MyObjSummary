<?php
namespace lib;

use PDO;
use PDOException;

/**
 * 数据库驱动
 */
class Db
{
    private $con;
    private $db;
    private $re_times = 5;
    private static $sql;

    public function __construct($db = 'erp')
    {
        $this->db = $db;
        $this->connect();
    }

    private function connect()
    {
        $conf       = c($this->db);
        $dsn        = 'mysql:host=' . $conf['host'] . ';dbname=' . $conf['name'];
        $re_connect = true;
        for ($i = 1; $i <= $this->re_times + 1; $i++) {
            try {
                $this->con = new PDO($dsn, $conf['user'], $conf['pwd'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $this->exec('set names utf8');
                $i = $this->re_times + 2;
            } catch (PDOException $e) {
                if ($i == 1) {
                    Log::error('数据库连接失败，尝试重连...');
                } else if ($i == $this->re_times + 1) {
                    Log::error('数据库连接失败，任务退出...');
                    exit;
                }
                o('数据库第' . $i . '次重连...');
                sleep(1);
            }
        }
    }

    public function query($sql)
    {
        self::$sql = $sql;
        $query     = $this->con->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exec($sql)
    {
        self::$sql = $sql;
        return $this->con->exec($sql);
    }

    public function beginTransaction()
    {
        if (!$this->con->inTransaction()) {
            $this->con->beginTransaction();
        }
    }

    public function checkConnect()
    {
        try {
            $this->con->query('SELECT 1;');
        } catch (PDOException $e) {
            $this->connect();
        }
    }

    public static function sql()
    {
        return self::$sql;
    }

    public function __call($method, $arg)
    {
        return call_user_func_array([$this->con, $method], $arg);
    }
}
