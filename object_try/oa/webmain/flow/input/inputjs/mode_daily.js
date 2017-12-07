function initbodys(){
	$(form('dt')).blur(function(){
		changetypes();
	});
	$(form('type')).change(function(){
		changetypes();
	});
	$(form('enddt')).blur(function(){
		changetypes();
	});
}
function changetypes(){
	var lx= form('type').value;
	if(lx==''||lx=='0'){
		form('enddt').value='';
		return;
	}
	var dt = form('dt').value;
	if(dt=='')return;
	js.ajax(geturlact('getdtstr'),{dt:dt,type:lx},function(a){
		form('dt').value= a[0];
		form('enddt').value=a[1];
	},'get,json');
}

function changesubmit(d){
	if(d.type!='0' && d.enddt=='')return '截止日期不能为空';
}