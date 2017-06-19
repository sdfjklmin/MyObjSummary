<?php
/**
 * 【保险业务明细】脚本
 */
use lib\Log;

$this->set([
    'init'    => 10,
    'tb_name' => 'm_report_inte_baoxian',
]);
$rep_name = '[保险业务明细]';
Log::info($rep_name . '开始执行');

try {

    // 主表数据
    $qdata = $this->db->query("SELECT * FROM m_inte_baoxian_vin v LEFT JOIN m_inte_master_order o ON o.id = v.master_id WHERE o.order_status = 5 AND o.check_status = 8 AND o.check_status_bx = 23;");
    if ($qdata) {
        $master_ids = implode(',', array_unique(array_column($qdata, 'master_id')));

        // 商业险表数据
        $sdata    = $this->db->query("SELECT * FROM m_inte_baoxian_syx WHERE master_id IN ({$master_ids}) AND brand_id NOT IN (10,30,31)");
        $sml_type = [
            11 => 'csx',
            13 => 'szx',
            19 => 'dqx',
            21 => 'bsx',
            22 => 'zrx',
            23 => 'wgczrx',
            25 => 'hwx',
            26 => 'hhx',
            27 => 'ssx',
            28 => 'fdjtbssx',
            29 => 'xzsbx',
        ];
        $syx_data = [];
        foreach ($sdata as $s) {
            $k = $s['master_id'] . '_' . $s['class_id'];
            if (in_array($s['brand_id'], [15, 16, 17])) {
                $syx_data[$k]['czx_' . $s['brand_id'] . '_baoe']   = $s['baoe'];
                $syx_data[$k]['czx_' . $s['brand_id'] . '_sumfee'] = $s['fee'];
            } else {
                $syx_data[$k][$sml_type[$s['brand_id']] . '_baoe']   = $s['baoe'];
                $syx_data[$k][$sml_type[$s['brand_id']] . '_sumfee'] = $s['fee'];
            }

            // 处理玻碎险保额
            if ($s['brand_id'] == 21) {
                if ($s['baoe']) {
                    $bsx      = json_decode($s['baoe']);
                    $bsx_type = [1 => '国产', 2 => '进口'];
                    if ($bsx->type) {
                        $bsx_type = '(' . $bsx_type[$bsx->type] . ')';
                    } else {
                        $bsx_type = '';
                    }
                    $syx_data[$k][$sml_type[$s['brand_id']] . '_baoe'] = $bsx->baoe . $bsx_type;
                }
            }

            // 处理不计免赔
            if ($s['isnon'] == 'Y') {
                if ($s['brand_id'] == 15) {
                    $brand_name = '乘坐险-驾驶员';
                } else if (in_array($s['brand_id'], [16, 17])) {
                    $brand_name = '乘坐险-乘客';
                } else {
                    $brand_name = $s['brand_name'];
                }
                if (isset($syx_data[$k]['bjmp'])) {
                    if (strpos($syx_data[$k]['bjmp'], '乘坐险-乘客') === false || $brand_name != '乘坐险-乘客') {
                        $syx_data[$k]['bjmp'] .= ',' . $brand_name;
                    }
                } else {
                    $syx_data[$k]['bjmp'] = $brand_name;
                }
            }

            // 处理乘坐险
            if (isset(
                $syx_data[$k]['czx_15_baoe'],
                $syx_data[$k]['czx_16_baoe'],
                $syx_data[$k]['czx_17_baoe'])
            ) {
                $syx_data[$k]['czx_baoe'] = '';
                if ($syx_data[$k]['czx_15_baoe']) {
                    $syx_data[$k]['czx_baoe'] .= '驾驶员：' . $syx_data[$k]['czx_15_baoe'];
                }
                if ($syx_data[$k]['czx_16_baoe']) {
                    $syx_data[$k]['czx_baoe'] .= '，乘客：' . $syx_data[$k]['czx_16_baoe'] . '人';
                }
                if ($syx_data[$k]['czx_17_baoe']) {
                    $syx_data[$k]['czx_baoe'] .= '×' . $syx_data[$k]['czx_17_baoe'];
                }
                unset(
                    $syx_data[$k]['czx_15_baoe'],
                    $syx_data[$k]['czx_16_baoe'],
                    $syx_data[$k]['czx_17_baoe']
                );
            }
            if (isset(
                $syx_data[$k]['czx_15_sumfee'],
                $syx_data[$k]['czx_16_sumfee'],
                $syx_data[$k]['czx_17_sumfee'])
            ) {
                $syx_data[$k]['czx_sumfee'] = bcadd($syx_data[$k]['czx_15_sumfee'], $syx_data[$k]['czx_17_sumfee'], 2);
                unset(
                    $syx_data[$k]['czx_15_sumfee'],
                    $syx_data[$k]['czx_16_sumfee'],
                    $syx_data[$k]['czx_17_sumfee']
                );
            }
        }

        // 申请表数据
        $adata    = $this->db->query("SELECT * FROM m_inte_baoxian_order_apply WHERE master_id IN ({$master_ids})");
        $apy_data = [];
        foreach ($adata as $a) {
            // 计算车辆数
            $car_count = 1;
            foreach ($qdata as $q) {
                if ($q['master_id'] == $a['master_id']) {
                    $car_count = $q['car_count'];
                    break;
                }
            }
            $apy_data[$a['master_id'] . '_' . $a['class_id']] = [
                'ccs_apy_sumfee' => $a['ccs_fee'],
                'jqx_apy_sumfee' => $a['jqx_fee'],
                'syx_apy_sumfee' => $a['syx_fee'],
                'ywx_apy_sumfee' => $a['ywx_fee'],
                'ywx_baoe'       => $a['ywx_baoe'],
            ];
        }

        // 经办人数据
        $deal  = $this->db->query("SELECT master_id,deal_name FROM m_inte_baoxian_order_handle WHERE master_id IN ({$master_ids})");
        $deals = [];
        foreach ($deal as $d) {
            $deals[$d['master_id']] = $d['deal_name'];
        }

        // 车辆信息数据
        $car_ids  = implode(',', array_unique(array_column($qdata, 'car_id')));
        $cars     = $this->db->query("SELECT id,brand_name,type_name,category_name,series_name FROM m_c_car WHERE id IN ({$car_ids})");
        $car_data = [];
        foreach ($cars as $c) {
            $car_data[$c['id']] = [
                'car_brand'    => $c['brand_name'],
                'car_type'     => $c['type_name'],
                'car_category' => $c['category_name'],
                'car_series'   => $c['series_name'],
            ];
        }

        // 拼接数据
        $data = [];
        // 默认值
        $default = [
            'type'              => 1,
            'master_id'         => 0,
            'company_code'      => 0,
            'license_plate'     => '',
            'system_number'     => '',
            'customer_name'     => '',
            'vin'               => '',
            'jqx_brand_name'    => '',
            'jqx_com_name'      => '',
            'jqx_start_time'    => 0,
            'jqx_end_time'      => 0,
            'syx_brand_name'    => '',
            'syx_com_name'      => '',
            'syx_start_time'    => 0,
            'syx_end_time'      => 0,
            'ywx_brand_name'    => '',
            'ywx_com_name'      => '',
            'ywx_start_time'    => 0,
            'ywx_end_time'      => 0,
            'csx_baoe'          => '',
            'csx_sumfee'        => '0.00',
            'czx_baoe'          => '',
            'czx_sumfee'        => '0.00',
            'dqx_baoe'          => '',
            'dqx_sumfee'        => '0.00',
            'hwx_baoe'          => '',
            'hwx_sumfee'        => '0.00',
            'wgczrx_baoe'       => '',
            'wgczrx_sumfee'     => '0.00',
            'bsx_baoe'          => '',
            'bsx_sumfee'        => '0.00',
            'szx_baoe'          => '',
            'szx_sumfee'        => '0.00',
            'zrx_baoe'          => '',
            'zrx_sumfee'        => '0.00',
            'ssx_baoe'          => '',
            'ssx_sumfee'        => '0.00',
            'fdjtbssx_baoe'     => '',
            'fdjtbssx_sumfee'   => '0.00',
            'xzsbx_baoe'        => '',
            'xzsbx_sumfee'      => '0.00',
            'hhx_baoe'          => '',
            'hhx_sumfee'        => '0.00',
            'bjmp'              => '',
            'ccs_apy_sumfee'    => '0.00',
            'ccs_sumfee'        => '0.00',
            'jqx_apy_sumfee'    => '0.00',
            'jqx_sumfee'        => '0.00',
            'syx_apy_sumfee'    => '0.00',
            'syx_sumfee'        => '0.00',
            'ywx_apy_sumfee'    => '0.00',
            'ywx_sumfee'        => '0.00',
            'jqx_rebate_sumfee' => '0.00',
            'syx_rebate_sumfee' => '0.00',
            'ywx_rebate_sumfee' => '0.00',
            'jqx_tax_sumfee'    => '0.00',
            'syx_tax_sumfee'    => '0.00',
            'ywx_tax_sumfee'    => '0.00',
            'ywx_baoe'          => '0.00',
            'car_brand'         => '',
            'car_type'          => '',
            'car_category'      => '',
            'car_series'        => '',
            'deal_name'         => '',
            'operate_time'      => 0,
            'remark'            => '',
        ];
        $big_type = [1 => 'jqx', 2 => 'syx', 3 => 'ywx', 4 => 'ccs'];
        foreach ($qdata as $q) {
            // 忽略无效数据
            if (empty($q['master_id']) || empty($q['car_id'])) {
                continue;
            }
            $k = $q['master_id'] . '_' . $q['car_id'];
            $c = $q['master_id'] . '_' . $q['class_id'];
            if (!isset($data[$k])) {
                // 设置默认值
                $data_base = [
                    'master_id'     => $q['master_id'],
                    'license_plate' => $q['license_plate'],
                    'system_number' => $q['system_number'],
                    'customer_name' => $q['customer_name'],
                    'vin'           => $q['vin'],
                    'operate_time'  => $q['operate_time_1'],
                    'remark'        => $q['remark'],
                    'car_count'     => $q['car_count'],
                    'company_code'  => $q['company_code'],
                ];

                $data_base = array_merge($default, $data_base);

                // 拼接商业险数据
                if (isset($syx_data[$c])) {
                    $data_base = array_merge($data_base, $syx_data[$c]);
                }

                // 拼接申请表数据
                if (isset($apy_data[$c])) {
                    $data_base = array_merge($data_base, $apy_data[$c]);
                }

                // 拼接经办人
                if (isset($deals[$q['master_id']])) {
                    $data_base['deal_name'] = $deals[$q['master_id']];
                }

                // 拼接车辆信息
                if (isset($car_data[$q['car_id']])) {
                    $data_base = array_merge($data_base, $car_data[$q['car_id']]);
                }
                $data[$k] = $data_base;
            }

            $pre     = $big_type[$q['type']] . '_';
            $bx_data = [
                $pre . 'brand_name'    => $q['brand_name'],
                $pre . 'com_name'      => $q['com_name'],
                $pre . 'start_time'    => $q['start_time'],
                $pre . 'end_time'      => $q['end_time'],
                $pre . 'sumfee'        => $q['fee'],
                $pre . 'rebate_sumfee' => $q['rebate_fee'],
                $pre . 'tax_sumfee'    => $q['tax_fee'],
            ];

            $data[$k] = array_merge($data[$k], $bx_data);
        }
    }
    $data = array_merge(array_values($data), include ('rep_baoxian_fw.php'));

    // 执行插入
    if (empty($data)) {
        throw new Exception('没有查询到数据');
    }

    // 开启事务
    $this->db->beginTransaction();

    // 插入数据到统计表
    $total = count($data);
    for ($i = 0; $i < $total; $i++) {
        if ($i % 1000 == 0) {
            if (isset($sql)) {
                $sql = rtrim($sql, ',') . ';';
                $this->db->exec($sql);
            }
            $hsql = 'INSERT INTO `' . $this->tb . '` (';
            foreach ($default as $k => $f) {
                $hsql .= "`{$k}`,";
            }
            $sql = rtrim($hsql, ',') . ") VALUES ";
        }
        $t    = $data[$i];
        $tsql = '(';
        foreach ($default as $k => $f) {
            $tsql .= '"' . $t[$k] . '",';
        }
        $sql .= rtrim($tsql, ',') . '),';

        if ($i == $total - 1) {
            $sql = rtrim($sql, ',') . ';';
            $this->db->exec($sql);
        }
    }
    $this->db->commit();
    Log::info($rep_name . '执行完成，插入行数:' . $total);
} catch (Exception $e) {
    $this->db->rollback();
    Log::error($rep_name . $e->getMessage() . PHP_EOL . 'SQL: ' . $this->db->sql());
}
