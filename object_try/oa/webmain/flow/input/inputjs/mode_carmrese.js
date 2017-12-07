function initbodys(){
	$(form('carid')).change(function(){
		var val = this.value,txt='';
		if(val!=''){
			txt = this.options[this.selectedIndex].text;
		}
		form('carnum').value=txt;
	});
}

function changesubmit(d){
	if(d.enddt<=d.startdt)return '结束时间必须大于开始时间';
}
