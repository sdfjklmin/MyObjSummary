function initbodys(){
	$(form('startdt')).blur(function(){
		changetotal();
	});
	$(form('enddt')).blur(function(){
		changetotal();
	});
}
function changesubmit(d){
	if(d.enddt<=d.startdt)return '截止时间必须大于开始时间';
	if(d.enddt.substr(0,10)!=d.startdt.substr(0,10)){
		return '不允许跨天申请';
	}
}

function changetotal(){
	var st = form('startdt').value,
		et = form('enddt').value;
	if(st.substr(0,10)!=et.substr(0,10)){
		js.setmsg('不允许跨天申请');
		return;
	}
	js.setmsg('');
}