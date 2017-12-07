<?php
class optionClassAction extends Action
{
	public function getlistAjax()
	{
		$num	= $this->request('num');
		$name	= $this->request('name');
		$id		= $this->option->getnumtoid($num, $name, false);
		$rows	= $this->option->getall("`pid`='$id' order by `sort`, `id`");
		
		echo json_encode(array(
			'totalCount'=> $this->db->count,
			'rows'		=> $rows,
			'pid'		=> $id
		));
	}
	
	public function getfileAjax()
	{
		$mtype 	= $this->request('mtype');
		$mid 	= $this->request('mid');
		$rows 	= m('file')->getfile($mtype, $mid);
		echo json_encode($rows);
	}
	
	public function gettreedataAjax()
	{
		$num 	= $this->get('num');
		if($num=='')exit('error;');
		$pid 	= $this->option->getnumtoid($num,''.$num.'选项', false);
		$rows 	= $this->option->gettreedata($pid);
		$rows	= array(
			'rows' 	=> $rows,
			'pid'	=> $pid
		);
		$this->returnjson($rows);
	}
	
	public function deloptionAjax()
	{
		$id 	= (int)$this->post('id','0');
		$stable = $this->post('stable');
		$delbo	= true;
		if($delbo)if($this->option->rows("`pid`='$id'")>0)$delbo=false;
		if(!$delbo)$this->showreturn('','有下级分类不允许删除',201);
		$this->option->delete($id);
		if($stable!='')m($stable)->update('`typeid`=0', "`typeid`='$id'");
		$this->showreturn();
	}
}