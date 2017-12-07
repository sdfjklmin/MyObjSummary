var objcont,tabs_title,tabsarr={},nowtabs,opentabs=[],menutabs,menuarr;
var viewwidth,viewheight;
function publicstore(mo,dos,oans){
	if(!mo)mo='index';
	if(!dos)dos='';
	return js.getajaxurl('publicstore',mo,dos,oans);
}
function publicsave(mo, dos,oans){
	if(!mo)mo='index';
	if(!dos)dos='';
	return js.getajaxurl('publicsave',mo,dos,oans);
}
function editfacechang(xid,nems){
	js.upload('_editfacechangback',{maxup:'1',thumbnail:'150x150','title':'修改['+nems+']的头像',uptype:'image','params1':xid});	
}
function _editfacechangback(a,xid){
	var f = a[0];
	var nf= f.thumbpath+'?'+Math.random()+'';
	if(xid==adminid)get('myface').src=nf;
	if(get('faceviewabc_'+xid+''))get('faceviewabc_'+xid+'').src=nf;
	js.msg('wait','头像修改中...');
	js.ajax(js.getajaxurl('editface','admin','system'),{fid:f.id,'uid':xid},function(){
		js.msg('success','修改成功,如没显示最新头像，请清除浏览器缓存');
	});
}
function initbody(){
	objcont = $('#content_allmainview');
	objtabs = $('#tabs_title');
	resizewh();
	$(window).resize(resizewh);
	clickhome();

	var a = $("span[pmenuid]");
	a.click(function(){
		if(js.ajaxbool)return;
		a.removeClass();
		loadmenu(this);
	});
	loadmenu($("span[pmenuid]")[0]);
	if(typeof(applicationCache)=='undefined'){
		//js.msg('msg','您的浏览器太低了无法达到想要的预览效果<br>建议使用IE10+，Firefox，Chrome等高级点的',60);
	}
	if(a.length<=1)$('.topmenubg').html('');
	var ddsata=[{
		name:'<i class="icon-lock"></i> 修改密码',num:'pass',url:'system,geren,pass',names:'修改密码'
	},{
		name:'<i class="icon-bell"></i> 提醒信息',num:'todo',url:'system,geren,todo',names:'提醒信息'
	},{
		name:'<i class="icon-picture"></i> 修改头像',num:'face'
	},{
		name:'<i class="icon-magic"></i> 切换皮肤',num:'style',url:'system,geren,style',names:'切换皮肤'
	},{
		name:'<i class="icon-user"></i> 帐号('+adminuser+')',num:'user'
	}];
	if(js.request('afrom')=='')ddsata.push({name:'<i class="icon-signout"></i> 退出',num:'exit'});
	$('#indexuserl').rockmenu({
		width:170,top:50,
		data:ddsata,
		itemsclick:function(d){
			if(d.num=='exit'){
				js.confirm('确定要退出系统吗？',function(bn){
					if(bn=='yes')js.location('?m=login&a=exit');
				});
				return;
			}
			if(d.num=='face'){
				editfacechang(adminid, adminname);
				return;
			}
			if(d.num=='user')return;
			addtabs({num:d.num,url:d.url,name:d.names});
		}
	});
	$('#reordershla').click(function(){
		$('#indexmenu').hide();
		$('#indexmenuss').show();
		resizewh();
	});
	$('#indexmenuss').click(function(){
		$('#indexmenu').show();
		$('#indexmenuss').hide();
		resizewh();
	});
	$('body').keydown(function(e){
		var code	= e.keyCode;
		if(code==27){
			if(get('xpbg_bodydds')){
				js.tanclose($('#xpbg_bodydds').attr('xpbody'));
			}else{
				closenowtabs();
			}
			return false;
		}
	});
}

function loadmenu(o){
	var o1 = $(o);
	o1.addClass('spanactive');
	var id = o1.attr('pmenuid');
	$('#menulisttop').html(o1.html());
	$('#menulist').html('<div style="padding:30px;" align="center"><img src="images/mloading.gif"></div>');
	js.ajax(js.getajaxurl('getmenu','index'),{pid:id}, function(da){
		showmenula(da);
	});
}
function showmenula(da){
	var a = js.decode(da);
	menuarr = a;
	var i,s='',j,child;
	for(i=0; i<a.length; i++){
		s+='<div temp="menu"  onClick="clickmenu(this,'+i+',-1)" id="menu_list_'+a[i].num+'" class="menuone"><font><i class="icon-'+a[i].icons+'"></i></font> '+a[i].name+'';
		if(a[i].stotal>0)s+='<span id="menu_down_isons_'+a[i].num+'" class="icon-caret-down"></span>';
		s+='</div>';
		if(a[i].stotal>0){
			child = a[i].children;
			s+='<div class="menulist" id="menu_down_'+a[i].num+'" style="display:none">';
			for(j=0; j<child.length; j++){
				s+='<div temp="menu" id="menu_list_'+child[j].num+'" onClick="clickmenu(this,'+i+','+j+')" class="menutwo"><i class="icon-'+child[j].icons+'"></i> '+child[j].name+'</div>';
			}
			s+='</div>';
		}
	}
	$('#menulist').html(s);
}
function openinput(name,num, id,cbal){
	if(!id)id=0;
	if(!cbal)cbal='';
	if(id==0){name='[新增]'+name+'';}else{name='[编辑]'+name+'';}
	var url='?a=lu&m=input&d=flow&num='+num+'&mid='+id+'&callback='+cbal+'';
	openxiangs(name, url,'', cbal);
	return false;
}
function openxiangs(name,num,id,cbal){
	if(!id)id=0;
	if(!cbal)cbal='';
	var hm = winHb()-150;if(hm>800)hm=800;if(hm<400)hm=400;
	var wi = winWb()-150;if(wi>900)wi=900;if(wi<700)wi=700;
	js.tanbody('winiframe',name,wi,410,{
		html:'<div style="height:'+hm+'px;overflow:hidden"><iframe src="" name="openinputiframe" width="100%" height="100%" frameborder="0"></iframe></div>',
		bbar:'none'
	});
	var url = 'task.php?a=p&num='+num+'&mid='+id+'';
	if(num.indexOf('?')>-1)url=num;
	url+='&callback='+cbal+'';
	openinputiframe.location.href=url;
	return false;
}
function openxiang(num,id){
	var url = 'task.php?a=p&num='+num+'&mid='+id+'';
	js.open(url, 800,500);
}
function opentixiang(){
	addtabs({num:'todo',url:'system,geren,todo',icons:'bell',name:'提醒信息'});
	return false;
}

function clickhome(){
	addtabs({num:'home',url:'home,index',icons:'home',name:'首页',hideclose:true});
	return false;
}

function resizewh(){
	var _lw = $('#indexmenu').width();
	if(get('indexmenu').style.display=='none'){
		_lw = $('#indexmenuss').width();
	}
	var w = winWb()-_lw-5;
	var h = winHb();
	viewwidth = w; 
	viewheight = h-50-44; 
	$('#indexcontent').css({width:''+viewwidth+'px',height:''+(viewheight)+'px'});
	$('#tabsindexm').css({width:''+viewwidth+'px'});
	var nh = h-50;
	$('#indexmenu').css({height:''+nh+'px'});
	$('#indexsplit').css({height:''+nh+'px'});
	$('#indexmenuss').css({height:''+nh+'px'});
	$('#menulist').css({height:''+(viewheight)+'px'});
	_pdleftirng();
}

function clickmenu(o, i, k){
	var a = menuarr[i];
	if(k>-1)a=menuarr[i].children[k];
	var oi = a.stotal;
	if(oi>0){
		$('#menu_down_'+a.num+'').toggle();
		var o1	= get('menu_down_isons_'+a.num+'');
		if(o1.className.indexOf('down')>0){
			o1.className='icon-caret-up';
		}else{
			o1.className='icon-caret-down';
		}
	}else{
		addtabs(a);
	}
}
function changelassa(){
	var o1 = $("div[temp='menu']"),i,cls,cls1;
	for(i=0;i<o1.length;i++){
		cls = o1[i].className,cls1='menuone';
		if(cls.indexOf('two')>0)cls1='menutwo';
		o1[i].className=cls1;
	}
}


var coloebool = false;
function closetabs(num){
	tabsarr[num] = false;
	$('#content_'+num+'').remove();
	$('#tabs_'+num+'').remove();
	if(num == nowtabs.num){
		var now ='home',i,noux;
		for(i=opentabs.length-1;i>=0;i--){
			noux= opentabs[i];
			if(get('content_'+noux+'')){
				now = noux;
				break;
			}
		}
		changetabs(now);
	}
	coloebool = true;
	_pdleftirng();
	setTimeout('coloebool=false',10);
}

function closenowtabs(){
	var nu=nowtabs.num;
	if(nu=='home')return;
	closetabs(nu);
}

function changetabs(num,lx){
	if(coloebool)return;
	if(!lx)lx=0;
	$("div[temp='content']").hide();
	$("[temp='tabs']").removeClass();
	var bo = false;
	if(get('content_'+num+'')){
		$('#content_'+num+'').show();
		$('#tabs_'+num+'').addClass('accive');
		nowtabs = tabsarr[num];
		bo = true;
	}
	opentabs.push(num);
	if(lx==0)_changhhhsv(num);
	return bo;
}
function _changhhhsv(num){
	var o=$("[temp='tabs']"),i,w1=0;
	for(i=0;i<o.length;i++){
		if(o[i].id=='tabs_'+num+'')break;
		w1=w1+o[i].scrollWidth;
	}
	$('#tabsindexm').animate({scrollLeft:w1});
}
function _changesrcool(lx){
	var l = $('#tabsindexm').scrollLeft();
	var w = l+200*lx;
	$('#tabsindexm').animate({scrollLeft:w});
}
function _pdleftirng(){
	var mw=get('tabs_title').scrollWidth;
	if(mw>viewwidth){$('.jtcls').show();}else{$('.jtcls').hide();}
}
function addtabs(a){
	var url = a.url,
		num	= a.num;
	if(isempt(url))return false;
	if(url.indexOf('add,')==0){openinput(a.name,url.substr(4));return;}
	if(url.indexOf('http')==0){window.open(url);return;}
	nowtabs = a;
	if(changetabs(num))return true;

	var s = '<td temp="tabs" nowrap onclick="changetabs(\''+num+'\',1)" id="tabs_'+num+'" class="accive">';
	if(a.icons)s+='<i class="icon-'+a.icons+'"></i>  ';
	s+=a.name;
	if(!a.hideclose)s+='<span onclick="closetabs(\''+num+'\')" class="icon-remove"></span>';
	s+='</td>';
	objtabs.append(s);
	_changhhhsv(num);
	_pdleftirng();
	
	var rand = js.getrand(),i,oi=2,
		ura	= url.split(','),
		dir	= ura[0],
		mode= ura[1];
	url =''+PROJECT+'/'+dir+'/'+mode+'/rock_'+mode+'';
	if(ura[2]){
		if(ura[2].indexOf('=')<0){
			oi=3;
			url+='_'+ura[2]+'';
		}
	}
	url+='.shtml?rnd='+Math.random()+'';
	var urlpms= '';
	for(i=oi;i<ura.length;i++){
		var nus	= ura[i].split('=');
		urlpms += ",'"+nus[0]+"':'"+nus[1]+"'";
	}
	if(urlpms!='')urlpms = urlpms.substr(1);
	var bgs = '<div id="mainloaddiv" style="width:'+viewwidth+'px;height:'+viewheight+'px;overflow:hidden;background:#000000;color:white;filter:Alpha(opacity=20);opacity:0.2;z-index:3;position:absolute;left:0px;line-height:'+viewheight+'px;top:0px;" align="center"><img src="images/mloading.gif"  align="absmiddle">&nbsp;加载中...</div>';
	$('#indexcontent').append(bgs);
	
	objcont.append('<div temp="content" id="content_'+num+'"></div>');
	$.ajax({
		url:url,
		type:'get',
		success: function(da){
			$('#mainloaddiv').remove();
			var s = da;
				s = s.replace(/\{rand\}/gi, rand);
				s = s.replace(/\{adminid\}/gi, adminid);
				s = s.replace(/\{adminname\}/gi, adminname);
				s = s.replace(/\{mode\}/gi, mode);
				s = s.replace(/\{dir\}/gi, dir);
				s = s.replace(/\{params\}/gi, "var params={"+urlpms+"};");
			var obja = $('#content_'+num+'');
			obja.html(s);
		},
		error:function(){
			$('#mainloaddiv').remove();
			var s = 'Error:加载出错喽,'+url+'';
			$('#content_'+num+'').html(s);
		}
	});
	tabsarr[num] = a;
	return false;
}

var optmenudatas=[];
function optmenuclass(o1,num,id,obj,mname,oi){
	this.modenum = num;
	this.modename = mname;
	this.id 	 = id;
	this.mid 	 = id;
	this.tableobj=obj;
	this.oi 	= oi;
	this.obj 	= o1;
	var me 		= this;
	this._init=function(){
		if(typeof(optmenuobj)=='object')optmenuobj.remove();
		optmenuobj=$.rockmenu({
			data:[],
			itemsclick:function(d){me.showmenuclick(d);},
			width:150
		});
		var da = [{name:'详情',lx:998,nbo:false},{name:'详情(新窗口)',lx:998,nbo:true}];
		var off=$(this.obj).offset();
		var subdata = optmenudatas[''+this.modenum+'_'+this.id+''];
		if(!subdata){
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> 加载菜单中...',lx:999});
			this.loadoptnum();
		}else{
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}
		optmenuobj.setData(da);
		optmenuobj.showAt(off.left,off.top+20);
	};
	this.xiang=function(oi,nbo){
		var mnem=this.modename;
		if(!nbo){
			if(!mnem)mnem='详情';
			openxiangs(mnem,this.modenum,this.mid);
		}else{
			openxiang(this.modenum,this.mid);
		}
	};
	this.openedit=function(){
		openinput(this.modename,this.modenum,this.mid);
	};
	this.showmenuclick=function(d){
		d.num=this.modenum;d.mid=this.id;
		d.modenum = this.modenum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi, d.nbo);return;}
		if(lx==997){this.printexcel(d.oi);return;}
		if(lx==996){this.xiang(d.oi, d.nbo);return;}
		if(lx==11){this.openedit();return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='user';if(lx==3)clx='usercheck';
			js.getuser({type:clx,title:d.name,callback:function(na,nid){me.changeuser(na,nid);}});
			return;
		}
		var bts = (d.issm==1)?'必填':'选填';
		if(lx==1 || lx==9 || lx==10){
			js.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(index, text){
				if(index=='yes'){
					if(!text && d.issm==1){
						js.msg('msg','没有输入['+d.name+']说明');
					}else{
						me.showmenuclicks(d, text);
					}
				}
			});
			return;
		}
		if(lx==4){
			js.prompt(d.name, '说明('+bts+')：', function(index, text){
				if(index=='yes'){
					var ad=js.getformdata('myformsbc');
					for(var i in ad)d['fields_'+i+'']=ad[i];
					me.showmenuclicks(d, text);
				}
			},'', '<div align="left" id="showmenusss" style="padding:10px">加载中...</div>');
			var url='index.php?a=lus&m=input&d=flow&num='+d.modenum+'&menuid='+d.optmenuid+'&mid='+d.mid+'';
			$.get(url, function(s){
				var s='<form name="myformsbc">'+s+'</form>';
				$('#showmenusss').html(s);
				js.tanoffset('confirm');
			});
			return;
		}
		this.showmenuclicks(d,'');
	};
	this.changeuser=function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	};
	this.showmenuclicks=function(d,sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.msg('wait','处理中...');
		js.ajax(js.getajaxurl('yyoptmenu','flowopt','flow'),d,function(ret){
			if(ret.code==200){
				optmenudatas[''+d.modenum+'_'+d.mid+'']=false;
				me.tableobj.reload();
				js.msg('success','处理成功');
			}else{
				js.msg('msg',ret.msg);
			}
		},'post,json');	
	};
	this.loadoptnum=function(){
		js.ajax(js.getajaxurl('getoptnum','flowopt','flow'),{num:this.modenum,mid:this.id},function(ret){
			if(ret.code == 200){
				optmenudatas[''+me.modenum+'_'+me.id+''] = ret.data;
				me._init();
			}else{
				js.msg('msg',ret.msg);
			}
		},'get,json');
	};
	this._init();
}