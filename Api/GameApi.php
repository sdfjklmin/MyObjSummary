<?php

namespace MyObjSummary\Api ;
class GameApi 
{

    public $_base = null ; # 基本参数(token和一些默认参数)
    public $_url = null ;  # 请求网址
    public $_conf = null ; # 基本配置
    public function __construct($label='TT')
    {

        # 初始化参数,获取电子所需要的基本参数 TT标识可以电子提供
        $this->_conf = [
                            # 认证信息
                            'pwd' =>[
                                'app_key' =>'epa4a5d85f2e858837885dd97a77e91f7b' ,
                                'app_secret'=>'5a56f251cdae3728cac98cbbea11b168fd6a6314' ,
                            ],
                            'url'   =>'e.700chanpin.com/',      # 请求网址默认带上斜杠
                            'token' => 'v1/getToken' ,          # 获取token接口
                            'base'  =>'v1/game',                # 游戏基础列表接口
                            'into'  =>'v1/gateway' ,            # 点击进入游戏
                            'orderSync' => 'v1/getBetRecord',   # 投注记录
                            'corp_id' =>1,                      # 电子游戏对应的包网ID
                        ];
             
                  
        $this->_url  = $this->_conf['url'] ;
        #初始化base
        $this->CurlLink() ;
        #初始化web
        $data = $this->CurlCommon($this->_conf['base']) ;
        $this->_web = array_column($data,'url') ;
    }

    /**
     *  游戏列表接口
     * @author sjm
     * @date
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function baseInfo(Request $request)
   {
       $data = $this->CurlCommon($this->_conf['base']) ;
       return $this->success($data);
   }

    /**
     *  点击进入游戏
     * @author sjm
     * @date
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function playGame(Request $request)
   {
       $data = $this->CurlCommon($this->_conf['into'],
           ['redirect_uri'=>$this->_web[0],'source_user_id'=>$request->input('user_id')],true) ;
       header("Location: http://$data");
   }

    /**
     *  初始化基本参数
     * @author sjm
     * @date
     */
   private function CurlLink()
   {
       $rzStr = $this->paramsLink($this->_conf['pwd']) ;
       if(empty($rzStr)) {
           dd('没有认证参数');
       }
       $url = 'http://'.$this->_conf['url'].$this->_conf['token'].'?'.$rzStr ;
       $ch = curl_init ();
       curl_setopt ( $ch, CURLOPT_URL, $url );
       curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
       curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
       $file_contents = curl_exec ($ch);
       curl_close ($ch);
       $data = json_decode($file_contents,true) ;
       if($data['code'] != '200') {
           dd($data);
       }
       $str = $this->paramsLink($data['data']) ;
       if(!$str) {
           dd('电子基本参数错误');
       }
       $this->_base = $str ;
   }

    /**
     *  基本参数拼接
     * @author sjm
     * @date
     * @param array $data
     * @return string
     */
   private function paramsLink($data = array(),$link='&')
   {
       if(empty($data)) {
           return '';
       }
       $str = '' ;
       foreach ($data as $k => $v) {
           $str .= $link.$k.'='.$v ;
       }
       $str = ltrim($str,$link) ;
       return $str ;

   }

    /**
     * 接口请求
     * @author sjm
     * @date
     * @param $action string 请求方法
     * @param array $param array 参数
     * @param bool $retUrl bool 是否返回url
     * @param string $req string 请求类型
     * @param bool $initial bool 是否返回原始请求数据
     * @return string
     */
   public function CurlCommon($action,$params=array(),$retUrl=false,$req='',$initial=false)
   {
       $allUrl =$this->_url.$action.'?'.$this->_base ;
       if ($params) {
           $ext = $this->paramsLink($params);
           $allUrl .= '&'.$ext ;
       }
       if($retUrl == true) {
           return $allUrl ;
       }
       $ch = curl_init ();
       curl_setopt ( $ch, CURLOPT_URL, $allUrl );
       curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
       curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
       if($req) {
           curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
       }
       $file_contents = curl_exec ($ch);
       curl_close ($ch);
       $data = json_decode($file_contents,true) ;
       if($initial == true) {
           return $data ;
       }
       if($data['code'] != '200') {
           dd($data);
       }
       return $data['data'] ;
   }
}
