var ismobile=0;
function initbodys(){};
function savesuccess(){};
function eventaddsubrows(){}
function eventdelsubrows(){}
function geturlact(act){
	var url=js.getajaxurl(act,'mode_'+moders.num+'|input','flow');
	return url;
}
function initbody(){
	$('body').keydown(function(et){
		var code	= event.keyCode;
		if(code==27){
			c.close();
			return false;
		}
		if(event.altKey){
			if(code == 83){
				get('AltS').click();
				return false;
			}
		}
	});
	
	var len = arr.length,i,fid,nfid='';
	for(i=0;i<len;i++){
		fid=arr[i].fields;
		if(arr[i].islu=='1' && arr[i].iszb=='0'){
			if(arr[i].fieldstype=='checkboxall')fid+='[]';
			if(!form(fid)){
				nfid+='\n('+fid+'.'+arr[i].name+')';
			}
			if(arr[i].fieldstype=='htmlediter')c.htmlediter(arr[i].fields);
		}
	}
	c.initsubtable();
	if(nfid==''){
		c.showdata();
	}else{
		alert('录入页面缺少必要的字段：'+nfid+'');
	}
	
	if(ismobile==1)f.fileobj = $.rockupload({
		autoup:false,
		fileview:'filedivview',
		allsuccess:function(){
			c.saveken();
		}
	});
}
function changesubmit(d){};
function changesubmitbefore(){};

var f={
	change:function(o1){
		f.fileobj.change(o1);
	}
};

js.apiurl = function(m,a,cans){
	var url=''+apiurl+'api.php?m='+m+'&a='+a+'&adminid='+adminid+'';
	var cfrom='mweb';
	url+='&device='+device+'';
	url+='&cfrom='+cfrom+'';
	url+='&token='+token+'';
	if(!cans)cans={};
	for(var i in cans)url+='&'+i+'='+cans[i]+'';
	return url;
}

var c={
	callback:function(cs){
		var calb = js.request('callback');
		if(!calb){
			try{
			if(ismobile==0){
			parent.bootstableobj[moders.num].reload();
			parent.js.msg('success','处理成功');
			parent.js.tanclose('winiframe');}
			}catch(e){}
			return;
		}
		try{parent[calb](cs);}catch(e){}
		try{opener[calb](cs);}catch(e){}
		try{parent.js.tanclose('winiframe');}catch(e){}
	},
	save:function(){
		var d = this.savesss();
		if(!d)return;
		if(ismobile==1){
			js.msg('wait','保存中...');
			get('AltS').disabled=true;
			f.fileobj.start();
		}else{
			this.saveken();
		}
	},
	saveken:function(){
		var d = this.savesss();
		if(!d)return;
		this.saveok(d);
	},
	savesss:function(){
		var d=this.getsubdata(0);
		if(js.ajaxbool||isedit==0)return false;
		var len = arr.length,i,val,fid;
		changesubmitbefore();
		var d = js.getformdata();
		for(i=0;i<len;i++){
			if(arr[i].iszb!='0')continue;
			fid=arr[i].fields;
			if(ismobile==0 && arr[i].islu=='1'&&arr[i].fieldstype=='htmlediter'){
				d[fid] = this.editorobj[fid].html();
			}
			if(arr[i].isbt=='1'){
				val=d[fid];
				if(isempt(val)){
					if(form(fid))form(fid).focus();
					js.setmsg(''+arr[i].name+'不能为空');
					if(ismobile==1)js.msg('msg',''+arr[i].name+'不能为空');
					return false;
				}
			}
		}
		var s=changesubmit(d);
		if(typeof(s)=='string'&&s!=''){
			js.setmsg(s);
			js.msg('msg',s);
			return false;
		}
		if(typeof(s)=='object')d=js.apply(d,s);
		d.modeid=moders.id;
		d.modenum=moders.num;
		return d;
	},
	saveok:function(d){
		js.setmsg('保存中...');
		get('AltS').disabled=true;
		js.ajax(geturlact('save'),d,function(str){
			var a = js.decode(str);
			c.backsave(a, str);
		}, 'post', function(){
			get('AltS').disabled=false;
			js.setmsg('error:内部错误,可F12调试');
		});
	},
	backsave:function(a,str){
		var msg = a.msg;
		if(a.success){
			js.setmsg(msg,'green');
			js.msg('success','保存成功');
			this.formdisabled();
			$('#AltS').hide();
			form('id').value=a.data;
			isedit=0;
			this.callback(a.data);
			try{
			js.sendevent('reload', 'yingyong_mode_'+moders.num+'');
			js.backla();}catch(e){}
			savesuccess();
		}else{
			if(typeof(msg)=='undefined')msg=str;
			get('AltS').disabled=false;
			js.setmsg(msg);
			js.msg('msg',msg);
		}
	},
	showdata:function(){
		var smid=form('id').value;
		if(smid=='0'||smid==''){
			isedit=1;
			$('#AltS').show();
			c.initdatelx();
			initbodys(smid);
		}else{
			js.setmsg('加载数据中...');
			js.ajax(geturlact('getdata'),{mid:smid,flownum:moders.num},function(str){
				c.showdataback(js.decode(str));	
			},'post', function(){
				js.setmsg('error:内部错误,可F12调试');
			});
		}
	},
	initdatelx:function(){
		
	},
	showdataback:function(a){
		if(a.success){
			var da = a.data;
			js.setmsg();
			var len = arr.length,i,fid,val,flx,ojb,j;
			data=da.data;
			for(i=0;i<len;i++){
				fid=arr[i].fields;
				flx=arr[i].fieldstype;
				if(arr[i].islu=='1' && arr[i].iszb=='0'){
					val=da.data[fid];
					if(val==null)val='';
					if(flx=='checkboxall'){
						ojb=$("input[name='"+fid+"[]']");
						val=','+val+',';
						for(j=0;j<ojb.length;j++){
							if(val.indexOf(','+ojb[j].value+',')>-1)ojb[j].checked=true;
						}
					}else if(flx=='checkbox'){
						form(fid).checked = (val=='1');
					}else if(flx=='htmlediter' && ismobile==0){
						this.editorobj[fid].html(val);
					}else if(flx.substr(0,6)=='change'){
						if(form(fid))form(fid).value=val;
						fid = arr[i].data;
						if(!isempt(fid)&&form(fid))form(fid).value=da.data[fid];
					}else{
						if(form(fid))form(fid).value=val;
					}
				}
			}
			isedit=da.isedit;
			if(form('base_name'))form('base_name').value=da.user.name;
			if(form('base_deptname'))form('base_deptname').value=da.user.deptname;
			js.downupshow(da.filers,'fileidview','', (isedit==0));
			var subd = da.subdata,subds;
			for(j=0;j<=3;j++){
				subds=subd['subdata'+j+''];
				if(subds)for(i=0;i<subds.length;i++){
					subds[i].sid=subds[i].id;
					if(form('xuhao'+j+'_'+i+'')){
						c.adddatarow(j,i, subds[i]);
					}else{
						c.insertrow(j, subds[i], true);
					}
				}
			}
			initbodys(form('id').value);
			if(isedit==0){
				this.formdisabled();
				js.setmsg('无权编辑');
			}else{
				$('#AltS').show();
				c.initdatelx();
			}
			if(da.isflow==1){
				$('.status').show();
				var zt=da.status;
				if(da.data.isturn=='0')zt='3';
				get('statusimg').src='images/status'+zt+'.png';
			}
		}else{
			get('AltS').disabled=true;
			this.formdisabled();
			js.setmsg(a.msg);
			js.msg('msg',a.msg);
		}
	},
	date:function(o1,lx){
		$(o1).rockdatepicker({view:lx,initshow:true});
	},
	close:function(){
		window.close();
	},
	formdisabled:function(){
		$('form').find('*').attr('disabled', true);
		$('#fileupaddbtn').remove();
	},
	upload:function(){
		js.upload('',{showid:'fileidview'});
	},
	changeuser:function(na, lx){
		js.changeuser(na,lx);
	},
	changeclear:function(na){
		js.changeclear(na);
	},
	editorobj:{},
	htmlediter:function(fid){
		if(ismobile==1)return;
		var cans  = {
			resizeType : 0,
			allowPreviewEmoticons : false,
			allowImageUpload : true,
			formatUploadUrl:false,
			allowFileManager:true,
			uploadJson:'?m=upload&a=upimg&d=public',
			minWidth:'300px',height:'250',
			items : [
				'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|','image', 'link','unlink','|','source','clearhtml','fullscreen'
			]	
		};
		this.editorobj[fid] = KindEditor.create("[name='"+fid+"']", cans);
	},
	subtablefields:[],
	initsubtable:function(){
		var i,oba,j,o,nas,nle,nasa,fname;
		for(i=0;i<=3;i++){
			if(get('tablesub'+i+'')){
				fname=[];
				o=$('#tablesub'+i+'');
				form('sub_totals'+i+'').value=o.find('tr').length-1;
				this.repaixuhao(i);
				oba = o.find('tr:eq(1)').find('[name]');
				for(j=0;j<oba.length;j++){
					nas=oba[j].name;
					nasa=nas.split('_');
					nle = nasa.length;
					nna= nasa[0];
					if(nle>2)nna+='_'+nasa[1]+'';
					if(nle>3)nna+='_'+nasa[2]+'';
					fname.push(nna.substr(0,nna.length-1));
				}
				this.subtablefields[i]=fname;
			}
		}
	},
	getsubdata:function(i){
		var d=[];
		if(!get('tablesub'+i+''))return d;
		var len=parseFloat(form('sub_totals'+i+'').value);
		var i1,ji,i2,far=this.subtablefields[i],lens=far.length,fna;
		for(i1=0;i1<len;i1++){
			var a={};i2=0;
			for(j1=0;j1<lens;j1++){
				fna=''+far[j1]+''+i+'_'+i1+'';
				if(form(fna)){
					a[far[j1]]=form(fna).value;
					i2++;
				}
			}
			if(i2>0)d.push(a);
		}
		return d;
	},
	delrow:function(o,xu){
		if(isedit==0){
			$(o).remove();
			return;
		}
		var o1=$('#tablesub'+xu+'').find('tr');
		if(o1.length<=2){
			js.msg('msg','最后一行不能删除');
			return;
		}
		$(o).parent().parent().remove();
		this.repaixuhao(xu);
		eventdelsubrows(xu);
	},
	repaixuhao:function(xu){
		var o=$('#tablesub'+xu+'').find("input[temp='xuhao']");
		for(var i=0;i<o.length;i++){
			o[i].value=(i+1);
		}
	},
	insertrow:function(xu, d, isad){
		if(!get('tablesub'+xu+'')){
			alert('error=201：表单设计有误');
			return;
		}
		var o=$('#tablesub'+xu+'');
		var o1=o.find('tr'),oi=o1.length-1,i,str,oba,nas,oj,nna,ax2,d1;
		str = o.find('tr:eq('+oi+')').html();
		oba = o.find('tr:eq('+oi+')').find('[name]');
		oj  = parseFloat(form('sub_totals'+xu+'').value);
		var narrs=[],fasr=this.subtablefields[xu],wux=''+xu+'_'+oj+'';
		for(i=0;i<oba.length;i++){
			nas=oba[i].name;
			nna=fasr[i]+''+wux+'';
			str=str.replace(nas, nna);
			str=str.replace(nas, nna);
			narrs.push(nna);
		}
		form('sub_totals'+xu+'').value=(oj+1);
		str=str.replace('rockdatepickerbool="true"','');
		o.append('<tr>'+str+'</tr>');
		d=js.apply({sid:'0'},d);
		for(d1 in d){
			ax2=d1+wux;
			if(form(ax2))form(ax2).value=d[d1];
		}
		this.repaixuhao(xu);
		this.initdatelx();
		if(!isad)eventaddsubrows(xu);
	},
	adddatarow:function(xu, oj, d){
		d=js.apply({sid:'0'},d);
		var fasr=this.subtablefields[xu],ans;
		for(var i=0;i<fasr.length;i++){
			ans=fasr[i]+''+xu+'_'+oj+'';
			if(form(ans)&&d[fasr[i]])form(ans).value=d[fasr[i]];
		}
	},
	setrowdata:function(xu, oj, d){
		var ans;
		for(var i in d){
			ans=i+''+xu+'_'+oj+'';
			if(form(ans))form(ans).value=d[i];
		}
	},
	addrow:function(o,xu){
		if(isedit==0){
			$(o).remove();
			return;
		}
		this.insertrow(xu);
	},
	getsubtabledata:function(){
		
	},
	_getsubtabledatas:function(xu){
		var oxut=form('sub_totals'+xu+'');
		if(!oxut)return false;
		var da={},fasr,len=parseFloat(oxut.value),j,f,na;
		da['sub_totals'+xu+'']=oxut.value;
		fasr=this.subtablefields[xu];
		for(j=1;j<=len;j++){
			for(f=0;j<fasr.length;j++){
				na=fasr[f]+''+xu+'_'+j+'';
				if(form(na))da[na]=form(na).value;
			}
		};
		return da;
	},
	getsubtotals:function(fid, xu){
		var oi=0;
		if(!xu)xu='0';
		var oxut=form('sub_totals'+xu+'');
		if(!oxut)return oi;
		var len=parseFloat(oxut.value),j,na,val;
		for(j=0;j<len;j++){
			na=fid+''+xu+'_'+j+'';
			if(form(na)){
				val=form(na).value;
				if(val)oi+=parseFloat(val);
			}
		}
		return oi;
	}
};