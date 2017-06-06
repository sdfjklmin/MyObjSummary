<?php
namespace lib;
use lib\PYInitials;
/**
 * 公共方法
 * Class Common
 * @package lib
 */
class Common
{
    /**
     * 财务收款项目
     * @var array
     */
    public static $services = [
        1  => ['id' => 1, 'name' => '按揭还款', 'jm' => 'AJHK'],
        2  => ['id' => 2, 'name' => '短借还款', 'jm' => 'DJHK'],
        3  => ['id' => 3, 'name' => '卡车销售', 'jm' => 'KCXS'],
        5  => ['id' => 5, 'name' => '首付合计', 'jm' => 'SFHJ'],
        6  => ['id' => 6, 'name' => '挂靠费', 'jm' => 'GKFY'],
        9  => ['id' => 9, 'name' => '牌证费', 'jm' => 'PZF'],
        10 => ['id' => 10, 'name' => '三包收款', 'jm' => 'SBSK'],
        11 => ['id' => 11, 'name' => '保险投保', 'jm' => 'BXTB'],
        13 => ['id' => 13, 'name' => '配件销售', 'jm' => 'PJXS'],
        14 => ['id' => 14, 'name' => '车辆维修', 'jm' => 'CLWX'],
        15 => ['id' => 15, 'name' => '调查费', 'jm' => 'DCF'],
        16 => ['id' => 16, 'name' => '转籍费', 'jm' => 'ZJF'],
        19 => ['id' => 19, 'name' => '合格证变更', 'jm' => 'HGZBG'],
        20 => ['id' => 20, 'name' => '过户费', 'jm' => 'GHF'],
        21 => ['id' => 21, 'name' => '卡车报废', 'jm' => 'GKF'],
        22 => ['id' => 22, 'name' => '安全保证金', 'jm' => 'AQBZJ'],
        23 => ['id' => 23, 'name' => '保险返利', 'jm' => 'BXFL'],
        24 => ['id' => 24, 'name' => '牌证补办', 'jm' => 'PZBB'],
        25 => ['id' => 25, 'name' => '采购退货', 'jm' => 'CGTH'],
        26 => ['id' => 26, 'name' => '年审费用', 'jm' => 'ZFNSFY'],
        27 => ['id' => 27, 'name' => '二级维护费', 'jm' => 'ZFWHFY'],
        28 => ['id' => 28, 'name' => '续保费用', 'jm' => 'ZFXBFY'],
        29 => ['id' => 29, 'name' => '续挂费用', 'jm' => 'ZFXGFY'],
        30 => ['id' => 30, 'name' => '安全保证金', 'jm' => 'ZFAQBZJ'],
        31 => ['id' => 31, 'name' => '银行放款',   'jm' => 'ZYYHFK'],
        32 => ['id' => 32, 'name' => '定位设备费', 'jm' => 'ZYDWSBF'],
        33 => ['id' => 33, 'name' => '手续费',     'jm' => 'ZYSXF'],
        34 => ['id' => 34, 'name' => '风险费',     'jm' => 'ZYFXF'],
        35 => ['id' => 35, 'name' => '档案费',     'jm' => 'ZYDAF'],
        36 => ['id' => 36, 'name' => '公证费',     'jm' => 'ZYGZF'],
        37 => ['id' => 37, 'name' => '首付车款',   'jm' => 'ZYSFCK'],
        38 => ['id' => 38, 'name' => '保证金',     'jm' => 'ZYBZJ'],
        39 => ['id' => 39, 'name' => '续保保证金', 'jm' => 'ZYXBBZJ'],
    ];

    /**
     * 得到客户详情
     * @param $order_source
     * @param $customer_id
     * @return mixed
     */
    public static function getCustomer($order_source, $customer_id)
    {
        $db     = new Db('erp');
        $db_gbd = new Db('gbd');
        //线下
        if ($order_source == 1) {
            $sql = "SELECT
                      name,
                      CASE type WHEN 1 THEN  '普通客户' WHEN 2 THEN '高级客户' ELSE 'VIP' END AS type,
                      phone1,
                      phone2,
                      address
                    FROM m_b_customer WHERE id = " . $customer_id;
            $data                = $db->query($sql);
            $data                = $data[0];
            $customer['name']    = $data['name'];
            $customer['type']    = $data['type'];
            $customer['phone']   = $data['phone1'] ? $data['phone1'] : $data['phone2'];
            $customer['address'] = $data['address'];
        }
        //线上
        else {
            $sql                 = "  SELECT real_name,telphone FROM member WHERE id = " . $customer_id;
            $data                = $db_gbd->query($sql);
            $data                = $data[0];
            $customer['name']    = $data['real_name'];
            $customer['type']    = '';
            $customer['phone']   = $data['telphone'];
            $customer['address'] = '';
        }

        return $customer;
    }

    /**
     * 获取订单对应客户信息
     * @access public
     * @param $id 客户id（修复数据所属客户id）
     * @param $name 客户名称（修复数据所属客户名称）
     * @param $companyCode 公司码（修复数据所属公司码）
     * @param $companyId=0 公司id（修复数据所属公司id）
     * @return void
     * @author knight
     */
    public function getCustomerInfo($id,$name,$companyCode,$companyId=0)
    {
        if(empty($id) || empty($name) || empty($companyCode)){
            return false;
        }
        $gbd = new Db('gbd');
        $sql = 'select id as member_id,real_name as name,company_code as m_code,id_card,telphone as phone,telphone,level,erp_user_id from member where id='.$id.' limit 1';
        $memberInfo = $gbd->query($sql);
        $memberInfo = $memberInfo[0];
        if(($memberInfo['level'] == 108202)){ //B2B客户判断
            $dealerNameSql = 'select name from dealer where company_code='.$memberInfo['m_code'].' limit 1'; //获取B2B客户商家名称
            $dealerName = $gbd->query($dealerNameSql);
            if($dealerName[0]['name'] != $name){//表示会员中不存在此用户，传入参数数据属于线下客户
                return -1;
            }
        } else {
            if (empty($memberInfo) || $memberInfo['name'] !=$name ) { //表示会员中不存在此用户，传入参数数据属于线下客户
                return -1;
            }
        }

        //会员表中存在此数据
        $data['name']     = is_null($memberInfo['name']) ? '' : $memberInfo['name']; //名称
        $data['phone1']   = is_null($memberInfo['phone']) ? '' : $memberInfo['phone']; //电话号
        $data['p_member_id'] = $memberInfo['member_id']; //平台会员id

        $db = new Db('erp');
        if(!empty($memberInfo['erp_user_id']) && $memberInfo['level'] == 108202){ //B2B客户
            //获取B2B客户商家信息
            $sql11 = 'select name,legal_phone,company_code,id as dealer_id,legal_phone,business_license_no,uniform_social_credit_codes from dealer where company_code='.$memberInfo['m_code'];
            $dealerInfo = $gbd->query($sql11); //获取B2B客户信息
            $data['name']     = is_null($dealerInfo[0]['name']) ? '' : $dealerInfo[0]['name']; //公司名称
            $data['phone1']   = is_null($dealerInfo[0]['legal_phone']) ? '' : $dealerInfo[0]['legal_phone']; //法人电话

            $data['phone2'] = is_null($memberInfo['telphone']) ? '' : $memberInfo['telphone'];//联系人电话
            $sql1 = 'select name from m_b_user where id='.$memberInfo['erp_user_id'];
            $userInfo = $db->query($sql1);
            $data['linkman'] = $userInfo[0]['name']; //联系人
            $data['b_code']  = $memberInfo['m_code'];//B2B 会员所属公司码
            $data['card'] = !empty($dealerInfo[0]['business_license_no']) ? $dealerInfo[0]['business_license_no'] : $dealerInfo[0]['uniform_social_credit_codes'];
            $data['birthday'] = '';
            $data['type'] = 2; //等级为 批发
        } else {
            $data['card']     = is_null($memberInfo['id_card']) ? '' : $memberInfo['id_card']; //身份证
            $data['birthday'] = $this->getIDCardInfo($data['card'], 0); //生日
            $data['birthday'] =  $data['birthday']['birthday'];
        }

        //检查是否存在对应会员的客户数据
        $customerId = $this->checkMemberIsExist($memberInfo['name'],$memberInfo['phone'],$companyCode,$memberInfo['level'],$memberInfo['m_code']);
        if ($customerId) { //存在对应会员的客户客户信息
            $dataStr = '';
            foreach($data as $k=>$v){
                if(is_string($v) || empty($v)){
                    $dataStr .= $k.'=\''.$v.'\',';
                } else {
                    $dataStr .= $k.'='.$v.',';
                }
            }
            $dataStr = substr($dataStr,0,strlen($dataStr)-1);
            $sql2 = 'update m_b_customer set '.$dataStr.' where id='.$customerId;
            $ret = $db->exec($sql2);
            if($ret === false){
                return [];
            }
            $customer_stauts = true;
        } else { //不存在对应会员客户id
            $data['company_code'] = $companyCode;
            if(!$companyId){
                $comIdSql = 'select id from m_b_organization where is_head =1 and company_code='.$companyCode.' and type=1'.' and pid=0 and cid=0 and is_del=0';
                $companyId = $db->query($comIdSql);
                if(!$companyId){
                    Log::info($companyCode.'对应公司不存在');
                    return false;
                }
                $companyId = $companyId[0]['id'];
            }
            $data['company_id']   = $companyId;
            $data['type']  = !isset($data['type']) ? 1 : $data['type'];//等级为 普通
            $cusSql = 'select max(customer_number) as customer_number  from m_b_customer where company_code='.$data['company_code'];
            $max = $db->query($cusSql);
            // 设置客户编号
            $data['customer_number'] = !empty($max) ? $max[0]['customer_number'] + 1 : 1;
            //设置客户简码
            $pinyin = new PYInitials();
            $data['jm']     = $pinyin->pinyin($name,1);

            $insertField = implode(',',array_keys($data));
            $valueStr = '';
            foreach($data as $k=>$v){
                if(is_string($v) || empty($v)){
                    $valueStr .= '\''.$v.'\',';
                } else {
                    $valueStr .= $v.',';
                }
            }
            $valueStr = substr($valueStr,0,strlen($valueStr)-1);
            $sql3 = 'insert into m_b_customer ('.$insertField.') value ('.$valueStr.')';
            Log::info('新增客户线下:'.$sql3);
            $ret = $db->exec($sql3);
            if($ret === false){
                return [];
            }
            $customerId = $db->lastInsertId();
            if(empty($customerId)){
                return [];
            }
            $customer_stauts = false;
        }
        //获取客户信息
        $sql4 = 'select * from m_b_customer where id='.$customerId;
        $customerInfo = $db->query($sql4);

        $data = $customerInfo[0];
        $data['customer_stauts'] = $customer_stauts;
        return $data;
    }

    /**
     * 检查是否存在对应会员的客户
     * @access public
     * @param $name 会员名称
     * @param $phone 会员联系电弧
     * @param $companyCode 供应商（商家）公司码
     * @param $level 会员等级
     * @param $mCode 会员公司码
     * @return void
     * @author knight
     */
    private function checkMemberIsExist($name,$phone,$companyCode,$level,$mCode)
    {
        $where = 'is_del=0 and company_code='.$companyCode;
        if($level == 108202 && !empty($mCode)){ //B2B客户 用公司码 检查
            $where .= ' and b_code='.$mCode;//公司码
        } else { //普通会员用 名称电话 检查
            $where .=' and name=\''.$name.'\' and phone1=\''.$phone.'\'';//名称 联系电话
        }
        $sql = 'select id from m_b_customer where '.$where.' limit 1';
        $db = new Db('erp');
        $customerInfo = $db->query($sql);
        if(!empty($customerInfo)){
            return $customerInfo[0]['id'];
        }
        return false;
    }


    /**
     * 用php从身份证中提取生日,包括15位和18位身份证
     * @access public
     ** @param $IDCard 身份证号
     * @param int $format 生日格式 1 1992-3-28； 0:3-28
     * @return mixed
     * @author knight
     */
    function getIDCardInfo($IDCard,$format=1)
    {
        $result['error']=0;//0:未知错误，1:身份证格式错误，2:无错误
        $result['flag']='';//0标示成年，1标示未成年
        $result['tdate']='';//生日，格式如:2012-11-15
        if(!preg_match("/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/",$IDCard)){
            $result['error']=1;
            return $result;
        }else{
            if(strlen($IDCard)==18)
            {
                $tyear=intval(substr($IDCard,6,4));
                $tmonth=intval(substr($IDCard,10,2));
                $tday=intval(substr($IDCard,12,2));
            }
            elseif(strlen($IDCard)==15)
            {
                $tyear=intval("19".substr($IDCard,6,2));
                $tmonth=intval(substr($IDCard,8,2));
                $tday=intval(substr($IDCard,10,2));
            }

            if($tyear>date("Y")||$tyear<(date("Y")-100))
            {
                $flag=0;
            }
            elseif($tmonth<0||$tmonth>12)
            {
                $flag=0;
            }
            elseif($tday<0||$tday>31)
            {
                $flag=0;
            }else
            {
                if($format)
                {
                    $tdate=$tyear."-".$tmonth."-".$tday;
                }
                else
                {
                    $tdate=$tmonth."-".$tday;
                }

                if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60)
                {
                    $flag=0;
                }
                else
                {
                    $flag=1;
                }
            }
        }
        $result['error']=2;//0:未知错误，1:身份证格式错误，2:无错误
        $result['isAdult']=$flag;//0标示成年，1标示未成年
        $result['birthday']=$tdate;//生日日期
        return $result;
    }


}
