<?php
class inputClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_element');
	}
	
	
	public function getsubtable($modeid,$iszb=1, $hang=1, $ism=0)
	{
		if($iszb<=0)$iszb=1;
		if($hang<=0)$hang=1;
		
		$rows 	= $this->getall("`mid`='$modeid' and `iszb`=$iszb and `islu`=1",'`isbt`,`fields`,`name`','`sort`');
		if(!$rows)return '';
		$xu	 = $iszb-1;
		$str = '<table class="tablesub" id="tablesub'.$xu.'" style="width:100%;" border="0" cellspacing="0" cellpadding="0">';
		$str.='<tr>';
		$str.='<td width="10%" nowrap>序号</td>';
		foreach($rows as $k=>$rs){
			$xh = '';
			if($rs['isbt']==1)$xh='*';
			$str.='<td nowrap>'.$xh.''.$rs['name'].'</td>';
		}
		$str.='<td width="5%" nowrap>操作</td>';
		$str.='</tr>';
		for($j=0;$j<$hang;$j++){
			$str.='<tr>';
			$str.='<td >[xuhao'.$xu.','.$j.']</td>';
			foreach($rows as $k=>$rs){
				$str.='<td>['.$rs['fields'].''.$xu.','.$j.']</td>';
			}
			$str.='<td >{删,'.$xu.'}</td>';
			$str.='</tr>';
		}
		$str.='</table>';
		if($ism==0)$str.='<div style="background-color:#F1F1F1;">{新增,'.$xu.'}</div>';
		if($ism==1)$str.='<div>{新增,'.$xu.'}</div>';
		return $str;
	}
}