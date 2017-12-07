function changesubmit(d){
	if(d.etime<=d.stime)return '截止时间必须大于开始时间';
	var st=parseFloat(d.totals);
	if(st<=0)return '时间必须大于0';
}