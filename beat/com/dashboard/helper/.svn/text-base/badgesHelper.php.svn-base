<?php

class badgesHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-1 year' ,strtotime($this->enddate)) );		
		
		$areaid = intval($this->apps->_g('areaid'));
		if($areaid!=0) $this->qAreaFilter = " AND pages.areaid IN ({$areaid}) ";
		else $this->qAreaFilter = " ";
	}
	
	function totalBadgesCollect()
	{
		$sql = "SELECT COUNT( * ) num
				FROM my_badge mb
				LEFT JOIN tbl_badge_detail detail ON mb.badgecode = detail.id
				WHERE 1      AND detail.n_status = 1
				";
		$qData = $this->apps->fetch($sql,1);
		if($qData) return $qData;
		return false;
	}

	function top10badges()
	{
		$sql = "SELECT COUNT( * ) num, detail.name, mb.badgecode
				FROM my_badge mb
				LEFT JOIN tbl_badge_detail detail ON mb.badgecode = detail.id
				WHERE 1      AND detail.n_status = 1
				AND DATE( mb.datetime ) >= '{$this->startdate}'
				AND DATE( mb.datetime ) <= '{$this->enddate}'
				GROUP BY mb.badgecode
				ORDER BY num DESC ";
	
		$qData = $this->apps->fetch($sql,1);
		 
		if(!$qData) return false;
		return $qData;
	
	}
	
	function top10userBadges()
	{
		$sql = "SELECT COUNT( * ) num, sm.name, sm.last_name, DATE( b.datetime ) dd, b.userid
				FROM my_badge b
				LEFT JOIN social_member sm ON b.userid = sm.id
					LEFT JOIN tbl_badge_detail detail ON b.badgecode = detail.id
				WHERE 1 AND detail.n_status = 1
					{$this->qAreaFilter}
				AND DATE( b.datetime ) >= '{$this->startdate}'
				AND DATE( b.datetime ) <= '{$this->enddate}'
				AND sm.n_status = 1
				GROUP BY b.userid
				ORDER BY num DESC
				LIMIT 10 ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
	
	function top10act()
	{
		$sql = "SELECT COUNT( * ) num, sm.name, sm.last_name, DATE( b.datetime ) dd
				FROM my_badge b
				LEFT JOIN social_member sm ON b.userid = sm.id
				LEFT JOIN tbl_badge_detail detail ON b.badgecode = detail.id
				WHERE 1 AND detail.n_status = 1
					{$this->qAreaFilter}
				AND DATE( b.datetime ) >= '{$this->startdate}'
				AND DATE( b.datetime ) <= '{$this->enddate}'
				AND sm.n_status = 1
				GROUP BY b.userid
				ORDER BY num DESC
				LIMIT 10 ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
	
	function getCity(){
		$sql =" 
		SELECT sm.id,sm.name ,sm.last_name 
		FROM my_pages p 
		LEFT JOIN social_member sm ON sm.id = p.ownerid
		WHERE p.masterrole = 1 AND p.type = 5 ";
		
		$qData = $this->apps->fetch($sql,1);
		if($qData)	return $qData;
		return false;
		
	}
	
	function gettopactivitybadges(){
		
		//entourage
			$sql = " 
			SELECT COUNT(1) total 
			FROM my_entourage 
			WHERE  n_status = 1 ";
			$qData['archivement'] = $this->apps->fetch($sql);
		
			// pr($sql);
		//engagemenet
		$sql = " 
					SELECT count(1) total 
					FROM my_checkin checkin 
					LEFT JOIN social_member sm ON sm.id=checkin.userid
					LEFT JOIN  my_pages pages ON sm.id=pages.ownerid
					LEFT JOIN {$this->dbshema}_news_content cnt ON cnt.id=checkin.contentid
					LEFT JOIN {$this->dbshema}_news_content_tags tags ON tags.contentid=checkin.contentid
					LEFT JOIN my_entourage e ON e.id=tags.friendid
					WHERE 
						tags.friendtype = 0 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (0) 
						AND e.referrerbybrand=tags.userid  ";
			$qData['personal engagement'] = $this->apps->fetch($sql);
		
		$sql = " SELECT COUNT(*) total 
			FROM my_checkin 
			WHERE  n_status = 1  GROUP BY userid ";
			$qData['personal checkin'] = $this->apps->fetch($sql);
			
		$sql = "SELECT count(1) total 
					FROM my_checkin checkin 
					LEFT JOIN social_member sm ON sm.id=checkin.userid
					LEFT JOIN  my_pages pages ON sm.id=pages.ownerid
					LEFT JOIN {$this->dbshema}_news_content cnt ON cnt.id=checkin.contentid
					LEFT JOIN {$this->dbshema}_news_content_tags tags ON tags.contentid=checkin.contentid
					LEFT JOIN my_entourage e ON e.id=tags.friendid
					WHERE 
						tags.friendtype = 0 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (3) 
						AND e.referrerbybrand=tags.userid ";
			$qData['brand engagement']  = $this->apps->fetch($sql);
			
		$sql = "SELECT count(1) total 
					FROM my_checkin checkin 
					LEFT JOIN social_member sm ON sm.id=checkin.userid
					LEFT JOIN  my_pages pages ON sm.id=pages.ownerid
					LEFT JOIN {$this->dbshema}_news_content cnt ON cnt.id=checkin.contentid
					LEFT JOIN {$this->dbshema}_news_content_tags tags ON tags.contentid=checkin.contentid
					LEFT JOIN my_entourage e ON e.id=tags.friendid
					WHERE 
						tags.friendtype = 0 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (2) 
						AND e.referrerbybrand=tags.userid 			
			";
			$qData['co creation engagement'] = $this->apps->fetch($sql);
			 // pr($qData);
			return $qData;
		
	}
	
	
	
	
}
?>
