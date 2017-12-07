<?php
class beifenClassAction extends Action
{
	
	public function chushuaAjax()
	{
		$myext		= $this->getsession('adminallmenuid');
		if(getconfig('systype')=='demo')exit('演示请勿操作');
		if($myext!='-1'){
			echo '只有管理员才可以用';
		}else{
			$tables		= explode(',', 'daily,file,flow_log,im_history,im_mess,im_messzt,infor,items,log,logintoken,meet,reads,sjoin,work,todo,flow_bill,goodss,goods,kqanay,kqdkjl,kqinfo,location,official,schedule,project,wx_agent,wx_chat,wx_dept,wx_user,userinfo,userract,hrpositive,word,hrredund,hrsalary,customer,custsale,custract,custfina,assetm,book,bookborrow,carm,carmrese');
			$alltabls 	= $this->db->getalltable();
			foreach($tables as $tabs){
				$_tabs 	= ''.PREFIX.''.$tabs.'';
				if(in_array($_tabs, $alltabls)){
					$sql1 = "delete from `$_tabs`";
					$sql2 = "alter table `$_tabs` AUTO_INCREMENT=1";
					$this->db->query($sql1, false);
					$this->db->query($sql2, false);
				}
			}
			echo 'ok';
		}
	}
	
	public function beifenAjax()
	{
		m('beifen')->start();
		echo 'ok';
	}
}