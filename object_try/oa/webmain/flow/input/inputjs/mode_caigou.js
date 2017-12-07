function initbodys(){
	$(form('money')).click(function(){
		//cchangtongss();
	});
	$(form('custid')).change(function(){
		var val = this.value,txt='';
		if(val!=''){
			txt = this.options[this.selectedIndex].text;
		}
		form('custname').value=txt;
	});
	addchengesss();
}
function addchengesss(){
	if(isedit==0)return;
	$("[name^='aid0_']").unbind('change').change(function(){
		changeaidtssk(this);
		cchangtongss();
	});
	$("[name^='count0_']").unbind('change').change(function(){
		cchangtongss();
	});
	$("[name^='price0_']").unbind('change').change(function(){
		cchangtongss();
	});
}
function changesubmit(){
	
}
function changesubmitbefore(){
	cchangtongss();
}
function eventaddsubrows(){
	cchangtongss();
	addchengesss();
}
function eventdelsubrows(){
	cchangtongss();
}
function cchangtongss(){
	var d=c.getsubdata(0);
	var to=0,i,len=d.length;
	for(i=0;i<len;i++){
		to=to+parseFloat(d[i].count)*parseFloat(d[i].price);
	}
	form('money').value=js.float(to)+'';
}
function changeaidtssk(o1){
	var val=o1.value;
	var nam = o1.name.replace('aid','').split('_');
	js.ajax(geturlact('getgoods'),{aid:val},function(d){
		c.setrowdata(nam[0],nam[1],d);
		cchangtongss();
	},'get,json');
}