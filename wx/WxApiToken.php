<?php


namespace wx;


class WxApiToken extends WxApi
{
    protected $url = 'https://api.weixin.qq.com/cgi-bin/token';

    protected $params = [];

    protected $method = 'GET';

    private   $grant_type = 'client_credential';

    public function __construct()
    {
        $this->params = [
            'grant_type' => $this->grant_type,
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
        ];
    }

    public function setAppId($appId)
    {
        $this->params['appid'] = $appId;
        return $this;
    }

    public function setAppSecret($secret)
    {
        $this->params['secret'] = $secret;
        return $this;
    }

    public function analyzeRet()
    {
        if($this->result) {

        }
    }
}