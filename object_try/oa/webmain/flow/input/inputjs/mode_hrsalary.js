var initshujubs=false;
function initsssss(){
	if(ismobile==1){
		$('#div_punish').before('<div><b>减少部分(-)</b></div>');
	}
}
initsssss();
function initbodys(){
	var o = $('input[type=number]');
	o.attr('minValue','0');
	$('#AltS').before('<input type="button" style="background:#888888" onclick="return initshuju()" value="初始数据" class="webbtn">&nbsp; &nbsp;');
	o.blur(function(){
		jisuantongzi();
	});
	$(form('uname')).blur(function(){
		
		chuangeusername();
	});
}
function initshuju(){
	var xuid=form('xuid').value,month=form('month').value;
	if(xuid==''){
		js.msg('msg','请选择人员');
		return;
	}
	if(month==''){
		js.msg('msg','请选择月份');
		return;
	}
	js.msg('wait','初始化中...');
	js.setmsg();
	initshujubs = false;
	js.ajax(geturlact('initdatas'),{'xuid':xuid,'month':month},function(adds){
		js.msg('success','初始化完成，请认真核对');
		for(var i in adds){
			if(form(i))form(i).value=adds[i];
		}
		jisuantongzi();
		initshujubs=true;
	},'get,json');
}
function changesubmitbefore(){
	jisuantongzi();
}
function changesubmit(){
	if(mid=='0' && !initshujubs){
		return '请先初始数据';
	}
}
function jisuantongzi(){
	var j=0,i,len=arr.length,a1,j1,isa=true;
	for(i=0;i<len;i++){
		a1=arr[i];
		if(a1.fieldstype=='number' && a1.islu=='1'){
			if(form(a1.fields)){
				j1=parseFloat(form(a1.fields).value);
				if(a1.fields=='punish')isa=false;
				if(!isa)j1=0-j1;
				j=j+j1;
			}
		}
	}
	form('money').value=j;
}
function chuangeusername(){
	var xuid=form('xuid').value;
	initshujubs=false;
	if(xuid!='')js.ajax(geturlact('changeuname'),{'xuid':xuid},function(a){
		if(a){
			form('udeptname').value=a.deptname;
			form('ranking').value=a.ranking;
		}
	},'get,json');
}