<?php
namespace bookLog\buildApis;
/**
 * @remark 输出数据
 * 1.介绍
 *      输出数据
 * 2.直接法
 *      每个开发人员要做的第一件事就是使用他们最喜欢的
 *      ORM(对象关系映射)、ODM、DataMapper或Query Builder，调出一个查询，然后将结果直接导入输出。
 *          性能(获取所有数据时,数据过大),
 *          显示(格式都为json),
 *          安全性(非必要字段),
 *          稳定性(v1|v2|v3,不同版本号)
 * 3.分形变换
 *      简单的API数据可以用json_encode,复杂多变的数据json_encode输出类型可能改变,
 *      提供一个单独的API输出方法或者类
 * 4.隐藏模式更新
 *   'website' => $data->website  update  'website' => $data->url
 * 5.输出错误
 *   API: 400 ,404 ,403
 * 6.测试输出
 */

/** API分形变换
 * Class Chapter06API
 * @package bookLog\buildApis
 */
class Chapter06API
{
    const ERROR_ARGS = 400 ;
    const ERROR_FORBIDDEN = 403 ;
    const ERROR_NOT_FIND = 404 ;
    /**默认状态码
     * @var int
     */
    protected $statusCode = 200 ;

    /**默认操作提示
     * @var string | array(多个提示消息)
     */
    protected $statusMsg = '请求成功!';

    /**获取状态码
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode ;
    }

    /** 手动改变状态码
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode ;
        return $this ;

    }

    /** 手动改变提示消息
     * @param $statusMsg
     * @param $tipCode
     */
    public function setStatusMsg($statusMsg,$tipCode)
    {
        if($tipCode) {
            if(is_string($this->statusMsg)) $this->statusMsg = [] ;
            $this->statusMsg[(string)$tipCode] = $statusMsg ;
        }else{
            $this->statusMsg = $statusMsg ;
        }
    }

    /** json响应格式
     * @param $data
     * @return string
     */
    protected function respondWithJson($data)
    {
        $data = [
            'code' => $this->statusCode ,
            'data' => $data ,
            'msg'  => $this->statusMsg ,
        ] ;
        return json_encode($data,true);
    }

    /** 单数据响应
     * @param $data array 数据
     * @param $callBack callable 子类处理数据的回调
     * @return string
     */
    public function respondWithItem($data,$callBack)
    {
        $data = $this->$callBack($data);
        return $this->respondWithJson($data);

    }

    /** 多数据响应
     * @param $data
     * @param $callBack
     */
    public function respondWithCollection($data,$callBack)
    {
        //TODO
    }

    /**
     * @remark common code
     */

    /** 403
     * @param string $msg
     * @return string
     */
    public function errorForbidden($msg = 'Forbidden !')
    {
        $this->statusMsg = $msg ;
        return $this->setStatusCode(self::ERROR_FORBIDDEN)->respondWithJson([]);
    }

    /** 404
     * @param string $msg
     * @return string
     */
    public function errorNotFind($msg = 'Not Find !')
    {
        $this->statusMsg = $msg ;
        return $this->setStatusCode(self::ERROR_NOT_FIND)->respondWithJson([]);
    }

    /** 400
     * @param string $msg
     * @return string
     */
    public function errorWrongArgs($msg = 'Wrong Args !')
    {
        $this->statusMsg = $msg ;
        return $this->setStatusCode(self::ERROR_ARGS)->respondWithJson([]);
    }
}
/**
 * Class Chapter06
 * @package bookLog\buildApis
 */
class Chapter06 extends Chapter06API
{

    private static function findOne($id)
    {
        return ['name'=>'outputting','id'=>$id,'age'=>18,'money'=>12.25];
    }

    /** orm outputting
     * @return string
     */
    public function outputtingData()
    {
        $data['data'] = self::findOne('put');
        return json_encode($data,true);
    }

    /** 数据封送|序列化
     * @link https://github.com/thephpleague/fractal/stargazers fractal 数据视图层
     * @param $data
     * @return array
     */
    private static function transferDataToJson($data)
    {
        return [
            'name' => $data['name'] ,
            'id' => $data['id'] ,
            'age' => (int)$data['age'] ,
            'money' => (double)$data['money'] ,
        ] ;
    }

    public function outputtingData2()
    {
        $user = self::findOne('put');
        $data['data'] = self::transferDataToJson($user) ;
        return json_encode($data,true) ;
    }

    public function outputtingData3()
    {
        //return $this->errorForbidden() ;
        $user = self::findOne('test');
        if($user)    {
            $this->statusCode = 500 ;
            $this->statusMsg = '无效的请求!' ;
            $this->setStatusMsg('错误','ERR_1');
            $this->setStatusMsg('错误了','ERR_2');
            return $this->respondWithJson([]);
        }
        $data = $this->respondWithItem($user,'outputtingDataCom');
        return $data ;
    }

    public function outputtingDataCom($data)
    {
        return [
            'name' => $data['name'] ,
            'id' => $data['id'] ,
            'age' => (int)$data['age'] ,
            'money' => (double)$data['money'] ,
        ] ;
    }

    public function test(...$number)
    {
        dd($number);
    }
}