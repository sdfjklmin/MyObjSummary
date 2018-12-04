//表单构建
var MForm = function () {
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