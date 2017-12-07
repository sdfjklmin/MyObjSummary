function othercheck(){}
function initbody(){
	$('body').click(function(){
		$('.menullss').hide();
	});
	$('body').keydown(c.onkeydown);
	$('#showmenu').click(function(){
		$('.menullss').toggle();
		return false;
	});
	$('.menullss li').click(function(){
		c.mencc(this);
	});
}
function showchayue(opt, st){
	alert('总查阅:'+st+'次\n最后查阅：'+opt+'');
}
function check(o1){
	var da = {'sm':form('check_explain').value,'mid':mid,'modenum':modenum,'zt':_getaolvw('check_status')};
	if(da.zt==''){js.setmsg('请选择处理动作');return;}if(da.zt=='2'&&isempt(da.sm)){js.setmsg('此动作必须填写说明');return;}
	if(form('zhuanbanname')){
		da.zyname 	= form('zhuanbanname').value;
		da.zynameid = form('zhuanbannameid').value;
	}
	if(form('nextnameid') && da.zt=='1'){
		da.nextname 	= form('nextname').value;
		da.nextnameid 	= form('nextnameid').value;
		if(da.nextnameid==''){
			js.setmsg('请选择下一步处理人');return;
		}
	}
	if(!da.zynameid && da.zt=='1'){
		var fobj=$('span[fieidscheck]'),i,fid,fiad;
		for(i=0;i<fobj.length;i++){
			fiad = $(fobj[i]);
			fid	 = fiad.attr('fieidscheck');
			da['cfields_'+fid]=form(fid).value;
			if(da['cfields_'+fid]==''){js.setmsg(''+fiad.text()+'不能为空');return;}
		}
	}
	var ostr=othercheck(da);
	if(typeof(ostr)=='string'&&ostr!=''){js.setmsg(ostr);return;}
	if(typeof(ostr)=='object')for(var csa in ostr)da[csa]=ostr[csa];
	js.setmsg('处理中...');
	o1.disabled = true;
	var url = c.gurl('check');
	js.ajax(url,da,function(a){
		if(a.success){
			js.setmsg(a.msg,'green');
			c.callback();
			if(get('autocheckbox'))if(get('autocheckbox').checked)c.close();
		}else{
			js.setmsg(a.msg);
			o1.disabled = false;
		}
	},'post,json',function(){
		js.setmsg('处理失败请重试');o1.disabled = false;
	});
}
function _getaolvw(na){
	var v = '',i,o=$("input[name='"+na+"']");
	for(i=0;i<o.length;i++)if(o[i].checked)v=o[i].value;
	return v;
}

function _submitother(nae,zt,ztid,ocan,las){
	if(!las)las='other_explain';
	if(!nae||!get(las)){js.setmsg('sorry;不允许操作','','msgview_spage');return;}
	var sm=$('#'+las+'').val();
	if(!zt)zt='';if(!ocan)ocan={};
	if(!ztid){js.setmsg('没有选择状态','','msgview_spage');return;}
	if(!sm){js.setmsg('没有输入备注/说明','','msgview_spage');return;}
	var da = js.apply({'name':nae,'mid':mid,'modenum':modenum,'zt':zt,'ztid':ztid,'sm':sm},ocan);
	js.setmsg('处理中...','','msgview_spage');
	js.ajax(c.gurl('addlog'),da,function(s){
		js.setmsg('处理成功','green', 'msgview_spage');
		$('#spage_btn').hide();
	},'post',function(s){
		js.setmsg(s,'','msgview_spage');
	});
	return false;
}
var c={
	callback:function(cs){
		var calb = js.request('callback');
		if(!calb)return;
		try{parent[calb](cs);}catch(e){}
		try{opener[calb](cs);}catch(e){}
		try{parent.js.tanclose('openinput');}catch(e){}
	},
	gurl:function(a){
		var url=js.getajaxurl(a,'flowopt','flow');
		return url;
	},
	close:function(){
		window.close();
		try{parent.js.tanclose('winiframe');}catch(e){}
	},
	other:function(nae,las){
		_submitother(nae,'','1',las);
	},
	others:function(nae,zt,ztid,ocan,las){
		_submitother(nae,zt,ztid,ocan,las);
	},
	mencc:function(o1){
		var lx=$(o1).attr('lx');
		if(lx=='2')c.delss();
		if(lx=='3')c.close();
		if(lx=='4')location.reload();
		if(lx=='0')c.clickprint();
		if(lx=='5')c.daochuword();
		if(lx=='1'){
			var url='index.php?a=lu&m=input&d=flow&num='+modenum+'&mid='+mid+'';
			js.location(url);
		}
	},
	clickprint:function(){
		c.hideoth();
		window.print();
	},
	daochuword:function(){
		var url='task.php?a=p&num='+modenum+'&mid='+mid+'&stype=word';
		js.location(url);
	},
	hideoth:function(){
		$('.menulls').hide();
		$('.menullss').hide();
		$('a[temp]').remove();
	},
	delss:function(){
		js.confirm('删除将不能恢复，确定要<font color=red>删除</font>吗？',function(lx){
			if(lx=='yes')c.delsss();
		});
	},
	delsss:function(){
		var da = {'mid':mid,'modenum':modenum,'sm':''};
		js.ajax(c.gurl('delflow'),da,function(a){
			js.msg('success','单据已删除,3秒后自动关闭页面,<a href="javascript:;" onclick="c.close()">[关闭]</a>');
			c.callback();
			setTimeout('c.close()',3000);
		},'post');
	},
	onkeydown:function(e){
		var code	= event.keyCode;
		if(code==27){
			c.close();
			return false;
		}
		if(event.altKey){
			if(code == 67){
				c.close();
				return false;
			}
		}
	},
	changeshow:function(lx){
		$('#showrecord'+lx+'').toggle();
	}
};