    /*表单构建*/
    var f = document.createElement("form");
    document.body.appendChild(f);
    var hidField = {
        'par_value' : '23' ,
        'money' : '23' ,
        'type' : '3223'
    } ;
    for( var i in hidField ) {
        var temp = document.createElement("input");
        temp.type = "hidden";
        f.appendChild(temp);
        temp.value = hidField[i];
        temp.name = i ;
    }
    /*JSON.stringify()
    JSON.parse()*/
    f.action = "/user/account/pay-money";
    f.method = "post";
    f.submit();

    /*原生header设置*/
    var xmlhttp = new XMLHttpRequest();

    //POST发送
    xmlhttp.open("POST", "/bar/steam-code/pay-money",true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //xmlhttp.setRequestHeader("token","header-token-value"); // 可以定义请求头带给后端
    var content = "appid=11111&sign=222222222"; //application/x-www-form-urlencoded
    xmlhttp.send(content) ; //内容格式根据Content-type设置的格式而定

    //GET发送
    xmlhttp.open("GET", "/bar/steam-code/pay-money?id=23",true);
    xmlhttp.send();

    xmlhttp.onreadystatechange = function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            //成功
            console.log(xmlhttp.responseText);
        }
    }