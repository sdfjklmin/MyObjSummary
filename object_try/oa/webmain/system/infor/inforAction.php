<?php
class inforClassAction extends Action
{

	public function publicbeforesave($table, $cans, $id)
	{
		$num = $cans['num'];
		if($num=='')$num = $this->db->ranknum($this->T($table),'num');
		return array('rows'=>array('num'=>$num));
	}
	
	public function loaddataAjax()
	{
		$id		= (int)$this->get('id');
		$data	= m('infor')->getone($id);
		$arr 	= array(
			'data'		=> $data,
			'infortype' 	=> $this->option->getdata('infortype')
		);
		echo json_encode($arr);
	}
	
}