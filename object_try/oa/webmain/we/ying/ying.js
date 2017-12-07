var yy={
	init:function(){
		this.num = json.num;
		this.showobj = $('#mainbody');
		$('.weui_navbar').click(function(){return false;});
		$('body').click(function(){
			$("div[id^='menushoess']").remove();
		});
	},
	clickmenu:function(oi,o1){
		var sid='menushoess_'+oi+'';
		if(get(sid)){
			$('#'+sid+'').remove();
			return;
		}
		$("div[id^='menushoess']").remove();
		var a = json.menu[oi],slen=a.submenu.length,i,a1;
		if(slen<=0){
			this.clickmenus(a);
		}else{
			var o=$(o1),w=1/json.menu.length*100;
			var s='<div id="'+sid+'" style="position:fixed;left:'+(o.offset().left)+'px;bottom:50px; background:white;width:'+w+'%" class="menulist r-border-r r-border-l">';
			for(i=0;i<slen;i++){
				a1=a.submenu[i];
				s+='<div onclick="yy.clickmenua('+oi+','+i+')" class="r-border-t" style="color:'+a1.color+';">'+a1.name+'</div>';
			}
			s+='</div>';
			$('body').append(s);
		}
	},
	clickmenua:function(i,j){
		var a = json.menu[i].submenu[j];
		this.clickmenus(a);
	},
	clickmenus:function(a){
		$("div[id^='menushoess']").remove();
		if(a.type==0){this.clickevent(a);return;}
		if(a.type==1){
			var url=a.url,amod=this.num;
			if(url.substr(0,3)=='add'){
				if(url!='add')amod=url.replace('add_','');
				url='index.php?a=lum&m=input&d=flow&num='+amod+'&show=we';
			}
			js.location(url);
		}
	},
	clickevent:function(a){
		this.getdata(a.url, 1);
	},
	data:[],
	_showstotal:function(d){
		var d1,v,s,o1;
		for(d1 in d){
			v=d[d1];
			if(v==0)v='';
			o1= $('#'+d1+'_stotal');
			o1.html(v);
		}
	},
	regetdata:function(o,p){
		this.getdata(this.nowevent,p);
	},
	getdata:function(st,p){
		this.nowevent=st;
		js.ajax('index','getyydata',{'page':p,'event':st,'num':this.num},function(ret){
			yy.showdata(ret);
		},'mode');
	},
	xiang:function(oi){
		var d = this.data[oi-1];
		var ids = d.id,nus=d.modenum,modne=d.modename;
		if(!ids)return;
		if(!nus||nus=='undefined')nus = this.num;
		var url='task.php?a=x&num='+nus+'&mid='+ids+'&show=we';
		js.location(url);
	},
	suboptmenu:{},
	showmenu:function(oi){
		var a = this.data[oi-1],ids = a.id,i;
		var nus=a.modenum;if(!nus||nus=='undefined')nus = this.num;
		if(a.type=='applybill' && nus){
			var url='index.php?a=lum&m=input&d=flow&num='+nus+'&show=we';
			js.location(url);return;
		}
		if(!ids)return;
		this.tempid 	= ids;
		this.tempnum 	= nus;
		this.temparr 	= {oi:oi};
		var da = [{name:'详情',lx:998,oi:oi}];
		var subdata = this.suboptmenu[''+nus+'_'+ids+''];
		if(!subdata){
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> 加载菜单中...',lx:999});
			this.loadoptnum(nus,ids);
		}else{
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}
		js.showmenu({
			data:da,
			width:150,
			onclick:function(d){
				yy.showmenuclick(d);
			}
		});
	},
	loadoptnum:function(nus,id){
		js.ajax('agent','getoptnum',{num:nus,mid:id},function(ret){
			yy.suboptmenu[''+nus+'_'+id+'']=ret;
			yy.showmenu(yy.temparr.oi);
		},'none');
	},
	showmenuclick:function(d){
		d.num=this.num;d.mid=this.tempid;
		d.modenum = this.tempnum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi);return;}
		if(lx==996){this.xiang(this.temparr.oi);return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='changeuser';if(lx==3)clx='changeusercheck';
			changeuser('',clx);
			return;
		}
		if(lx==1 || lx==9 || lx==10){
			var bts = (d.issm==1)?'必填':'选填';
			js.wx.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(text){
				if(!text && d.issm==1){
					js.msg('msg','没有输入['+d.name+']说明');
				}else{
					yy.showmenuclicks(d, text);
				}
			});
			return;
		}
		this.showmenuclicks(d,'');
	},
	xuanuserok:function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	},
	showmenuclicks:function(d, sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.ajax('index','yyoptmenu',d,function(ret){
			yy.suboptmenu[''+d.modenum+'_'+d.mid+'']=false;
			yy.getdata(yy.nowevent, 1);
		});	
	},
	showdata:function(a){
		var s='',i,len=a.rows.length,d,st='',oi;
		$('#showblank').remove();
		$('#notrecord').remove();
		if(typeof(a.stotal)=='object')this._showstotal(a.stotal);
		if(a.page==1){
			this.showobj.html('');
			this.data=[];
		}
		for(i=0;i<len;i++){
			d=a.rows[i];
			oi=this.data.push(d);
			if(d.showtype=='line' && d.title){
				s='<div class="contline">'+d.title+'</div>';
			}else{
				if(!d.statuscolor)d.statuscolor='';
				st='';
				if(d.ishui==1)st='color:#aaaaaa;';
				s='<div onclick="yy.showmenu('+oi+')" style="'+st+'" class="contlist">';
				/*
				s+='<div><table><tr>';
				s+='<td align="left" width="45"><img height="35" width="35" src="images/noface.png"></td>';
				s+='<td><div style="height:1px;line-height:16px;font-size:12px">管理员</div><div style="height:20px;line-height:20px"  class="dt">'+d.title+'</div></td>';
				s+='</tr></table></div>';
				*/
				if(d.title)s+='<div class="tit">'+d.title+'</div>';
				if(d.optdt)s+='<div class="dt">'+d.optdt+'</div>';
				if(d.cont)s+='<div class="cont">'+d.cont.replace(/\n/g,'<br>')+'</div>';
				if(d.url)s+='<div class="xq">详情</div>';
				if(d.statustext)s+='<div style="background-color:'+d.statuscolor+';opacity:0.7" class="zt">'+d.statustext+'</div>';
				s+='</div>';
			}
			this.showobj.append(s);
		}
		var count=a.count;
		if(count==0)count=len;
		if(count>0){
			s = '<div class="showblank" id="showblank">共'+count+'条记录';
			if(a.maxpage>1)s+=',当前'+a.maxpage+'/'+a.page+'页';
			if(a.page<a.maxpage)s+=', <a onclick="yy.regetdata(this,'+(a.page+1)+')" href="javascript:;">点击加载</a>';
			s+= '</div>';
			this.showobj.append(s);
			if(a.count==0)$('#showblank').html('');
		}else{
			this.showobj.html('<div class="notrecord" id="notrecord">暂无记录</div>');
		}
	}
}

function changeuser(na,lx){
	if(!lx)lx='';
	var url='task.php?fn=dept&changetype='+lx+'';
	var s='<div style="height:100%;width:100%;position:fixed;top:0px;left:0px;z-index:99; background:rgba(0,0,0,0.6)"  align="center" id="changmodddid">';
	s+='<div style="max-width:300px;height:100%;max-height:450px;margin-top:5%; background:while;border:1px #dddddd solid">';
	s+='<iframe style="background:white" name="changdept" height="100%" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0" width="100%" src="'+url+'"></iframe>';
	s+='</div>';
	s+='</div>';
	$('body').append(s);
}
function changecancel(){
	$('#changmodddid').remove();
}
function changeok(sna,sid){
	yy.xuanuserok(sna,sid);
}