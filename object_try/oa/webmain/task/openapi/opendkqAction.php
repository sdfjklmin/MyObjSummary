<?php
/**
*	将数据上传到打卡记录表上
* 	请求地址如：http://127.0.0.1/api.php?m=opendkq&openkey=key
*	请求方式：POST
*	提交过来数据[{"name":"姓名","dkdt":"2016-10-22 09:00:00"}]
*/
class opendkqClassAction extends openapiAction
{
	public function indexAction()
	{
		$str = $this->postdata;
		if(isempt($str))$this->showreturn('', 'not data', 201);
		$arr 	= json_decode($str, true);
		$oi 	= 0;$uarr = array();
		$dtobj 	= c('date');$adb 	= m('admin');$db = m('kqdkjl');
		if(is_array($arr))foreach($arr as $k=>$rs){
			$name = isset($rs['name']) ? $rs['name'] : '';
			$dkdt = isset($rs['dkdt']) ? $rs['dkdt'] : '';
			$uid  = 0;
			
			if(!isempt($name) && !isempt($dkdt)){
				if(!$dtobj->isdate($dkdt))continue;
				if(isset($uarr[$name])){
					$uid = $uarr[$name];
				}else{
					$usar 	= $adb->getrows("`name`='$name'",'id');
					if($this->db->count!=1)continue;
					$uid	= $usar[0]['id'];
					$uarr[$name] = $uid;
				}
				if($db->rows("`uid`='$uid' and `dkdt`='$dkdt'")>0)continue;
				$oi++;
				$db->insert(array(
					'uid'	=> $uid,
					'dkdt'	=> $dkdt,
					'optdt'	=> $this->now,
					'type'	=> 6
				));
			}
		}
		$this->showreturn('成功导入'.$oi.'条数据');
	}
}