/**
	tabs选择卡
	caratename：chenxihu
	caratetime：214-04-15 21:40:00
	email:qqqq2900@126.com
	homepage:www.xh829.com
*/

(function ($) {
	
	function rocktabs(element, options){
		var obj		= element;
		var can		= options;
		var items	= can.items;
		var rand	= ''+parseInt(Math.random()*99999)+''; 
		var me		= this;
		this.rand	= rand;
		//初始化
		this.init	= function(){
			var len	= items.length;
			
			var s	= '';
			s+='<div class="rocktabs" id="rocktabs_'+rand+'">';
			s+='	<div class="rocktabs_title">';
			for(var i=0; i<len; i++){
				s+='	<a oi="'+i+'" id="rocktabs_'+rand+'_title'+i+'">'+items[i].title+'</a>';
			}
			s+='	</div>';
			s+='	<div class="rocktabs_cont">';
			for(var i=0; i<len; i++){
				var hc	= items[i].height;
				if(!hc)hc = obj.height()-30;
				var htm	= items[i].html;
				if(!htm)htm='';
				s+='	<div id="rocktabs_'+rand+'_cont'+i+'" style="display:none;overflow:auto;height:'+hc+'px">'+htm+'</div>';
			}
			s+='	</div>';
			s+='</div>';
			obj.html(s);
			$("[id^='rocktabs_"+rand+"_title']").click(function(){
				me.clicktitle(this);
			});
			this.clicktitle(get('rocktabs_'+rand+'_title'+can.avtchang+''));
		};
		this.clicktitle=function(o){
			var oi	= parseInt($(o).attr('oi'));
			$("[id^='rocktabs_"+rand+"_title']").removeClass();
			$("[id^='rocktabs_"+rand+"_cont']").hide();
			$('#rocktabs_'+rand+'_title'+oi+'').addClass('a1');
			$('#rocktabs_'+rand+'_cont'+oi+'').show();
			can.changetitle(oi,this,$('#rocktabs_'+rand+'_cont'+oi+'').html());
		};
		this.sethtml	= function(oi,nr,bo){
			var o = $('#rocktabs_'+rand+'_cont'+oi+'');
			if(!bo || o.html()=='')o.html(nr);
		}
	}	
	
	
	$.fn.rocktabs	= function(options){
		
		var defaultVal = {
			items:[],
			avtchang:0,
			changetitle:function(){}
		};
	
		var can = $.extend({}, defaultVal, options);
		var tabs = new rocktabs($(this), can);
		tabs.init();
		return tabs;
	};
	
})(jQuery);