var oldpass='',initlogo='images/logo.png',olduser;
function initbody(){
	
	form('adminuser').focus();
	oldpass	= form('adminpass').value;
	olduser	= form('adminuser').value;
	if(form('adminuser').value!=''){
		form('adminpass').focus();
	}
	
	resizewh();
	$(window).resize(resizewh);
	var sf = js.getoption('loginface');
	if(sf)get('imglogo').src=sf;
	$(form('adminuser')).change(function(){
		changeuserface(this.value);
	});
	yunanimate();
	
}
function yunanimate(){
	var whe=winWb();
	//$('#yun1').animate({'left':''+(whe)+'px'},10000);
	//$('#yun2').animate({'left':''+(whe)+'px'},20000);
}
function resizewh(){
	var h = ($(document).height()-510)*0.5;
	$('#topheih').css('height',''+h+'px');
}
function changeuserface(v){
	var sf = js.getoption('loginface');
	if(!sf)return;
	if(v==''||v!=olduser){
		get('imglogo').src=initlogo;
	}else{
		get('imglogo').src=sf;
	}
}
function loginsubmit(){
	if(js.bool)return false;
	var user = form('adminuser').value;
	var pass = form('adminpass').value;
	var device= js.cookie('deviceid');
	if(device=='')device=js.now('time');
	js.savecookie('deviceid', device, 365);
	if(user==''){
		js.setmsg('帐号不能为空','red');
		form('adminuser').focus();
		return false;
	}
	if(pass==''){
		js.setmsg('密码不能为空','red');
		form('adminpass').focus();
		return false;
	}
	js.setmsg('登录中...','blue');
	form('button').disabled=true;
	var data	= js.getformdata();
	var url		= js.getajaxurl('check','login');
	data.jmpass	= 'false';
	data.device = device;
	data.adminuser = jm.base64encode(user);
	data.adminpass = jm.base64encode(pass);
	if(oldpass==pass)data.jmpass= 'true';
	js.bool		= true;
	js.ajax(url,data,function(a){
		if(a.success){
			var ltype=js.request('ltype');
			if(ltype=='1'){
				history.back();
			}else{
				js.setmsg('登录成功,跳转中..','green');
				location.href='?m=index';
			}
			get('imglogo').src=a.face;
			js.setoption('loginface', a.face);
		}else{
			js.setmsg(a.msg,'red');
			form('button').disabled=false;
			js.bool	= false;
		}
	},'post,json');
}