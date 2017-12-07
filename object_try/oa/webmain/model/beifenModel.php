<?php
class beifenClassModel extends Model
{
	public function start()
	{
		$alltabls 	= $this->db->getalltable();
		$data = array();
		foreach($alltabls as $tabs){
			$rows  	= $this->db->getall('select * from '.$tabs.'');
			$fields	= $this->db->gettablefields($tabs);
			$data[$tabs] = array(
				'fields' 	=> $fields,
				'data'		=> $rows
			);
		}
		$rnd  = str_shuffle('abcedfghijk').rand(1000,9999);
		$file = 'databat_'.date('Ymd').'_'.$rnd.'.json';
		$this->rock->createtxt('upload/data/'.$file.'', json_encode($data));
		return true;
	}
	
	
	public function updatefabric($cont)
	{
		$bos 	= $this->updatefabricfile($cont);
		if(!$bos)return 'err:'.$this->db->error();
		return 'ok';
	}
	
	public function updatefabricfile($cont='')
	{
		if($cont=='')return false;
		$data = json_decode($cont, true);
		foreach($data as $tabe=>$da){
			$table 	= str_replace('xinhu_', PREFIX, $tabe);
			$fields = $da['fields'];
			$nowfiel= $this->getfieldsa($table);   
			$str 	= '';
			$sql	= '';
			if(!$nowfiel){
				$str  = '`id` int(11) NOT NULL AUTO_INCREMENT';
				foreach($fields as $k=>$frs){
					$fname = $frs['name'];
					$nstr  = $this->getfielstr($frs);
					if($fname!='id')$str.=','.$nstr.'';
				}
				$str .=',PRIMARY KEY (`id`)';
				$sql  = "CREATE TABLE `$table`($str)ENGINE=MyISAM DEFAULT CHARSET=utf8";
			}else{
				foreach($fields as $k=>$frs){
					$fname = $frs['name'];
					$nstr  = $this->getfielstr($frs);
					if(!isset($nowfiel[$fname])){
						$str.=',add '.$nstr.'';
					}else{
						$ostr = $this->getfielstr($nowfiel[$fname]);
						if($nstr != $ostr){
							$str.=',MODIFY '.$nstr.'';
						}
					}
				}
				if($str!=''){
					$str = substr($str, 1);
					$sql = "alter table `$table` $str";
				}
			}
			if($sql!=''){
				$bo  = $this->db->query($sql);
				if($bo)$this->rock->debugs($sql, 'upgmysql');
				if(!$bo)return false;
			}
		}
		return true;
	}
	private function getfieldsa($table)
	{
		$nowfiel= $this->db->gettablefields($table);
		$a 		= array();
		foreach($nowfiel as $k=>$rs){
			$a[$rs['name']] = $rs;
		}
		return $a;
	}
	private function getfielstr($rs)
	{
		$str 	= '`'.$rs['name'].'` '.$rs['types'].'';
		$dev 	= $rs['dev'];
		$isnull = $rs['isnull'];
		if($isnull=='NO')$str.=' NOT NULL';
		if(is_null($dev)){
			if($isnull != 'NO')$str.=' DEFAULT NULL';
		}else{
			$str.=" DEFAULT '$dev'";
		}
		if(!isempt($rs['explain']))$str.=" COMMENT '".$rs['explain']."'";
		return $str;
	}
}