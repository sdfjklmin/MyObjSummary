var MODE	= '',ACTION = '',DIR='',PROJECT='',HOST='',PARAMS='',QOM='xinhu_',apiurl='',token='',device='';
var windows	= null;
function initbody(){}
function globalbody(){}
$(document).ready(function(){
	$(window).scroll(js.scrolla);
	adminid=js.request('adminid');
	token=js.request('token');
	globalbody();
	initbody();
	$('body').click(function(e){
		js.downbody(this, e);
	});
});
var js={path:'index',url:'',bool:false,login:{},initdata:{},scroll:function(){}};
var isIE=true;
if(!document.all)isIE=false;
var get=function(id){return document.getElementById(id)};
var isempt=function(an){var ob	= false;if(an==''||an==null||typeof(an)=='undefined'){ob=true;}if(typeof(an)=='number'){ob=false;}return ob;}
var strreplace=function(str){if(isempt(str))return '';return str.replace(/[ ]/gi,'').replace(/\s/gi,'')}
var strhtml=function(str){if(isempt(str))return '';return str.replace(/\</gi,'&lt;').replace(/\>/gi,'&gt;')}
var form=function(an,fna){if(!fna)fna='myform';return document[fna][an]}
var xy10=function(s){var s1=''+s+'';if(s1.length<2)s1='0'+s+'';return s1;};
js.getarr=function(caa,bo){
	var s='';
	for(var a in caa)s+=' @@ '+a+'=>'+caa[a]+'';
	if(!bo)alert(s);
	return s;
}
js.getarropen=function(caa){
	jsopenararass = caa;
	js.open('js/array.shtml');
}
if(typeof(api)=='undefined'){
	var api={};
	api.systemType='android';
	api.deviceId='';
}
js.str=function(o){
	o.value	= strreplace(o.value);
}
js.getcan = function(i,dev){
	var a = PARAMS.split('-');
	var val = '';
	if(!dev)dev='';
	if(a[i])val=a[i];
	if(!val)val=dev;
	return val;
}
function winHb(){
	var winH=(!isIE)?window.innerHeight:document.documentElement.offsetHeight;
	return winH;
}
function winWb(){
	var winH=(!isIE)?window.innerWidth:document.documentElement.offsetWidth;
	return winH;
}
js.scrolla	= function(){
	var top	= $(document).scrollTop();
	js.scroll(top);
}
js.request=function(name,dev,url){
	if(!dev)dev='';
	if(!name)return dev;
	if(!url)url=location.href;
	if(url.indexOf('\?')<0)return dev;
	neurl=url.split('\?')[1];
	neurl=neurl.split('&');
	var value=dev;
	for(i=0;i<neurl.length;i++){
		val=neurl[i].split('=');
		if(val[0].toLowerCase()==name.toLowerCase()){
			value=val[1];
			break;
		}
	}
	if(!value)value='';
	return value;
}
js.now=function(type,sj){
	if(!type)type='Y-m-d';
	if(type=='now')type='Y-m-d H:i:s';
	var dt,ymd,his,weekArr,Y,m,d,w,H=0,i=0,s=0,W;
	if(typeof(sj)=='string')sj=sj.replace(/\//gi,'-');
	if(/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/.test(sj)){
		sj=sj.split(' ');
		ymd=sj[0];
		his=sj[1];if(!his)his='00:00:00';
		ymd=ymd.split('-');
		his=his.split(':');
		H = his[0];if(his.length>1)i = his[1];if(his.length>2)s = his[2];
		dt=new Date(ymd[0],ymd[1]-1,ymd[2],H,i,s);
	}else{
		dt=(typeof(sj)=='number')?new Date(sj):new Date();
	}
	weekArr=new Array('日','一','二','三','四','五','六');
	Y=dt.getFullYear();
	m=xy10(dt.getMonth()+1);
	d=xy10(dt.getDate());
	w=dt.getDay();
	H=xy10(dt.getHours());
	i=xy10(dt.getMinutes());
	s=xy10(dt.getSeconds());
	W=weekArr[w];
	if(type=='time'){
		return dt.getTime();
	}else{
		return type.replace('Y',Y).replace('m',m).replace('d',d).replace('H',H).replace('i',i).replace('s',s).replace('w',w).replace('W',W);
	}
}
js.float=function(num,w){
	if(isNaN(num)||num==''||!num||num==null)num='0';
	num=parseFloat(num);
	if(!w&&w!=0)w=2;
	var m=num.toFixed(w);
	return m;	
}
js.splittime=0;
js.getsplit=function(){
	if(!js.servernow)return false;
	var dt=js.now('Y-m-d H:i:s');
	var d1=js.now('time',dt);	
	var d2=js.now('time',js.servernow);
	js.splittime=d1-d2;
}
js.serverdt=function(atype){
	if(!atype)atype='Y-m-d H:i:s';
	var d1=js.now('time')-js.splittime;
	var dt=js.now(atype,d1);
	return dt;
}
js.openarr={};
js.open=function(url,w,h,wina,can){
	if(wina){
		try{
		var owina	= this.openarr[wina];
		owina.document.body;
		owina.focus();
		return false;
		}catch(e){}
	}
	var ja=(url.indexOf('?')>=0)?'&':'?';
	if(!w)w=600;
	if(!h)h=500;
	if(!can)can='resizable=yes,scrollbars=yes';
	var l=(screen.width-w)*0.5;
	var t=(screen.height-h)*0.5;
	var opar=window.open(url,'','width='+w+'px,height='+h+'px,left='+l+'px,top='+t+'px,'+can+'');
	if(wina)this.openarr[wina]=opar;
}
js.onunload=function(){
	var a=js.openarr;
	for(var b in a){
		try{a[b].close()}catch(e){}
	}
}
js.decode=function(str){
	var arr	= {length:-1};
	try{
		arr	= new Function('return '+str+'')();
	}catch(e){}
	return arr;
}
js.move=function(id,event){
	var _left=0,_top=0;
	var obj	= id;
	if(typeof(id)=='string')obj=get(id);
	var _Down=function(evt){
		try{
			var s='<div id="divmovetemp" style="filter:Alpha(Opacity=0);opacity:0;z-index:99999;width:100%;height:100%;position:absolute;background-color:#000000;left:0px;top:0px;cursor:move"></div>';
			$('body').prepend(s);
			evt=window.event||evt;
			_left=evt.clientX-parseInt(obj.style.left);
			_top=evt.clientY-parseInt(obj.style.top);
			document.onselectstart=function(){return false}
		}catch(e){}		
	}
	var _Move=function(evt){
		try{
			var c=get('divmovetemp').innerHTML;
			evt=window.event||evt;
			obj.style.left=evt.clientX-_left+'px';
			obj.style.top=evt.clientY-_top+'px';
		}catch(e){_Down(evt)}
	}
	var _Up=function(){
		document.onmousemove="";
		document.onselectstart=""
		$('#divmovetemp').remove();	
	}
	document.onmousemove=_Move
	document.onmouseup=_Up;
}
js.setdev=function(val,dev){
	var cv	= val;
	if(isempt(cv))cv=dev;
	return cv;
}
js.upload=function(call,can, glx){
	if(!call)call='';
	if(!can)can={};
	js.uploadrand	= js.now('YmdHis')+parseInt(Math.random()*999999);
	var url = 'index.php?m=upload&d=public&callback='+call+'&upkey='+js.uploadrand+'';
	for(var a in can)url+='&'+a+'='+can[a]+'';
	if(glx=='url')return url;
	var s='',tit=can.title;if(!tit)tit='上传文件';
	js.tanbody('uploadwin',tit,450,300,{
		html:'<div style="height:260px;overflow:hidden"><iframe src="" name="winiframe" width="100%" height="100%" frameborder="0"></iframe></div>',
		bbar:'none'
	});
	winiframe.location.href=url;
	return false;
}
js.downshow=function(id){
	js.open('?id='+id+'&a=down',600,350);
	return false;
}
js.downupdels=function(sid, said, o1){
	js.confirm('确定要删除此文件吗？', function(lx){
		if(lx=='yes'){
			js.downupdel(sid, said, o1);
		}
	});
}
js.downupdel=function(sid, said, o1){
	if(sid>0){
		$.get(js.getajaxurl('delfile','upload','public',{id:sid}));
	}
	if(o1)$(o1).parent().remove();
	var o = $('#view_'+said+'');
	var to= $('#count_'+said+'');
	var o1 = o.find('span'),s1='';
	for(i=0;i<o1.length;i++)$(o1[i]).html(''+(i+1));
	to.html('');
	if(i>0)to.html('<font style="font-size:11px" color="#555555">文件:'+i+'</font>');
	o1 = o.find('font');
	for(i=0;i<o1.length;i++)s1+=','+$(o1[i]).html();
	if(s1!='')s1=s1.substr(1);
	$('#'+said+'-inputEl').val(s1);
	$('#fileid_'+said+'').val(s1);
}
js.downupshow=function(a, showid){
	var s = '',i=0,s1='';
	var o = $('#view_'+showid+'');
	for(i=0; i<a.length; i++){
		s='<div onmouseover="this.style.backgroundColor=\'#f1f1f1\'" onmouseout="this.style.backgroundColor=\'\'" style="padding:4px 5px;border-bottom:1px #eeeeee solid"><span>'+(i+1)+'</span><font style="display:none">'+a[i].id+'</font>、<a class="a" onclick="return js.downshow('+a[i].id+',\''+a[i].fileext+'\')" href="javascript:">'+a[i].filename+'</a> ('+a[i].filesizecn+')';
		s+=' <a class="a" temp="dela" onclick="return js.downupdels('+a[i].id+',\''+showid+'\', this)" href="javascript:">×</a>';
		s+='</div>';
		o.append(s);
	}
	js.downupdel(0, showid, false);
}
js.getajaxurl=function(a,m,d,can){
	if(!can)can={};
	if(!m)m=MODE;
	if(!d)d=DIR;
	if(d=='null')d='';
	var jga	= a.substr(0,1);
	if(jga=='@')a = a.substr(1);
	var url	= ''+this.path+'.php?a='+a+'&m='+m+'&d='+d+'';
	for(var c in can)url+='&'+c+'='+can[c]+'';
	if(jga!='@')url+='&ajaxbool=true';	
	url+='&rnd='+Math.random()+'';	
	return url;
}
js.formatsize=function(size){
	var arr = new Array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
	var e	= Math.floor(Math.log(size)/Math.log(1024));
	var fs	= size/Math.pow(1024,Math.floor(e));
	return js.float(fs)+' '+arr[e];
}
js.getformdata=function(nas){
	var da	={},ona='',o,type,val,na,i,obj;
	if(!nas)nas='myform';
	obj	= document[nas];
	for(i=0;i<obj.length;i++){
		o 	 = obj[i];type = o.type,val = o.value,na = o.name;
		if(type=='checkbox'){
			val	= '0';
			if(o.checked)val='1';
			da[na]	= val;
		}else if(type=='radio'){
			if(o.checked)da[na]	= val;					
		}else{
			da[na] = val;
		}
		if(na.indexOf('[]')>-1){
			if(ona.indexOf(na)<0)ona+=','+na+'';
		}
	}
	if(ona != ''){
		var onas = ona.split(',');
		for(i=1; i<onas.length; i++){
			da[onas[i].replace('[]','')] = js.getchecked(onas[i]);
		}
	}
	return da;
}
js.getdata = function(na,da){
	if(!da)da={};
	var obj	= $('#'+na+'');
	var inp	= obj.find('input,select');
	for(var i=0;i<inp.length;i++){
		var type	= inp[i].type;
		var val		= inp[i].value;
		if(type=='checkbox'){
			val	= '0';
			if(inp[i].checked)val='1';
		}
		var ad1	= inp[i].name;
		if(!ad1)ad1 = inp[i].id;
		da[ad1]	= val;
	}
	return da;
}
js.selall = function(o,na,bh){
	var i,oi1;
	if(bh){
		o1=$("input[name^='"+na+"']");
	}else{
		o1=$("input[name='"+na+"']");
	}
	for(i=0;i<o1.length;i++){
		if(!o1[i].disabled)o1[i].checked = o.checked;
	}
}
js.getchecked=function(na,bh){
	var s = '';
	var o1;
	if(bh){
		o1=$("input[name^='"+na+"']");
	}else{
		o1=$("input[name='"+na+"']");
	}
	for(var i=0;i<o1.length;i++){
		if(o1[i].checked && !o1[i].disabled)s+=','+o1[i].value+'';
	}
	if(s!='')s=s.substr(1);
	return s;
}
js.cookie=function(name){
	var str=document.cookie,cda,val='',arr,i;
	if(str.length<=0)return '';
	arr=str.split('; ');
	for(i=0;i<arr.length;i++){
		cda=arr[i].split('=');
		if(name.toLowerCase()==cda[0].toLowerCase()){
			val=cda[1];
			break;
		}
	}
	if(!val)val='';
	return val;
}
js.savecookie=function(name,value,d){
	var expires = new Date();
	if(!d)d=365;
	if(!value)d=-10;
	expires.setTime(expires.getTime()+d*24*60*60*1000);
	var str=''+name+'='+value+';expires='+expires.toGMTString()+';path=/';
	document.cookie = str;
}
js.backtop=function(ci){
	if(!ci)ci=0;
	$('body,html').animate({scrollTop:ci});
	return false;
}
js.backto = function(oid){
	if(!get(oid))return;
	var of	= $('#'+oid+'').offset();
	this.backtop(of.top);
	return false;
}
js.applyIf=function(a,b){
	if(!a)a={};
	if(!b)b={};
	for(var c in b)if(typeof(a[c])=='undefined')a[c]=b[c];
	return a;
}
js.apply=function(a,b){
	if(!a)a={};
	if(!b)b={};
	for(var c in b)a[c]=b[c];
	return a;
}
js.tanbodyindex = 90;
js.tanbody=function(act,title,w,h,can1){
	this.tanbodyindex++;
	var can	= js.applyIf(can1,{html:'',showfun:function(){},bodystyle:'',guanact:'',titlecls:'',btn:[]});
	var l=(winWb()-w-50)*0.5,t=(winHb()-h-50)*0.5;
	var s	= '';
	var mid	= ''+act+'_main';
	$('#'+mid+'').remove();
	var posta= 'fixed';
	if(js.path == 'admin')posta='absolute';
	s+='<div id="'+mid+'" style="position:'+posta+';background-color:#ffffff;left:'+l+'px;width:'+w+'px;top:'+t+'px;z-index:'+this.tanbodyindex+';box-shadow:0px 0px 10px rgba(0,0,0,0.3);">';
	s+='	<div class="title '+can.titlecls+'" style="-moz-user-select:none;-webkit-user-select:none;user-select:none;">';
	s+='		<table border="0"  width="100%" cellspacing="0" cellpadding="0"><tr>';
	s+='			<td height="34" style="font-size:16px; font-weight:bold;color:white; padding-left:8px" width="100%" onmousedown="js.move(\''+mid+'\')" id="'+act+'_title">'+title+'</td>';
	s+='			<td><div onmouseover="this.style.backgroundColor=\'#C64343\'" onmouseout="this.style.backgroundColor=\'\'" style="padding:0px 8px;height:40px;overflow:hidden;cursor:pointer;" onclick="js.tanclose(\''+act+'\',\''+can.guanact+'\')"><div id="'+act+'_spancancel" style="height:16px;overflow:hidden;width:16px;background:url(images/wclose.png);margin-top:12px"></div></div></td>';
	s+='		</tr></table>';
	s+='	</div>';
	s+='	<div id="'+act+'_body" style="'+can.bodystyle+'">';
	s+=can.html;
	s+='	</div>';
	s+='	<div id="'+act+'_bbar" style="padding:5px 10px;background:#eeeeee;line-height:30px" align="right"><span id="msgview_'+act+'"></span>&nbsp;';
	for(var i=0; i<can.btn.length; i++){
		var a	= can.btn[i];
		s+='<a class="btn btn-success" id="'+act+'_btn'+i+'" onclick="return false" href="javascript:">';
		if(!isempt(a.icons))s+='<i class="icon-'+a.icons+'"></i>&nbsp; ';
		s+=''+a.text+'</a>&nbsp; ';
	}
	s+='		<a class="btn btn-default" id="'+act+'_cancel" onclick="return js.tanclose(\''+act+'\',\''+can.guanact+'\')" href="javascript:"><i class="icon-remove"></i>&nbsp;取消</a>';
	s+='	</div>';
	s+='</div>';
	js.xpbody(act,can.mode);
	$('body').prepend(s);
	if(can.closed=='none'){
		$('#'+act+'_bbar').remove();
		$('#'+act+'_spancancel').parent().remove();
	}
	if(can.bbar=='none')$('#'+act+'_bbar').remove();
	this.tanoffset(act);
	can.showfun(act);
}
js.tanoffset=function(act){
	var mid=''+act+'_main';
	var lw = get(mid).offsetWidth,lh=get(mid).offsetHeight,l,t;
	l=(winWb()-lw)*0.5;t=(winHb()-lh-20)*0.5;
	$('#'+mid+'').css({'left':''+l+'px','top':''+t+'px'});
}
js.tanclose=function(act, guan){
	if(!isempt(guan)){
		var s= guan.split(',');
		for(var i=0;i<s.length;i++)$('#'+s[i]+'_main').remove();
	}
	$('#'+act+'_main').remove();
	js.xpbody(act,'none');
	return false;
}
js.xpbody=function(act,type){
	if(type=='none'){
		$("div[xpbody='"+act+"']").remove();
		return;
	}
	if(get('xpbg_bodydds'))return false;
	var H	= (document.body.clientHeight<winHb())?winHb()-5:document.body.clientHeight;
	var W	= document.documentElement.scrollWidth+document.body.scrollLeft;
	var bs='<div id="xpbg_bodydds" xpbody="'+act+'" oncontextmenu="return false" style="position:absolute;display:none;width:'+W+'px;height:'+H+'px;filter:Alpha(opacity=30);opacity:0.3;left:0px;top:0px;background-color:#000000;z-index:80"></div>';
	$('body').prepend(bs);	
	$('#xpbg_bodydds').fadeIn(300);
}
js.focusval	= '0';
js.number=function(obj){
	val=strreplace(obj.value);
	if(!val){
		obj.value=js.focusval;
		return false;
	}
	if(isNaN(val)){
		js.msg('msg','输入的不是数字');
		obj.value=js.focusval;
		obj.focus();
	}else{
		var min=$(obj).attr('minvalue');if(min && parseFloat(val)<parseFloat(min))val=min;
		var max=$(obj).attr('maxvalue');if(max && parseFloat(val)>parseFloat(max))val=max;
		obj.value=val;
	}
}
js.setmsg=function(txt,col,ids){
	if(!ids)ids='msgview';
	$('#'+ids+'').html(js.getmsg(txt,col));
}
js.getmsg  = function(txt,col){
	if(!col)col='red';
	var s	= '';
	if(!txt)txt='';
	if(txt.indexOf('...')>0){
		s='<img src="images/loading.gif" height="16" width="16" align="absmiddle"> ';
		col = '#ff6600';
	}	
	s+='<span style="color:'+col+'">'+txt+'</span>';
	if(!txt)s='';
	return s;
}
js.setcopy	= function(txt){
	if(!txt)txt='';
	txt	= escape(txt);
	js.savecookie('copy_text', txt, 1);
	js.msg('msg','复制成功，仅限本站使用');
	return false;
}
js.getcopy = function(){
	var txt	= js.cookie('copy_text');
	txt	= unescape(txt);
	return txt;
}
js.chao=function(obj,shuzi,span,guo){
	var cont=(guo)?strreplace(obj.value):obj.value;
    if (cont.length>shuzi){
		alert("您输入的字符超过"+shuzi+"个字符\n\n将被截掉"+(cont.length-shuzi)+"个字符！");
		cont=cont.substring(0,shuzi);
		obj.value=cont;
	}
	if(guo)obj.value=cont;
	if(span)get(span).innerHTML=obj.value.length;
}
js.debug	= function(s){
	if(!DEBUG)return;
	if(typeof(console)!='object')return;
	console.log(s);
}
js.alert = function(txt,tit,fun){
	js.confirm(txt, fun, '', tit, 2, '');
}
js.confirm	= function(txt,fun, tcls, tis, lx,ostr){
	if(!lx)lx=0;
	var h = '<div style="padding:20px;line-height:30px" align="center">';
	if(lx==1){
		if(!tcls)tcls='';if(!ostr)ostr='';
		h='<div style="padding:10px;" align="center">'+ostr+'';
		h+='<div align="left" style="padding-left:10px">'+txt+'</div>';
		h+='<div ><textarea class="input" id="confirm_input" style="width:280px;height:60px">'+tcls+'</textarea></div>';
	}else{
		h+='<img src="images/helpbg.png" align="absmiddle">&nbsp; '+txt+'';
	}
	h+='</div>';
	h+='<div style="padding:10px" align="center"><a id="confirm_btn1" class="btn btn-default webbtn" sattr="yes" href="javascript:;"><i class="icon-ok"></i>&nbsp;确定</a>';
	if(lx!=2)h+=' &nbsp;  &nbsp;  &nbsp;  &nbsp; <a sattr="no" class="btn btn-danger webbtn" class="webbtn" id="confirm_btn" href="javascript:;"><i class="icon-remove"></i>&nbsp;取消</a>';
	h+='</div>';
	h+='<div class="blank10"></div>';
	if(!tcls)tcls='danger';if(lx==1)tcls='info';
	if(!tis)tis='<i class="icon-question-sign"></i>&nbsp;系统提示';
	js.tanbody('confirm', tis, 320, 200,{closed:'none',bbar:'none',html:h,titlecls:tcls});
	function backl(e){
		var jg	= $(this).attr('sattr'),val=$('#confirm_input').val();
		if(val==null)val='';
		if(typeof(fun)=='function')fun(jg, val);
		js.tanclose('confirm');
		return false;
	}
	$('#confirm_btn1').click(backl);
	if(get('confirm_btn')){
	$('#confirm_btn').click(backl);
	get('confirm_btn').focus();}
	if(lx==1)get('confirm_input').focus();
}
js.prompt = function(tit,txt,fun, msg, ostr){
	js.confirm(txt, fun, msg, tit, 1, ostr);
}
js.msg = function(lx, txt,sj){
	clearTimeout(this.msgshowtime);
	if(typeof(sj)=='undefined')sj=5;
	$('#msgshowdivla').remove();
	if(lx == 'none' || !lx){
		return;
	}
	if(lx == 'wait'){
		txt	= '<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> '+txt;
		sj	= 60;
	}
	if(lx=='msg')txt='<font color=red>'+txt+'</font>';var t=10;
	var s = '<div onclick="$(this).remove()" id="msgshowdivla" style="position:fixed;top:'+t+'px;z-index:20;" align="center"><div style="padding:8px 20px;background:rgba(0,0,0,0.7);color:white;font-size:16px;">'+txt+'</div></div>';
	$('body').append(s);
	var w=$('#msgshowdivla').width(),l=(winWb()-w)*0.5;
	$('#msgshowdivla').css('left',''+l+'px');
	if(sj>0)this.msgshowtime= setTimeout("$('#msgshowdivla').remove()",sj*1000);	
}
js.repempt=function(stt,v){
	var s = stt;
	if(isempt(s))s=v;
	return s;
}
js.getrand=function(){
	var r;
	r = ''+new Date().getTime()+'';
	r+='_'+parseInt(Math.random()*9999)+'';
	return r;
}
js.arraystr=function(str){
	if(!str)str='1|是,0|否';
	var s = str.split(','),
		d = [],i,s1,nv,vv;
	for(i=0; i<s.length; i++){
		s1 = s[i].split('|');
		nv = s1[0];
		vv = nv;
		if(s1.length>1)nv=s1[1];
		d.push([vv,nv]);
	}	
	return d;
}
js._bodyclick = {};
js.downbody=function(o1, e){
	this.allparent = '';
	this.getparenta($(e.target),0);
	var a,s = this.allparent,a1;
	for(a in js._bodyclick){
		a1 = js._bodyclick[a];
		if(s.indexOf(a)<0){
			if(a1.type=='hide'){
				$('#'+a1.objid+'').hide();
			}else{
				$('#'+a1.objid+'').remove();
			}
		}
	}
	return true;
}
js.addbody = function(num, type,objid){
	js._bodyclick[num] = {type:type,objid:objid};
}
js.getparenta=function(o, oi){
	try{
	if(o[0].nodeName.toUpperCase()=='BODY')return;}catch(e){return;}
	var id = o.attr('id');
	if(!isempt(id)){
		this.allparent+=','+id;
	}
	this.getparenta(o.parent(), oi+1);
}
js.ajaxwurbo = false;
js.ajaxbool = false;
js.ajax = function(url,da,fun,type,efun, tsar){
	if(js.ajaxbool)return;
	if(!da)da={};if(!type)type='get';if(!tsar)tsar='';tsar=tsar.split(',');
	if(typeof(fun)!='function')fun=function(){};
	if(typeof(efun)!='function')efun=function(){};
	var atyp = type.split(','),dtyp='';type=atyp[0];
	if(atyp[1])dtyp=atyp[1];
	js.ajaxbool=true;if(tsar[0])js.msg('wait', tsar[0]);
	var ajaxcan={
		type:type,
		data:da,url:url,
		success:function(str){
			js.ajaxbool=false;
			try{
				if(tsar[1])js.msg('success', tsar[1]);
				fun(str);
			}catch(e){
				js.msg('msg', str);
			}
		},error:function(e){
			js.ajaxbool=false;
			js.msg('msg','处理出错:'+e.responseText+'');
			efun();
		}
	};
	if(dtyp)ajaxcan.dataType=dtyp;
	$.ajax(ajaxcan);
}
js.setoption=function(k,v){
	k=QOM+k;
	try{
		if(isempt(v)){
			localStorage.removeItem(k);
		}else{
			localStorage.setItem(k, v);
		}
	}catch(e){
		js.savecookie(k,v);
	}
	return true;
}
js.getoption=function(k,dev){
	var s = '';
	k=QOM+k;
	try{s = localStorage.getItem(k);}catch(e){s=js.cookie(k);}
	if(isempt(dev))dev='';
	if(isempt(s))s=dev;
	return s;
}
js.location = function(url){
	location.href = url;
}
js.backla=function(msg){
	if(msg)if(!confirm(msg))return;
	try{api.closeWin();}catch(e){}
}
js.sendevent=function(typ,na,d){
	if(!d)d={};
	d.opttype=typ;
	if(!na)na='xinhuhome';
	if(api.sendEvent)api.sendEvent({
		name: na,
		extra:d
	});
}
js.isimg = function(lx){
	var ftype 	= '|png|jpg|bmp|gif|jpeg|';
	var bo		= false;
	if(ftype.indexOf('|'+lx+'|')>-1)bo=true;
	return bo;
}
js.changeuser=function(na, lx){
	this.backnana=na;
	var h = winHb()-70;
	if(h>400)h=400;
	js.tanbody('changeaction','请选择...',350,h,{
		html:'<div style="height:'+h+'px"><iframe src="" name="winiframe" width="100%" height="100%" frameborder="0"></iframe></div>',
		bbar:'none'
	});
	var url='task.php?fn=dept&changetype='+lx+'';
	winiframe.location.href=url;
	return false;
}
js.back=function(){
	history.back();
}
js.changeclear=function(na){
	get(na).value='';
	get(na+'_id').value='';
	get(na).focus();
}
function changeok(na,naid){
	if(!js.backnana)return;
	get(js.backnana).value=na;
	get(js.backnana+'_id').value=naid;
	get(js.backnana).focus();
}
function changecancel(){
	js.tanclose('changeaction');
}
js.changedate=function(o1,id,v){
	if(!v)v='date';
	$(o1).rockdatepicker({initshow:true,view:v,inputid:id});
}
js.fileall=',aac,ace,ai,ain,amr,app,arj,asf,asp,aspx,av,avi,bin,bmp,cab,cad,cat,cdr,chm,com,css,cur,dat,db,dll,dmv,doc,docx,dot,dps,dpt,dwg,dxf,emf,eps,et,ett,exe,fla,ftp,gif,hlp,htm,html,icl,ico,img,inf,ini,iso,jpeg,jpg,js,m3u,max,mdb,mde,mht,mid,midi,mov,mp3,mp4,mpeg,mpg,msi,nrg,ocx,ogg,ogm,pdf,php,png,pot,ppt,pptx,psd,pub,qt,ra,ram,rar,rm,rmvb,rtf,swf,tar,tif,tiff,txt,url,vbs,vsd,vss,vst,wav,wave,wm,wma,wmd,wmf,wmv,wps,wpt,wz,xls,xlsx,xlt,xml,zip,';
js.filelxext = function(lx){
	if(js.fileall.indexOf(','+lx+',')<0)lx='wz';
	return lx;
}
js.datechange=function(o1,lx){
	if(!lx)lx='date';
	$(o1).rockdatepicker({'view':lx,'initshow':true});
	return false;
}
js.selectdate=function(o1,inp,lx){
	if(!lx)lx='date';
	$(o1).rockdatepicker({'view':lx,'initshow':true,'inputid':inp});
	return false;
}
js.importjs=function(url,fun){
	var sid = jm.encrypt(url);
	if(!fun)fun=function(){};
	if(get(sid)){fun();return;}
	var scr = document.createElement('script');
	scr.src = url;
	scr.id 	= sid;
	if(isIE){
		scr.onreadystatechange = function(){
			if(this.readyState=='loaded' || this.readyState=='complete'){fun(this);}
		}
	}else{
		scr.onload = function(){fun(this);}
	}
	document.getElementsByTagName('head')[0].appendChild(scr);
	return false;	
}