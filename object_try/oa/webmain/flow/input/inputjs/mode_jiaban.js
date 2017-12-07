function initbodys(){
	$(form('stime')).blur(function(){
		changetotal();
	});
	$(form('etime')).blur(function(){
		changetotal();
	});
}
function changesubmit(d){
	if(d.etime<=d.stime)return '截止时间必须大于开始时间';
	if(d.stime.substr(0,10)!=d.etime.substr(0,10)){
		return '不允许跨日申请';
	}
	var st=parseFloat(d.totals);
	if(st<=0)return '加班时间必须大于0';
}

function changetotal(){
	var st = form('stime').value,
		et = form('etime').value;
	if(isempt(st)||isempt(et)){
		form('totals').value='0';
		return;
	}
	if(st.substr(0,10)!=et.substr(0,10)){
		js.setmsg('不允许跨日申请');
		return;
	}
	js.ajax(geturlact('total'),{stime:st,etime:et}, function(da){
		var a= js.decode(da);
		form('totals').value=a[0];
		js.setmsg(a[1]);
	},'post');
}