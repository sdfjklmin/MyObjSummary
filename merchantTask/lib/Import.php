<?php
namespace lib;

/**
 * 文件运行类
 */
class Import
{
    private $isSet = false;

    public $file;

    public $startTime;

    public $endTime;

    public $tb;

    public $db;

    public $gbd;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function run()
    {
        include $this->file;
    }

    /**
     * 设置参数
     */
    public function set($opt)
    {
        if ($this->isSet === true) {
            return;
        }

        $t = (new \Thread())->getCurrentThread();
        // 忽略脚本
        if (isset($opt['ignore']) && $opt['ignore'] === true) {
            $t->end();
            exit;
        }

        // 检查一次性脚本
        if (isset($opt['isOne']) && $opt['isOne'] === true) {
            if ($t->hasOne()) {
                $t->end();
                exit;
            }
            $t->isOne = true;
            return;
        }

        if (isset($opt['update']) && $opt['update']) {
            $t->update = $opt['update'];
            if (isset($opt['tb_name']) && $opt['tb_name']) {
                $t->tbName = $opt['tb_name'];
                $this->tb  = $opt['tb_name'];
            }
        }

        if (isset($opt['init']) && $opt['init']) {
            if (!isset($opt['tb_name']) || empty($opt['tb_name'])) {
                Log::error('全量数据模式必须设置tb_name');
                $t->isEnd = true;
                exit;
            }
            $t->init   = $opt['init'];
            $t->tbName = $opt['tb_name'];
            $this->tb  = $opt['tb_name'] . '_tasktmp';
            $t->initData();
        }
        $this->isSet = true;
    }
}
