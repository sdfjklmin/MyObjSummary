var im={
	minid:999999999,
	init:function(){
		strformat.emotspath='web/';
		this.type = receinfor.type;
		this.gid = receinfor.gid;
		this.showobj  = $('#showview');
		this.inputobj = $('#contentss');
		$('#btn').click(function(){
			im.sendcont();
		});
		this.inputobj.keydown(function(e){
			if(e.keyCode==13){
				im.sendcont();
				return false;
			};
		});
		this.loaddata();
	},
	loaddata:function(o1, iref){
		var iref = (!iref)?false:true;
		var minid= 0;
		if(iref)minid=this.minid;
		if(o1)$(o1).html('<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> 加载中...');
		js.ajax('reim','getrecord',{type:this.type,gid:this.gid,minid:minid},function(ret){
			if(o1)$(o1).html('');
			im.loaddatashow(ret, iref);
		},'none');
	},
	loaddatashow:function(ret,isbf){
		var a = ret.rows;
		var i,len = a.length,cont,lex,nas,fase,nr,d,na=[],rnd;
		$('#loadmored').remove();
		if(isbf){
			if(len>0)this.showobj.prepend('<div class="showblanks">---------↑以上是新加载---------</div>');
			na = a;
		}else{
			for(i= len-1; i>=0; i--)na.push(a[i]);
		}
		for(i= 0; i<len; i++){
			d   = na[i];
			lex = 'right';
			nas = '我';
			fase= adminface;
			if(d.sendid!=adminid){
				lex='left';
				nas= d.sendname;
				fase= d.face;
			}
			nr  = this.contshozt(d.filers);
			if(nr=='')nr= jm.base64decode(d.cont);
			rnd = 'mess_'+d.id+'';
			cont= strformat.showqp(lex,nas,d.optdt,nr ,'', fase, rnd);
			if(!isbf){
				this.addcont(cont, isbf);
			}else{
				this.showobj.prepend(cont);
			}
			if(d.id<this.minid)this.minid=d.id;
		}
		if(len>0){
			var s = '<div id="histordiv" class="showblanks" >';
			if(ret.wdtotal==0){
				s+='---------↑以上是历史记录---------';
				if(len>=5){
					this.showobj.prepend('<div id="loadmored" class="showblanks" ><a href="javascript:;" onclick="im.loadmoreda(this)">点击加载更多...</a></div>');
				}
			}else{
				s+='---↑以上是历史,还有未读信息'+ret.wdtotal+'条,<a href="javascript:;" onclick="im.loaddata(this)">点击加载</a>---';
			}
			s+='</div>';
			if(!isbf)this.addcont(s);
			if(isbf)this._addclickf();
		}
	},
	loadmoreda:function(o1){
		this.loaddata(o1, true);
	},
	addcont:function(cont, isbf){
		var o	= this.showobj;
		if(cont){if(isbf){o.prepend(cont);}else{o.append(cont);}}
		clearTimeout(this.scrolltime);
		this.scrolltime	= setTimeout(function(){
			$('body,html').animate({scrollTop:get('showview').scrollHeight},100);
			im._addclickf();
		}, 50);
	},
	_addclickf:function(){
		var o = this.showobj.find('img[fid]');
		o.unbind('click');
		o.click(function(){
			im.clickimg(this);
		});
	},
	clickimg:function(o1){
		var o=$(o1);
		var fid=o.attr('fid');
		var src = o1.src.replace('_s.','.');
		$.imgview({url:src,ismobile:true});
	},
	contshozt:function(d){
		var s='',slx;
		if(d){
			if(!d.fileid)d.fileid=d.id;
			if(js.isimg(d.fileext)){
				s='<img src="'+apiurl+''+d.thumbpath+'" fid="'+d.fileid+'">';
			}else{
				slx = d.fileext;
				if(js.fileall.indexOf(','+slx+',')<0)slx='wz';
				s='<img src="web/images/fileicons/'+slx+'.gif" align="absmiddle">&nbsp;'+d.filename+'<br><a href="javascript:;" onclick="js.downshow('+d.fileid+')">下载</a>&nbsp;'+d.filesizecn+'';
			}
		}
		return s;
	},
	sendcont : function(ssnr){
		if(js.ajaxbool)return;
		js.msg('none');
		var o	= this.inputobj;
		var nr	= strformat.sendinstr(o.val());
		nr		= nr.replace(/</gi,'&lt;').replace(/>/gi,'&gt;').replace(/\n/gi,'<br>');
		if(ssnr)nr=ssnr;
		if(isempt(nr))return false;
		var conss = jm.base64encode(nr);
		if(conss.length>500){
			js.msg('msg','发送内容太多了');
			return;
		}
		var nuid= js.now('time'),optdt = js.serverdt();
		var cont= strformat.showqp('right','我',optdt, nr, nuid, adminface);
		this.addcont(cont);
		o.val('');
		this.sendconts(conss, nuid, optdt, 0);
		return false;
	},
	sendconts:function(conss, nuid, optdt, fid){
		var d 	 = {cont:conss,gid:this.gid,type:this.type,nuid:nuid,optdt:optdt,fileid:fid};
		js.ajax('reim','sendinfor',d,function(ret){
			im.sendsuccess(ret,nuid);
		},'none',false,function(){
			im.senderror(nuid);
		});
	},
	senderror:function(nuid){
		js.ajaxbool = false;
		get(nuid).src='images/error.png';
		get(nuid).title='发送失败';
	},
	sendsuccess:function(d,nuid){
		this.bool = false;
		if(!d.id){
			this.senderror(nuid);
			return;
		}
		$('#'+d.nuid+'').remove();
		var bo = false;
		d.messid=d.id;
		d.face  = adminface;
	}
}