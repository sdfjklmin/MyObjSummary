window.onload = function (ev) {
    var pop_u_name = document.getElementById('pop_u_name');
    var pop_u_name_msg = document.getElementById('pop_u_name_msg');

    var pop_u_pwd = document.getElementById('pop_u_pwd') ;
    var pop_u_pwd_msg = document.getElementById('pop_u_pwd_msg');

    var pop_sub = document.getElementById('pop_sub');

    pop_sub.onclick = function (ev2) {
        var not = false ;
        if(pop_u_name.value ==='') {
            pop_u_name_msg.innerHTML = 'please into you name';
            not = true ;
        }else {
            pop_u_name_msg.innerHTML = '' ;
        }
        if(pop_u_pwd.value ==='') {
            pop_u_pwd_msg.innerHTML = 'please into you pwd';
            not = true ;
        }else {
            pop_u_pwd_msg.innerHTML = '' ;
        }
        if(not) return ;
        var f = document.getElementById("pop_form");
        f.action = "http://localhost:20002/";
        f.method = "POST";
        f.submit();
    }
}
