<?php
class workClassAction extends Action
{
	public function initAction()
	{
		$flow = m('flow:project');
		$this->statearr = $flow->statearr;
	}
	
	
	public function workafter($table,$rows)
	{
		foreach($rows as $k=>$rs){
			$zt = $this->statearr[$rs['state']];
			$rows[$k]['state'] = '<font color="'.$zt[1].'">'.$zt[0].'</font>';
			$title 		= $rs['title'];
			$projectid 	= (int)$rs['projectid'];
			if($projectid>0){
				$prs 		= $this->db->getone('[Q]project', $projectid);
				if($prs){
					$title.='<br><span style="color:#888888;font-size:12px">'.$prs['title'].'('.$prs['progress'].'%)</span>';
				}
			}
			$rows[$k]['title'] = $title;
		}
		return array('rows'=>$rows);
	}
	
	
	public function projectafter($table,$rows)
	{
		$work = m('work');
		foreach($rows as $k=>$rs){
			$zt = $this->statearr[$rs['state']];
			$id = $rs['id'];
			$rows[$k]['state'] = '<font color="'.$zt[1].'">'.$zt[0].'</font>';
			$wwc= $work->rows('projectid='.$id.' and state in(0,2)');
			$wez= $work->rows('projectid='.$id.'');
			if($wwc>0)$wwc='<font color=red>'.$wwc.'</font>';
			$rows[$k]['worklist'] = ''.$wwc.'/'.$wez.'';
		}
		return array('rows'=>$rows);
	}	
}