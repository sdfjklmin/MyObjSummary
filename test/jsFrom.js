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