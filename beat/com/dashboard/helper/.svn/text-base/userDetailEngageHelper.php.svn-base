<?php

class userDetailEngageHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
		
	}
	
	function entourageEngage($start=0, $limit=10){
	
		$sql = "SELECT id,name,last_name,email,img,SUM(engagement) baengagement FROM
					(
					SELECT e.id,e.name,e.last_name,e.email,e.img,  IF(stat.total IS NULL,0,stat.total) engagement 
					FROM my_entourage e 
					LEFT JOIN 
						( 
						SELECT count(*) total,tags.id,tags.friendid
						FROM beat_news_content_tags tags
						LEFT JOIN beat_news_content content ON content.id = tags.contentid 
						WHERE  
						tags.n_status=1 
						AND tags.friendtype = 0  
						AND content.articleType=5
						AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=tags.contentid AND n_status = 1 )
						GROUP BY tags.friendid 
						) 
						stat ON stat.friendid= e.id 	 
					)a
				GROUP BY id  ORDER BY engagement DESC LIMIT {$start},{$limit}";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return $false;
		return $qData;
	
	}
	
	function totalEntourage($start=0, $limit=10){
		$sql = "SELECT COUNT( * ) total
				FROM (

				SELECT e.id, e.name, e.last_name, e.email, e.img, IF( stat.total IS NULL , 0, stat.total ) engagement
				FROM my_entourage e
				LEFT JOIN (

				SELECT count( * ) total, tags.id, tags.friendid
				FROM beat_news_content_tags tags
				LEFT JOIN beat_news_content content ON content.id = tags.contentid
				WHERE tags.n_status =1
				AND tags.friendtype =0
				AND content.articleType =5
				AND EXISTS (

				SELECT contentid
				FROM my_checkin checkin
				WHERE checkin.contentid = tags.contentid
				AND n_status =1
				)
				GROUP BY tags.friendid
				)stat ON stat.friendid = e.id
				)a";
		$qData = $this->apps->fetch($sql);
		return $qData['total'];
	}
	
}

?>