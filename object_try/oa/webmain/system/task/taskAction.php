<?php
class taskClassAction extends Action
{
	
	public function getrunlistAjax()
	{
		$barr = m('task')->getlistrun($this->date);
		$this->returnjson($barr);
	}
	public function starttaskAjax()
	{
		$msg = m('task')->starttask();
		if(contain($msg, 'ok')){
			echo 'ok';
		}else{
			echo '无法启动';
		}
	}
	
	public function clearztAjax()
	{
		m('task')->update('state=0,lastdt=null,lastcont=null','1=1');
	}
}