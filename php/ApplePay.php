<?php
class ApplePay
{
    /** 环境配置(建议提成配置)
     * @var bool
     */
    private $sandbox = false ;

    /** result as json
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return string
     */
    private function jsonRet(int $code=200, string $msg='', array $data = [])
    {
        $result = [
            'code' => $code ,
            'message' => $msg ,
            'data' => $data ,
        ] ;
        return json_encode($result,true);
    }

    /** code400
     * @param string $msg
     * @return string
     */
    private function code400( $msg = '参数错误!' )
    {
        return $this->jsonRet(400,$msg);
    }

    /** code200
     * @param string $msg
     * @return string
     */
    private function code200( $msg = '操作成功!')
    {
        return $this->jsonRet(200,$msg);
    }

    /** log
     * @param $info
     */
    private function log($info)
    {
        //log info
    }

    /** ios apple 支付验证
     * @return string
     */
    public function apple()
    {
        //苹果支付认证的凭证(base64后的数据)
        $receipt = $_POST('receipt') ;
        if(empty($receipt)) {
            return $this->code400() ;
        }
        //环境配置
        if ($this->sandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';//沙箱地址
        } else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';//真实运营地址
        }
        //数据组装
        //$receipt ='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';
        $postData = json_encode(
            array('receipt-data' => $receipt)
            ,JSON_UNESCAPED_SLASHES);
        //日志记录
        $this->log($postData);
        //curl操作
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        curl_close($ch);
        if ($errno != 0) {
            return $this->code400('curl请求有错误!') ;
        }else{
            $data = json_decode($response, true);
            if (!is_array($data)) {
                return $this->code400('数据错误!') ;
            }
            //判断购买是否成功
            if (!isset($data['status']) || $data['status'] != 0) {
                return $this->code400('无效的iOS支付数据!') ;
            }
            //无效的bundle_id
            if(!in_array($data['receipt']['bundle_id'],['ios申请的bundle_id类似于支付的app_id'])) {
                return $this->code400('无效的bundle_id:'.$data['receipt']['bundle_id']) ;
            }
            //多物品购买时
            // in_app为多个(坑)
            // ios一次支付可能返回多个,可能是上次成功后没有及时返回,这次成功后会把上次或上上次成功的返回
            if(!empty($inAppData = $data['receipt']['in_app'])) {
                //产品配置,对应ios申请的product_id eg : yw_6 支付6元
                $productB = ['yw_6'];
                //多物品信息
                foreach ($inAppData as $product) {
                    //订单重复验证
                    $appleData = $product->check('自身业务去重');
                    if($appleData) {
                        continue ;
                        //return $this->code400('交易单号重复,请不要重复验证!id:'.$transactionId) ;
                    }
                    if(isset($productB[$product['product_id']])) {
                        $productId = $product['product_id'];
                        $money = $productB[$productId] ;
                        if(!$money) {
                            return $this->code400('没有找到对应产品的金额,ID:'.$product['product_id']) ;
                        }
                        //业务逻辑处理
                        //加余额,记录资金日志之类的操作
                        $product['add_balance'] = true ;
                    }
                    //环境
                    $product['is_sandbox']   = $this->sandbox ;
                    //数据
                    $product['receipt_data']  = $receipt ;
                    //时间
                    $product['time']         = date('YmdHis') ;
                    //返回码
                    $product['err_no']       = '200' ;
                    //save $product 保存数据
                }
            }
            //根据自身需求返回数据
            $returnData = [] ;
            return $this->code200($returnData) ;
        }
    }
}
