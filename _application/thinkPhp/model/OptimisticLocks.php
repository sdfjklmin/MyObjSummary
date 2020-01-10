<?php


namespace app\vdsns\model;

use think\Model;

/** 简单的乐观锁
 *  UPDATE
 *      `table` SET `filed`='value', `lock_version`='+1' 同步更新版本号
 *  WHERE
 *      (`id`='1') AND (`lock_version`='1') 更新条件带上版本号信息
 * Class OptimisticLocks
 * @author sjm
 * @package app\vdsns\model
 */
abstract class OptimisticLocks extends Model
{
    /** 数据库锁字段
     * @var null | string
     */
    protected $lock_field = null;

    /** 当前模型的主键字段
     * @var string
     */
    protected $pk_id  = 'id';

    /** 获取字段信息
     * @return string |null
     */
    private function optimisticLock()
    {
        return $this->lock_field;
    }

    /** 更新带锁
     * @param array $data
     * @param array $where
     * @param null $sequence
     * @return bool
     * @throws OptimisticLocksException
     */
    public function saveWithLock($data = [], $where = [], $sequence = null)
    {
        //获取版本号字段的字段名
        $lock = $this->optimisticLock();

        //如果 optimisticLock() 返回的是 null，那么，不启用乐观锁。
        $lockValue = '';
        if ($lock !== null) {
            //将 $lock 加入 条件和更新 中
            $lockValue      =   $this->$lock + 1;
            $data[$lock]    =   $lockValue;
            $where[$lock]   =   $this->$lock;

            //强制加入主键，覆盖Model中的主键设置
            $this->pk       =   '';
            $pk             =   $this->pk_id;
            if(!isset($where[$pk])) {
                $where[$pk] =   $this->$pk;
            }
        }
        $rows =  parent::save($data, $where, $sequence);

        if(!$rows) {
            return false;
        }

        if( ($lock !== null) ) {
            //由于TP5.1中 save()，更新后会覆盖当前的用户模型，所有这里重新获取数据
            $afterSaveData = $this->get($this->pk_id);
            if(($lockValue != $afterSaveData->$lock)) {
               throw new OptimisticLocksException('Optimistic Locks Exception');
            }
        }
        return true;

    }
}