//初始函数
function initbodys(){
	if(mid==0)loadinstyrs();
}
function loadinstyrs(){
	js.ajax(geturlact('getuserinfo'),{},function(d){
		if(d){
			form('ranking').value=d.ranking;
			form('entrydt').value=d.workdate;
		}
	},'get,json');
}