<?php 
class indexClassAction extends Action{
	
	public function defaultAction()
	{
		$afrom 			= $this->get('afrom');
		$this->tpltype	= 'html';
		$my			= $this->db->getone('[Q]admin', "`id`='$this->adminid'",'`face`,`id`,`name`,`ranking`,`deptname`,`type`,`style`');
		$allmenuid	= '-1';
		if($my['type'] != 1)$allmenuid	= $this->getuserext($this->adminid);
		
		$mewhere	= '';
		$isadmin	= 1;
		$myext		= $allmenuid;
		if($myext != '-1'){
			$isadmin	= 0;	
			$mewhere	= ' and `id` in('.str_replace(array('[',']'), array('',''), $myext).')';
		}
		$this->rock->savesession(array(
			'adminallmenuid'	=> $allmenuid,
			'isadmin'			=> $isadmin
		));
		$this->smartydata['topmenu'] 	= m('menu')->getall("`pid`=0 and `status`=1 $mewhere order by `sort`");
		
		
		
		$this->smartydata['my']			= $my;
		$this->smartydata['afrom']		= $afrom;
		$this->smartydata['face']		= $this->rock->repempt($my['face'], 'images/noface.png');
		$this->smartydata['style']		= $this->rock->repempt($my['style'], '0');
	}
	
	
	public function getmenuAjax()
	{
		$pid 	= $this->get('pid');
		$this->menuwhere = '';
		$myext	= $this->getsession('adminallmenuid');
		if($myext != '-1'){	
			$this->menuwhere	= ' and `id` in('.str_replace(array('[',']'), array('',''), $myext).')';
		}
		$arr	= $this->getmenu($pid);
		echo json_encode($arr);
	}
	
	private function getmenu($pid)
	{
		$menu	= m('menu')->getall("`pid`='$pid' and `status`=1 $this->menuwhere order by `sort`",'id,num,url,icons,name');
		$rows	= array();
		foreach($menu as $k=>$rs){
			$num			= $rs['num'];
			$sid			= $rs['id'];
			$icons			= $rs['icons'];
			if($this->rock->isempt($num))$num = 'num_'.$sid;
			if($this->rock->isempt($icons))$icons = 'bookmark-empty';
			$rs['icons']	= $icons;
			$rs['num']		= $num;
			$children		= $this->getmenu($sid);
			$rs['children']	= $children;
			$rs['stotal']	= count($children);
			$rows[] = $rs;
		}
		return $rows;
	}
	
	
	
	
	/**
		查看菜单权限
	*/	
	private function getuserext($uid)
	{
		$guid 	= '-1';
		if($this->adminuser=='admin')return $guid;
		$gasql	= " ( id in( select `sid` from `[Q]sjoin` where `type`='ug' and `mid`='$uid') or id in( select `mid` from `[Q]sjoin` where `type`='gu' and `sid`='$uid') )";//用户所在组id
		$gsql	= "select `id` from `[Q]group` where $gasql ";
		$owhe	= " and (`id` in(select `sid` from `[Q]sjoin` where ((`type`='um' and `mid`='$uid') or (`type`='gm' and `mid` in($gsql) ) ) ) or `id` in(select `mid` from `[Q]sjoin` where ((`type`='mu' and `sid`='$uid') or (`type`='mg' and `sid` in($gsql) )) ))";
		if($this->db->rows('`[Q]group`',"`ispir`=0 and $gasql")>0){	//不用权限验证的用户
			$owhe=''; 
			return $guid;
		}
		$guid	= '[0]';
		if($owhe != ''){
			$arss	= $this->db->getall("select `id`,`pid`,(select `pid` from `[Q]menu` where `id`=a.`pid`)as `mpid` from `[Q]menu` a where (`status` =1 $owhe) or (`status` =1 and `ispir`=0) order by `sort`");
			foreach($arss as $ars){
				$guid.=',['.$ars['id'].']';
				$bpid	= $ars['pid'];
				$bmpid	= $ars['mpid'];
				if(!$this->rock->contain($guid, '['.$bpid.']')){
					$guid.=',['.$bpid.']';
				}
				if(!$this->rock->isempt($bmpid)){
					if(!$this->rock->contain($guid, '['.$bmpid.']')){
						$guid.=',['.$bmpid.']';
					}
				}
			}
		}
		return $guid;
	}
	
	public function testAction()
	{
		$this->display = false;
		//echo m('view')->viewwhere(3,3);
		//m('beifen')->start();
		//m('flow')->submit('leave', '1', '提交');
		//$a = m('reim')->asynurl('asynrun','index', false, time()+12);
		//$body = c('curl')->getcurl('http://www.rockoa.com/images/web/weblogo1.png');
		//file_put_contents('we.png',$body);
		//echo m('weixin:media')->upload('we.png');
		
		//$mid = m('kaoqin')->kqanay($this->adminid, '2016-08-03');
		//m('schedule')->getdtdata();
		//print_r($mid);
	}
	
	public function downAction()
	{
		$this->display = false;
		$id  = (int)$this->jm->gettoken('id');
		m('file')->show($id);
	}
	
	public function getFilodes($path, $lx=0)
	{
		$arr	= array();
		$paths	= $path;
		if(!is_dir($paths))return ;
		$d 		= opendir($paths);
		$farrs	= array();
		$farra	= array();
		$str 	= '';
		for($i=0;$i<$lx;$i++)$str.='&nbsp;├ ';
		
		while( false !== ($file = readdir($d))){
			if($file != '.'  &&  $file!='..'){//遍历目录下文件夹
				$pafile	= $paths.'\\'.$file;
				
				//echo $file;
				//echo '<br>';
				if(is_dir($pafile)){
					$farrs[] = $file;
					
				}
				if(is_file($pafile)){	
					$farra[] = $file;
				}
			}
		}
		
		foreach($farrs as $f){
			echo $str;
			echo $f;
			echo '<br>';
			$apath   = $path.''.$f.'/';
			$this->getFilodes($apath, $lx+1);
		}
		
		foreach($farra as $f){
			echo $str;
			echo $f;
			echo '<br>';
		}
		
	}
}