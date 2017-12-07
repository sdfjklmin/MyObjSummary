/**
*	桌面通知插件(支持IE啊)
*	createname：雨中磐石
*	homeurl：http://xh829.com/
*	Copyright (c) 2016 rainrock (xh829.com)
*	Date:2016-01-01
*	var notify = notifyClass({
*		'sound':'声音文件地址','soundbo':true,'icon':'通知图标'
*	});
*	notify.showpopup('这是个通知？');
*	soundbo 声音提示
*	sound 声音文件地址
*/

function notifyClass(opts){
	this.title = '系统提醒';
	this.icon  = 'images/logo.png';
	this.notbool=true;
	this.lastmsg = '';
	this.sound 	 = '';
	this.sounderr= '';
	this.soundbo = true;
	this._init=function(){
		if(opts)for(var o1 in opts)this[o1]=opts[o1];
		var strsr = '';
		if(typeof(Notification)=='undefined'){
			this.notbool=false;
			strsr = '<bgsound id="notify_sound_audio" src="" hidden="true" autostart="false" loop="false">';
		}else{
			strsr = '<audio id="notify_sound_audio" hidden="true" src="'+this.sound+'"></audio>';
		}
		if(this.sound)$('body').append(strsr);
	};
	this.setsound	= function(bo){
		this.soundbo=bo;
	};
	this.opennotify = function(clsfun){
		if(!this.notbool)return false;
		if(!clsfun)clsfun=function(){};
		if(Notification.permission === 'granted')return false;
		if(Notification.permission !== 'denied'){
			Notification.requestPermission(function (permission){
				clsfun();
				if(!('permission' in Notification)) {
					Notification.permission = permission;
				}
				if(permission==='granted') {
					
				}
			});
		}
	};
	this.showpopup = function(msg,cans){
		this.lastmsg = msg;
		var can	= {body:msg,icon:this.icon,soundbo:this.soundbo,sound:this.sound,tag:'rockwebkitMeteoric',title:this.title,click:function(){}};
		if(cans)for(var oi in cans)can[oi]=cans[oi];
		var clsfun=can.click,title=can.title;
		if(!this.notbool){
			this._showpopupie(msg,clsfun,can);
			return;
		}else{
			var lx = this.getaccess();
			if(lx!='ok'){
				this.opennotify();
			}
		}
		var notification	= new Notification(title, can);
		notification.onclick = function(){
			var salx=clsfun(can);
			if(!salx)nwjs.winshow();
			this.close();
		};
		if(can.soundbo)this.playsound(can.sound);
	};
	this.playsound=function(src){
		if(!src)src=this.sound;
		var boa=document.getElementById('notify_sound_audio');
		if(boa){
			boa.src=src;
			if(boa.play)boa.play();
		}
	};
	this.playerrsound=function(src){
		if(!src)src=this.sounderr;
		if(src)this.playsound(src);
	};
	this.getaccess=function(){
		var lx = 'none';
		if(typeof(Notification)=='undefined'){
			lx='ok';
			return lx;
		}
		lx = Notification.permission;
		if(lx=='granted'){lx='ok';}else if(lx=='denied'){lx='jz';}else{lx='mr';}
		return lx;
	};
	this._showpopupie=function(msg, clsfun, can){
		if(typeof(createPopup)=='undefined')return;
		var x = window.screenLeft?window.screenLeft: window.screenX,
			y =	window.screenTop?window.screenTop: window.screenY; 
		var w = 310,h=80;
		var l = screen.width-x-w-10,
			t = screen.height-y-h-60;
		var p=window.createPopup();
		var pbody=p.document.body;
		pbody.style.backgroundColor='#f5f5f5';
		pbody.style.border= 'solid #cccccc 1px';
		msg   = msg.replace(/\n/gi,'<br>');
		var s = '<div style="cursor:pointer">';
		s+='<span id="createPopup_close" style="position:absolute;right:0px;top:0px;cursor:pointer;">×</span>';
		s+='<table id="createPopup_body"><tr valign="top">';
		s+='<td style="padding:5px"><img width="60px" src="'+can.icon+'" height="60px"><td>';
		s+='<td style="padding:0px 5px"><div style="font-size:14px;line-height:20px;padding-top:3px" align="left"><b>'+can.title+'</b></div><div style="font-size:12px;padding-top:3px;height:50px;overflow:hidden;">'+msg+'</div><td>';
		s+='</tr></table>';
		s+='</div>';
		pbody.innerHTML=s;
		p.show(l,t,w,h,document.body);
		p.document.getElementById('createPopup_close').onclick=function(){p.hide();};
		p.document.getElementById('createPopup_body').onclick=function(){
			var salx=clsfun(can);
			if(!salx)nwjs.winshow();
			p.hide();
		};
		if(can.soundbo)this.playsound(can.sound);
	};
	this.getnotifystr=function(ostr){
		var slx = '<font color="green">[已开启]</font>';
		var olx = this.getaccess();
		if(olx=='jz'){
			slx = '<font color="red">[已禁止]</font>,<a href="http://www.rockoa.com/view_notify.html" target="_blank">(去设置)</a>';
		}
		if(olx=='mr'){
			slx = '<font color="#ff6600">[未开启]</font>，<a onclick="'+ostr+'" href="javascript:;">[开启]</a>';
		}
		return slx;
	};
	this._init();
}