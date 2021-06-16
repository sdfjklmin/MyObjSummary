<?php
/**
 * @doc https://developer.apple.com/library/archive/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html
 * @sandboxUrl https://sandbox.itunes.apple.com/verifyReceipt
 * @proUrl https://buy.itunes.apple.com/verifyReceipt
 * Class ApplePayVerify
 */
class ApplePayVerify
{
    /** 环境地址,默认沙箱环境
     * @var string
     */
    private $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';

    /** 沙箱环境地址
     * @var string
     */
    private $sandbox_endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';

    /** 正式运营的地址
     * @var string
     */
    private $pro_endpoint = ' https://buy.itunes.apple.com/verifyReceipt';

    /** 环境配置,默认沙箱环境
     * @var bool
     */
    private $sandbox = true;

    /** 自动订阅
     * @var bool
     */
    private $auto_renewable = false;

    /** 自动订阅的 app store密钥
     * @var
     */
    private $auto_renewable_password;

    /** 苹果bundle_id
     * @var array
     */
    private $bundle_id = [];

    /**
     * ApplePayVerify constructor.
     * @param $bundle_id
     */
    public function __construct($bundle_id)
    {
        if (is_string($bundle_id)) {
            $this->bundle_id = [$bundle_id];
        }

        if (is_array($bundle_id)) {
            $this->bundle_id = $bundle_id;
        }
    }

    /** 静态初始化
     * @param string $bundle_id
     * @return ApplePayVerify
     */
    public static function init($bundle_id)
    {
        return new self($bundle_id);
    }

	/**
	 * @param int $code
	 * @return string
	 */
    protected function errorCode($code = 0)
	{
		return [
			0     => '成功',
			21000 => 'App Store无法读取你提供的JSON数据',
			21002 => '收据数据不符合格式',
			21003 => '收据无法被验证',
			21004 => '你提供的共享密钥和账户的共享密钥不一致',
			21005 => '收据服务器当前不可用',
			21006 => '收据是有效的，但订阅服务已经过期。当收到这个信息时，解码后的收据信息也包含在返回内容中',
			21007 => '收据信息是测试用（sandbox），但却被发送到产品环境中验证',
			21008 => '收据信息是产品环境中使用，但却被发送到测试环境中验证',
		][$code];
	}

    /** 设置环境，默认为沙箱环境
     * @param bool $isSandbox
     * @return $this
     */
    public function setEnv(bool $isSandbox = true)
    {
        if ($isSandbox) {
            $this->endpoint = $this->sandbox_endpoint;
        } else {
            $this->endpoint = $this->pro_endpoint;
        }
        $this->sandbox = $isSandbox;
        return $this;
    }

    /** 设置自动订阅和密钥
     * @param bool $isAuto
     * @param string $password
     * @return $this
     */
    public function setAutoRenewable(bool $isAuto = false, $password = '')
    {
        if ($isAuto && $password) {
            $this->auto_renewable = true;
            $this->auto_renewable_password = $password;
        } else {
            $this->auto_renewable = false;
        }
        return $this;
    }

    /** result as json
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return string
     */
    private function jsonRet(int $code = 200, string $msg = '', array $data = [])
    {
        $result = [
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ];
        return json_encode($result, true);
    }

    /** code400
     * @param string $msg
     * @return string
     */
    private function code400($msg = '参数错误!')
    {
        return $this->jsonRet(400, $msg);
    }

    /** code200
     * @param array $data
     * @param string $msg
     * @return string
     */
    private function code200($data = [], $msg = '操作成功!')
    {
        return $this->jsonRet(200, $msg, $data);
    }

    /** log
     * @param $info
     */
    private function log($info)
    {
        //log info
    }

    /** 获取票证验证的数据
     * @param $receipt
     * @return false|string
     */
    protected function getReceiptData($receipt)
    {
        if ($this->auto_renewable) {
            $postData = json_encode(
                array(
                    //票证
                    'receipt-data' => $receipt,
                    //自动订阅 app store 秘钥
                    'password' => $this->auto_renewable_password
                )
                , JSON_UNESCAPED_SLASHES);
        } else {
            $postData = json_encode(
                array(
                    'receipt-data' => $receipt
                )
                , JSON_UNESCAPED_SLASHES);
        }
        return $postData;
    }

    /**　ios apple 支付验证
     * @param $receipt string 苹果支付认证的凭证(base64后的数据)
     * @return string
     */
    public function appleReceipt($receipt)
    {
        //苹果支付认证的凭证(base64后的数据)
        if (empty($receipt)) {
            return $this->code400();
        }
        //数据组装
        //$receipt ='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';
        $postData = $this->getReceiptData($receipt);
        //日志记录
        $this->log($postData);
        //curl操作
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno != 0) {
            return $this->code400('curl请求有错误!');
        } else {
            $data = json_decode($response, true);
            if (!is_array($data)) {
                return $this->code400('数据错误!');
            }
			if(isset($data['status']) && ($data['status'] == 21007)) {
				//生产环境中,苹果进行沙盒测试付费,需要走沙盒验证
			}
            //判断购买是否成功
            if (!isset($data['status']) || $data['status'] != 0) {
                return $this->code400('无效的iOS支付数据!');
            }
            //无效的bundle_id
            if (!in_array($data['receipt']['bundle_id'], $this->bundle_id)) {
                return $this->code400('无效的bundle_id:' . $data['receipt']['bundle_id']);
            }
            //多物品购买时
            // in_app为多个(坑)
            // ios一次支付可能返回多个,可能是上次成功后没有及时返回,这次成功后会把上次或上上次成功的返回
			// 通过不同的订阅类型进行处理：
			// 普通:单产品会在in_app中，订单号独立
			// 连续订阅:订单号可能会和第一次订单号相同，去重的时候需要特殊处理
			// 一次性购买:通过官方文档和返回数据进行处理
			// 不同的处理可能数据源不用 $data['receipt], $data['latest_receipt_info'], $data['pending_renewal_info']
			// 具体参考官方文档和返回示例
            if (!empty($inAppData = $data['receipt']['in_app'])) {
                //处理自身逻辑
                $this->appleAppData($inAppData);
                return $this->code200($data['receipt']);
            }
            return $this->code400('验证成功,但没有数据！');
        }
    }

	/**
	 * 数据示例
	 */
	public function appleReceiptBackData()
	{
		/**
		 * @version 1.0 -1.1.0
		 * @tag two
		 */
		$versionTwo = '{
    "msg": "操作成功",
    "code": 200,
    "data": {
        "status": 0,
        "environment": "Sandbox",
        "receipt": {
            "receipt_type": "ProductionSandbox",
            "adam_id": 0,
            "app_item_id": 0,
            "bundle_id": "com.duluduludala.btvideo",
            "application_version": "1",
            "download_id": 0,
            "version_external_identifier": 0,
            "receipt_creation_date": "2020-05-10 12:27:48 Etc/GMT",
            "receipt_creation_date_ms": "1589113668000",
            "receipt_creation_date_pst": "2020-05-10 05:27:48 America/Los_Angeles",
            "request_date": "2020-05-10 12:30:47 Etc/GMT",
            "request_date_ms": "1589113847593",
            "request_date_pst": "2020-05-10 05:30:47 America/Los_Angeles",
            "original_purchase_date": "2013-08-01 07:00:00 Etc/GMT",
            "original_purchase_date_ms": "1375340400000",
            "original_purchase_date_pst": "2013-08-01 00:00:00 America/Los_Angeles",
            "original_application_version": "1.0",
            "in_app": [
                {
                    "quantity": "1",
                    "product_id": "2003",
                    "transaction_id": "1000000662646469",
                    "original_transaction_id": "1000000662646469",
                    "purchase_date": "2020-05-10 12:27:47 Etc/GMT",
                    "purchase_date_ms": "1589113667000",
                    "purchase_date_pst": "2020-05-10 05:27:47 America/Los_Angeles",
                    "original_purchase_date": "2020-05-10 12:27:48 Etc/GMT",
                    "original_purchase_date_ms": "1589113668000",
                    "original_purchase_date_pst": "2020-05-10 05:27:48 America/Los_Angeles",
                    "expires_date": "2020-05-10 13:27:47 Etc/GMT",
                    "expires_date_ms": "1589117267000",
                    "expires_date_pst": "2020-05-10 06:27:47 America/Los_Angeles",
                    "web_order_line_item_id": "1000000052368183",
                    "is_trial_period": "false",
                    "is_in_intro_offer_period": "false"
                }
            ]
        },
        "latest_receipt_info": [
            {
                "quantity": "1",
                "product_id": "2003",
                "transaction_id": "1000000662646469",
                "original_transaction_id": "1000000662646469",
                "purchase_date": "2020-05-10 12:27:47 Etc/GMT",
                "purchase_date_ms": "1589113667000",
                "purchase_date_pst": "2020-05-10 05:27:47 America/Los_Angeles",
                "original_purchase_date": "2020-05-10 12:27:48 Etc/GMT",
                "original_purchase_date_ms": "1589113668000",
                "original_purchase_date_pst": "2020-05-10 05:27:48 America/Los_Angeles",
                "expires_date": "2020-05-10 13:27:47 Etc/GMT",
                "expires_date_ms": "1589117267000",
                "expires_date_pst": "2020-05-10 06:27:47 America/Los_Angeles",
                "web_order_line_item_id": "1000000052368183",
                "is_trial_period": "false",
                "is_in_intro_offer_period": "false",
                "subscription_group_identifier": "20633812"
            }
        ],
        "latest_receipt": "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",
        "pending_renewal_info": [
            {
                "auto_renew_product_id": "2003",
                "original_transaction_id": "1000000662646469",
                "product_id": "2003",
                "auto_renew_status": "1"
            }
        ]
    },
    "time": 1589113847
}';
    }


    public function appleBackData()
	{
		/*array(6) {
		["environment"] => string(10) "Production"
		["receipt"] => array(18) {
			["receipt_type"] => string(10) "Production"
			["adam_id"] => int(1484898944)
			["app_item_id"] => int(1484898944)
			["bundle_id"] => string(24) "com.duluduludala.btvideo"
			["application_version"] => string(1) "6"
			["download_id"] => int(72080018155698)
			["version_external_identifier"] => int(840064035)
			["receipt_creation_date"] => string(27) "2021-01-25 16:30:32 Etc/GMT"
			["receipt_creation_date_ms"] => string(13) "1611592232000"
			["receipt_creation_date_pst"] => string(39) "2021-01-25 08:30:32 America/Los_Angeles"
			["request_date"] => string(27) "2021-01-26 03:00:53 Etc/GMT"
			["request_date_ms"] => string(13) "1611630053252"
			["request_date_pst"] => string(39) "2021-01-25 19:00:53 America/Los_Angeles"
			["original_purchase_date"] => string(27) "2020-11-04 04:21:31 Etc/GMT"
			["original_purchase_date_ms"] => string(13) "1604463691000"
			["original_purchase_date_pst"] => string(39) "2020-11-03 20:21:31 America/Los_Angeles"
			["original_application_version"] => string(1) "2"
			["in_app"] => array(4) {
				[0] => array(12) {
					["quantity"] => string(1) "1"
					["product_id"] => string(4) "3002"
					["transaction_id"] => string(15) "320000823953545"
					["original_transaction_id"] => string(15) "320000823953545"
					["purchase_date"] => string(27) "2021-01-25 16:30:32 Etc/GMT"
					["purchase_date_ms"] => string(13) "1611592232000"
					["purchase_date_pst"] => string(39) "2021-01-25 08:30:32 America/Los_Angeles"
					["original_purchase_date"] => string(27) "2021-01-25 16:30:32 Etc/GMT"
					["original_purchase_date_ms"] => string(13) "1611592232000"
					["original_purchase_date_pst"] => string(39) "2021-01-25 08:30:32 America/Los_Angeles"
					["is_trial_period"] => string(5) "false"
					["in_app_ownership_type"] => string(9) "PURCHASED"
      }
      [1] => array(17) {
					["quantity"] => string(1) "1"
					["product_id"] => string(4) "2001"
					["transaction_id"] => string(15) "320000776214684"
					["original_transaction_id"] => string(15) "320000776214684"
					["purchase_date"] => string(27) "2020-11-04 04:31:28 Etc/GMT"
					["purchase_date_ms"] => string(13) "1604464288000"
					["purchase_date_pst"] => string(39) "2020-11-03 20:31:28 America/Los_Angeles"
					["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
					["original_purchase_date_ms"] => string(13) "1604464291000"
					["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
					["expires_date"] => string(27) "2020-12-04 04:31:28 Etc/GMT"
					["expires_date_ms"] => string(13) "1607056288000"
					["expires_date_pst"] => string(39) "2020-12-03 20:31:28 America/Los_Angeles"
					["web_order_line_item_id"] => string(15) "320000305342466"
					["is_trial_period"] => string(5) "false"
					["is_in_intro_offer_period"] => string(5) "false"
					["in_app_ownership_type"] => string(9) "PURCHASED"
      }
      [2] => array(17) {
					["quantity"] => string(1) "1"
					["product_id"] => string(4) "2001"
					["transaction_id"] => string(15) "320000791458617"
					["original_transaction_id"] => string(15) "320000776214684"
					["purchase_date"] => string(27) "2020-12-04 04:31:28 Etc/GMT"
					["purchase_date_ms"] => string(13) "1607056288000"
					["purchase_date_pst"] => string(39) "2020-12-03 20:31:28 America/Los_Angeles"
					["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
					["original_purchase_date_ms"] => string(13) "1604464291000"
					["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
					["expires_date"] => string(27) "2021-01-04 04:31:28 Etc/GMT"
					["expires_date_ms"] => string(13) "1609734688000"
					["expires_date_pst"] => string(39) "2021-01-03 20:31:28 America/Los_Angeles"
					["web_order_line_item_id"] => string(15) "320000305342467"
					["is_trial_period"] => string(5) "false"
					["is_in_intro_offer_period"] => string(5) "false"
					["in_app_ownership_type"] => string(9) "PURCHASED"
      }
      [3] => array(17) {
					["quantity"] => string(1) "1"
					["product_id"] => string(4) "2001"
					["transaction_id"] => string(15) "320000809985451"
					["original_transaction_id"] => string(15) "320000776214684"
					["purchase_date"] => string(27) "2021-01-04 04:31:28 Etc/GMT"
					["purchase_date_ms"] => string(13) "1609734688000"
					["purchase_date_pst"] => string(39) "2021-01-03 20:31:28 America/Los_Angeles"
					["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
					["original_purchase_date_ms"] => string(13) "1604464291000"
					["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
					["expires_date"] => string(27) "2021-02-04 04:31:28 Etc/GMT"
					["expires_date_ms"] => string(13) "1612413088000"
					["expires_date_pst"] => string(39) "2021-02-03 20:31:28 America/Los_Angeles"
					["web_order_line_item_id"] => string(15) "320000314493781"
					["is_trial_period"] => string(5) "false"
					["is_in_intro_offer_period"] => string(5) "false"
					["in_app_ownership_type"] => string(9) "PURCHASED"
      }
    }
  }
  ["latest_receipt_info"] => array(3) {
			[0] => array(18) {
				["quantity"] => string(1) "1"
				["product_id"] => string(4) "2001"
				["transaction_id"] => string(15) "320000809985451"
				["original_transaction_id"] => string(15) "320000776214684"
				["purchase_date"] => string(27) "2021-01-04 04:31:28 Etc/GMT"
				["purchase_date_ms"] => string(13) "1609734688000"
				["purchase_date_pst"] => string(39) "2021-01-03 20:31:28 America/Los_Angeles"
				["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
				["original_purchase_date_ms"] => string(13) "1604464291000"
				["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
				["expires_date"] => string(27) "2021-02-04 04:31:28 Etc/GMT"
				["expires_date_ms"] => string(13) "1612413088000"
				["expires_date_pst"] => string(39) "2021-02-03 20:31:28 America/Los_Angeles"
				["web_order_line_item_id"] => string(15) "320000314493781"
				["is_trial_period"] => string(5) "false"
				["is_in_intro_offer_period"] => string(5) "false"
				["in_app_ownership_type"] => string(9) "PURCHASED"
				["subscription_group_identifier"] => string(8) "20633812"
    }
    [1] => array(18) {
				["quantity"] => string(1) "1"
				["product_id"] => string(4) "2001"
				["transaction_id"] => string(15) "320000791458617"
				["original_transaction_id"] => string(15) "320000776214684"
				["purchase_date"] => string(27) "2020-12-04 04:31:28 Etc/GMT"
				["purchase_date_ms"] => string(13) "1607056288000"
				["purchase_date_pst"] => string(39) "2020-12-03 20:31:28 America/Los_Angeles"
				["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
				["original_purchase_date_ms"] => string(13) "1604464291000"
				["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
				["expires_date"] => string(27) "2021-01-04 04:31:28 Etc/GMT"
				["expires_date_ms"] => string(13) "1609734688000"
				["expires_date_pst"] => string(39) "2021-01-03 20:31:28 America/Los_Angeles"
				["web_order_line_item_id"] => string(15) "320000305342467"
				["is_trial_period"] => string(5) "false"
				["is_in_intro_offer_period"] => string(5) "false"
				["in_app_ownership_type"] => string(9) "PURCHASED"
				["subscription_group_identifier"] => string(8) "20633812"
    }
    [2] => array(18) {
				["quantity"] => string(1) "1"
				["product_id"] => string(4) "2001"
				["transaction_id"] => string(15) "320000776214684"
				["original_transaction_id"] => string(15) "320000776214684"
				["purchase_date"] => string(27) "2020-11-04 04:31:28 Etc/GMT"
				["purchase_date_ms"] => string(13) "1604464288000"
				["purchase_date_pst"] => string(39) "2020-11-03 20:31:28 America/Los_Angeles"
				["original_purchase_date"] => string(27) "2020-11-04 04:31:31 Etc/GMT"
				["original_purchase_date_ms"] => string(13) "1604464291000"
				["original_purchase_date_pst"] => string(39) "2020-11-03 20:31:31 America/Los_Angeles"
				["expires_date"] => string(27) "2020-12-04 04:31:28 Etc/GMT"
				["expires_date_ms"] => string(13) "1607056288000"
				["expires_date_pst"] => string(39) "2020-12-03 20:31:28 America/Los_Angeles"
				["web_order_line_item_id"] => string(15) "320000305342466"
				["is_trial_period"] => string(5) "false"
				["is_in_intro_offer_period"] => string(5) "false"
				["in_app_ownership_type"] => string(9) "PURCHASED"
				["subscription_group_identifier"] => string(8) "20633812"
    }
  }
  ["latest_receipt"] => string(7916) "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"
		["pending_renewal_info"] => array(1) {
			[0] => array(4) {
				["auto_renew_product_id"] => string(4) "2004"
				["original_transaction_id"] => string(15) "320000776214684"
				["product_id"] => string(4) "2001"
				["auto_renew_status"] => string(1) "1"
    }
  }
  ["status"] => int(0)
}*/

	}

    /** 自身业务处理
     * @param $appData
     * @return string
     */
    public function appleAppData($appData)
    {
        $inAppData = $appData['in_app'];
        //产品配置,对应ios申请的product_id eg : yw_6 支付6元
        $productB = ['yw_6'];
        //多物品信息
        foreach ($inAppData as $product) {
            //订单重复验证
            $appleData = $product->check('自身业务去重');
            if ($appleData) {
                continue;
                //return $this->code400('交易单号重复,请不要重复验证!id:'.$transactionId) ;
            }
            //产品验证
            if (isset($productB[$product['product_id']])) {
                $productId = $product['product_id'];
                $money = $productB[$productId];
                if (!$money) {
                    return $this->code400('没有找到对应产品的金额,ID:' . $product['product_id']);
                }
                //业务逻辑处理
                //加余额,记录资金日志之类的操作
                $product['add_balance'] = true;
            }
            //环境
            $product['is_sandbox'] = $this->sandbox;
            //数据
            $product['receipt_data'] = '$receipt';
            //时间
            $product['time'] = date('YmdHis');
            //返回码
            $product['err_no'] = '200';
            //save $product 保存数据
        }
        //根据自身需求返回数据
        $returnData = [];
        return $this->code200($returnData);
    }
}