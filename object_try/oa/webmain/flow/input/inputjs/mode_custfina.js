function initbodys(){
	$(form('custid')).change(function(){
		var val = this.value,txt='';
		if(val!=''){
			txt = this.options[this.selectedIndex].text;
		}
		form('custname').value=txt;
		form('htid').value = '';
	});
	
	$(form('htid')).change(function(){
		var val = this.value,txt='';
		salechange(val);
	});
}
function salechange(v){
	if(v==''){
		form('custid').value='';
		return;
	}
	js.ajax(geturlact('ractchange'),{ractid:v},function(a){
		form('custid').value=a.custid;
		form('custname').value=a.custname;
		form('money').value=a.money;
		form('type').value=a.type;
		form('htnum').value=a.num;
		form('dt').value=a.signdt;
	},'get,json');
}