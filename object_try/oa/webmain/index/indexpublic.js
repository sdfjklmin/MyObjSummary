js.getuser = function(cans){
	var rand = js.getrand(),s='';
	var can = js.apply({windowid:'getuser_'+rand+'',title:'读取人员',idobj:false,nameobj:false,value:'',type:'deptusercheck',callback:function(){}}, cans);
	js.tanbody(can.windowid, can.title, 300, 400,{bbar:'none'});
	s+='<div style="height:350px;overflow:auto" id="view_'+rand+'"></div>';
	s+='<div style="padding:8px 10px;background:#eeeeee;line-height:30px" align="right">';
	s+='	<button type="button" class="btn btn-primary" id="ok_'+rand+'"><i class="icon-ok"></i>&nbsp;确定</button>';
	s+='&nbsp; <button type="button" onclick="js.tanclose(\''+can.windowid+'\');" class="btn btn-default" ><i class="icon-remove"></i>&nbsp;取消</button>';
	s+='</div>';
	js.tanbody(can.windowid, can.title, 300, 400,{bbar:'none',html:s});
	var ctype=can.type;
	var a = $('#view_'+rand+'').bootstree({
		storefields:'id,name',url:js.getajaxurl('deptuserdata','dept','system',{changetype:ctype}),
		params:{value:can.value},
		columns:[{
			text:'名称',dataIndex:'name',align:'left',xtype:'treecolumn',width:'99%'
		}]
	});
	$('#ok_'+rand+'').click(function(){
		var d = a.getchecked(),s1='',s2='',i,a1;
		for(i=0; i<d.length; i++){
			a1 = d[i];
			if(ctype=='dept'){
				if(a1.type=='d'){
					s1 = ','+a1.did;
					s2 = ','+a1.name;
				}
			}
			if(ctype=='deptcheck' || ctype=='checkdept'){
				if(a1.type=='d'){
					s1 += ','+a1.did;
					s2 += ','+a1.name;
				}
			}
			if(ctype=='user' || ctype=='deptuser'){
				if(a1.type=='u'){
					s1 = ','+a1.uid;
					s2 = ','+a1.name;
				}
			}
			if(ctype=='usercheck' || ctype=='checkuser'){
				if(a1.type=='u'){
					s1 += ','+a1.uid;
					s2 += ','+a1.name;
				}
			}
			if(ctype.indexOf('user')>-1 && ctype.indexOf('dept')>-1 && ctype.indexOf('check')>-1){
				s1 += ','+a1.id;
				s2 += ','+a1.name;
			}
		}
		if(s1!=''){
			s1 = s1.substr(1);
			s2 = s2.substr(1);
		}
		if(can.idobj)can.idobj.value = s1;
		if(can.nameobj){
			can.nameobj.value = s2;
			can.nameobj.focus();
		}	
		can.callback(s2,s1,d);
		js.tanclose(can.windowid);
	}).focus();
}