<?php
class flow_carmClassModel extends flowModel
{
	public function initModel()
	{
		$this->statearr = c('array')->strtoarray('blue|办理中,green|可用,red|维修中,gray|报废');
		$this->publiarr = c('array')->strtoarray('否|gray,green|是');
	}
	
	public function flowrsreplace($rs)
	{
		if(isset($this->statearr[$rs['state']])){
			$b 			 = $this->statearr[$rs['state']];
			$rs['state'] = '<font color="'.$b[0].'">'.$b[1].'</font>';
		}
		$b 			 	= $this->publiarr[$rs['ispublic']];
		$rs['ispublic'] = '<font color="'.$b[0].'">'.$b[1].'</font>';
		return $rs;
	}

	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$key 	= $this->rock->post('key');
		if($key != '')$where.=" and `carnum` like '$key%'";
		return array(
			'where' => $where,
			'order' => 'optdt desc',
			'fields'=> 'id,carnum,carmode,state,carbrand,cartype,buydt,ispublic,qxenddt,xszenddt,syxenddt,nsenddt,status'
		);
	}
}