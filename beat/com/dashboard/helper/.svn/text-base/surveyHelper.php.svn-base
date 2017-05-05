<?php

class surveyHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
	}
	
	function totalPersonalPlant(){
		
		$sql = "SELECT COUNT(*) num 
				FROM beat_news_content bnc 
				LEFT JOIN social_member sm ON bnc.authorid = sm.id
				LEFT JOIN my_pages mp ON sm.id = mp.ownerid 
				WHERE 
				mp.type = 1 
				AND bnc.articleType = 5 
				AND sm.n_status  = 1 ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
	
	function totalCocreationPlant(){
		
		$sql = "SELECT COUNT(*) num 
				FROM beat_news_content bnc 
				LEFT JOIN social_member sm ON bnc.authorid = sm.id
				LEFT JOIN my_pages mp ON sm.id = mp.ownerid 
				WHERE 
				mp.type IN ( 2 ) 
				AND bnc.articleType = 5 
				AND sm.n_status  = 1 " ;
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
	
	function totalBRandPlant(){
		
		$sql = "SELECT COUNT(*) num 
				FROM beat_news_content bnc 
				LEFT JOIN social_member sm ON bnc.authorid = sm.id
				LEFT JOIN my_pages mp ON sm.id = mp.ownerid 
				WHERE 
				mp.type IN ( 3,4 ) 
				AND bnc.articleType = 5 
				AND sm.n_status  = 1 " ;
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
}
?>