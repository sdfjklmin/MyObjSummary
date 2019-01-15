<?php
namespace bookLog\buildApis;
/**
 * @remark 身份验证
 * 1.介绍
 *      使用带有cookie、Memcache、Redis、Mongo或某些SQL平台等数据存储的会话被广泛接受为标准行为。
 *
 * 2.什么时候验证
 *      只读api(不需要认证的),一般情况下提供一个账号给调用者使用,方便控制恶意请求,恶意数据,
 *      内部API(运行在内部环境,或者局域网中的),可以跳过身份认证
 *
 * 3.不同的身份认证方法
 *      SSL(Secure Sockets Layer 安全套接层),及其继任者传输层安全（Transport Layer Security，TLS）是为网络通信提供安全及数据完整性的一种安全协议
 *      HTTP Basic :
 *              HTTP基本身份验证(BA)实现是对web资源实施访问控制的最简单技术，因为它不需要cookie、会话标识符和登录页面。
 *              相反，HTTP基本身份验证使用静态的标准HTTP报头，这意味着不需要预先握手。 ——来源:维基百科
 *              (易实现,易理解,工作在浏览器和任何其他HTTP客户端)(HTTP上非常不安全,HTTPS相当不安全,密码由浏览器保存不安全)
 *      摘要身份认证: 摘要是一种类似于Basic的身份验证方法，但旨在改进安全性问题。
 *      OAuth 1.0 |1.0a (OAuth Token and OAuth Token Secret) :
 *          无法轻松访问浏览器的移动和桌面应用程序设计
            POST/moments/1/giftHTTP/1.1
            Host: api.example.com
            Authorization: OAuthrealm="http://sp.example.com/",
            oauth_consumer_key="0685bd9184jfhq22",
            oauth_token="ad180jjd733klru7",
            oauth_signature_method="HMAC-SHA1",
            oauth_signature="wOJIO9A2W5mFwDgiDvZbTSMK%2FPY%3D",
            oauth_timestamp="137131200",
            oauth_nonce="4572616e48616d6d65724c61686176",
            oauth_version="1.0"11Content-Type: application/json1213
            {  "user_id": 2 }
 *      OAuth 2.0 : 删除了秘密令牌,只需获得一个访问令牌。
            POST /moments/1/gift HTTP/1.1
            Host: api.example.com
            Authorization: Bearer vr5HmMkzlxKE70W1y4Mi
            Content-Type: application/json
            { "user_id" : 2 }
 *          无论何时，您都应该尝试使用授权头来发送令牌。
 *      生命令牌(任意一段时间后过期)
 *      Grant Types
 *      其他认证:
 *          OpenId : https://openid.net/
 *          Hawk   : https://github.com/hueniverse/hawk
 *          Oz     : https://github.com/hueniverse/oz
 *
 * 4.实现OAuth 2.0服务器
 *    http://oauth2.thephpleague.com/installation/
 *    http://bshaffer.github.io/oauth2-server-php-docs/
 *
 * 5.其他服务器OAuth 2.0 (Ruby,python,rack,...)
 *
 * 6.理解OAuth 2.0 授权类型
 *      授权代码 : (多站点共享登录) => (规范)    https://tools.ietf.org/html/rfc6749#section-4.1
 *      刷新令牌 : 一直使用相同的令牌可能会被破解 https://tools.ietf.org/html/rfc6749#section-6
 *      客户端凭证 : 我是一个应用程序,你知道我是一个应用程序,
 *                  因为这是我的client_id和client_secret值 . 认证过后,请让我通行!
 *                  http://tools.ietf.org/html/rfc6749
 *      密码(用户凭证) : 用户凭据可能是为用户获取访问令牌的最简单方法。
 *                      这就跳过了“身份验证代码”提供的整个重定向流，以及随之而来的用户心态平和，但确实提供了简单性。
 *                      https://tools.ietf.org/html/rfc6749#section-4.3
 *      自定义授权类型 :  access_token
 *                      eg : 登录
 *                          获取用户的数据
 *                          找出他们是否是一个系统用户，如果不是，创建一个系统用户记录
 *                          创建访问令牌、刷新令牌等，以给予该用户访问权
 */
/**
 * Class Chapter09
 * @package bookLog\buildApis
 */
class Chapter09
{
    public function HttpBasic()
    {
        //$data = "eyJ2ZXJzaW9uIjoiNC4xLjAiLCJjb2x1bW5zIjpbImxvZyIsImJhY2t0cmFjZSIsInR5cGUiXSwicm93cyI6W1tbMjNdLCJEOlxcTWluXFxJbnN0YWxsXFxwaHBzdHVkeVxcUEhQVHV0b3JpYWxcXFdXV1xcU2VsZlxcTXlPYmpTdW1tYXJ5XFx0cnkucGhwIDogNSIsIiJdLFtbeyJET0NVTUVOVF9ST09UIjoiRDpcXE1pblxcSW5zdGFsbFxccGhwc3R1ZHlcXFBIUFR1dG9yaWFsXFxXV1dcXFNlbGZcXE15T2JqU3VtbWFyeSIsIlJFTU9URV9BRERSIjoiOjoxIiwiUkVNT1RFX1BPUlQiOiI2MTAxOSIsIlNFUlZFUl9TT0ZUV0FSRSI6IlBIUCA3LjEuMTMgRGV2ZWxvcG1lbnQgU2VydmVyIiwiU0VSVkVSX1BST1RPQ09MIjoiSFRUUFwvMS4xIiwiU0VSVkVSX05BTUUiOiJsb2NhbGhvc3QiLCJTRVJWRVJfUE9SVCI6IjIwMDAyIiwiUkVRVUVTVF9VUkkiOiJcL3RyeS5waHAiLCJSRVFVRVNUX01FVEhPRCI6IkdFVCIsIlNDUklQVF9OQU1FIjoiXC90cnkucGhwIiwiU0NSSVBUX0ZJTEVOQU1FIjoiRDpcXE1pblxcSW5zdGFsbFxccGhwc3R1ZHlcXFBIUFR1dG9yaWFsXFxXV1dcXFNlbGZcXE15T2JqU3VtbWFyeVxcdHJ5LnBocCIsIlBIUF9TRUxGIjoiXC90cnkucGhwIiwiSFRUUF9IT1NUIjoibG9jYWxob3N0OjIwMDAyIiwiSFRUUF9DT05ORUNUSU9OIjoia2VlcC1hbGl2ZSIsIkhUVFBfUFJBR01BIjoibm8tY2FjaGUiLCJIVFRQX0NBQ0hFX0NPTlRST0wiOiJuby1jYWNoZSIsIkhUVFBfVVBHUkFERV9JTlNFQ1VSRV9SRVFVRVNUUyI6IjEiLCJIVFRQX1VTRVJfQUdFTlQiOiJNb3ppbGxhXC81LjAgKFdpbmRvd3MgTlQgMTAuMDsgV09XNjQpIEFwcGxlV2ViS2l0XC81MzcuMzYgKEtIVE1MLCBsaWtlIEdlY2tvKSBDaHJvbWVcLzY2LjAuMzM1OS4xMzkgU2FmYXJpXC81MzcuMzYiLCJIVFRQX0FDQ0VQVCI6InRleHRcL2h0bWwsYXBwbGljYXRpb25cL3hodG1sK3htbCxhcHBsaWNhdGlvblwveG1sO3E9MC45LGltYWdlXC93ZWJwLGltYWdlXC9hcG5nLCpcLyo7cT0wLjgiLCJIVFRQX0FDQ0VQVF9FTkNPRElORyI6Imd6aXAsIGRlZmxhdGUsIGJyIiwiSFRUUF9BQ0NFUFRfTEFOR1VBR0UiOiJ6aC1DTix6aDtxPTAuOSIsIlJFUVVFU1RfVElNRV9GTE9BVCI6MTU0NzE3MzM5MS4xNDQyMzgsIlJFUVVFU1RfVElNRSI6MTU0NzE3MzM5MX1dLCJEOlxcTWluXFxJbnN0YWxsXFxwaHBzdHVkeVxcUEhQVHV0b3JpYWxcXFdXV1xcU2VsZlxcTXlPYmpTdW1tYXJ5XFx0cnkucGhwIDogNiIsImluZm8iXV0sInJlcXVlc3RfdXJpIjoiXC90cnkucGhwIn0=";
        //$a = json_decode(utf8_decode(base64_decode($data)));
        //dd($a);
        //base64_encode(utf8_encode(json_encode($data)));

       //dd(base64_encode('test:111'));
       //request header
        /*POST /try.php HTTP/1.1
        Host: localhost:20002
        Authorization: Basic dGVzdDoxMTE=  ( Basic认证方式[Digest,AWS,...]  dGVzdDoxMTE=认证auth )
        Cache-Control: no-cache
        Postman-Token: 15e7429a-5d84-1246-6d2a-f60bc060c01b*/
       $basic = $_SERVER['HTTP_AUTHORIZATION'] ?? '' ; //  Basic dGVzdDoxMTE=
       $basicArr = explode(" ",$basic) ; // ['Basic','dGVzdDoxMTE=']
       switch ($basicArr[0]){
           case 'Basic':
               return  base64_decode($basicArr[1]); // test:111
           default :
               return $basic;
       }
    }
}