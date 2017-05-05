<?php

class baRankHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = '2013-05-01';
		
		$sbaid = intval($this->apps->_g('sbaid'));
		if($sbaid!=0) $this->qSbaFilter = " AND pages.ownerid IN ({$sbaid}) ";
		else $this->qSbaFilter = " ";
		
		$brandid = intval($this->apps->_g('brandid'));
		if($brandid!=0) $this->qBrandFilter = " AND ( pages.brandid IN ({$brandid}) OR pages.brandsubid IN ({$brandid}) ) ";
		else $this->qBrandFilter = " ";
		
		$areaid = intval($this->apps->_g('areaid'));
		if($areaid!=0) $this->qAreaFilter = " AND pages.areaid IN ({$areaid}) ";
		else $this->qAreaFilter = " ";
		
	}
	
	function baRankList($start=0, $limit=10,$typefilter='', $all=false){
	
		mysql_query("SET CHARACTER SET utf8");
		$typefilter = strip_tags($this->apps->_request('activity'));
		
		// checkin
			if($typefilter=='checkin') $qData = $this->checkin($start,$limit,'',$all);
		// entourage
			if($typefilter=='entourage') $qData = $this->entourage($start,$limit,'',$all);
		// posting
			if($typefilter=='posting') $qData = $this->posting($start,$limit,'',$all);
		// comment
			if($typefilter=='comment') $qData = $this->comment($start,$limit,'',$all);
		// emoticon
			if($typefilter=='emoticon') $qData = $this->emoticon($start,$limit,'',$all);
		// plan	
			if($typefilter=='plan') $qData = $this->plandata($start,$limit,'',$all);
		// else
		
			if($typefilter=='') $qData = $this->entourage($start, $limit,'',$all);
		
		if(!$qData) return false;
		$no = 1;
		foreach($qData as $key => $val){
			$qData[$key]['no'] = $no;
			$no++;
		
		}
		return $qData;
	
	}
	
	function entourage($start=0, $limit=10,$typefilter='',$all=false){
		
		$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM `my_entourage` entourage
				LEFT JOIN social_member sm ON sm.id = entourage.referrerbybrand
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm WHERE id IN ( 4,5 )
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type =1
				AND DATE( entourage.register_date ) >= '{$this->startdate}'
				AND DATE( entourage.register_date ) <= '{$this->enddate}'
				AND entourage.n_status =1
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY `referrerbybrand`
				ORDER BY totalentourage DESC , sm.id DESC  {$qLimit}
				";
				
		// pr($sql);
		return $this->apps->fetch($sql,1);
	
	}
	
	
	function emoticon($start=0, $limit=10,$typefilter='',$all=false){
		$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city,  IF(pages.brandid=4,'AMILD',IF(pages.brandid=5,'MARLBORO','-')) brand_name
				FROM beat_news_content_favorite fav
				LEFT JOIN social_member sm ON sm.id = fav.userid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city

					WHERE pages.type = 1				   
					AND DATE( fav.date ) >= '{$this->startdate}'
					AND DATE( fav.date ) <= '{$this->enddate}'
					AND sm.n_status = 1
					{$this->qBrandFilter}
					{$this->qAreaFilter}


				GROUP BY fav.userid
				ORDER BY totalentourage DESC , sm.id DESC 
				{$qLimit}
				";
		/* $sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM beat_news_content_favorite fav
				LEFT JOIN social_member sm ON sm.id = fav.userid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm LIMIT 1
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type = 1
				AND DATE( fav.date ) >= '{$this->startdate}'
				AND DATE( fav.date ) <= '{$this->enddate}'
				AND sm.n_status = 1
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY fav.userid
				ORDER BY totalentourage DESC , sm.id DESC LIMIT {$start},{$limit}";*/
				// pr($sql);
		return $this->apps->fetch($sql,1);
	}
	
	function comment($start=0, $limit=10,$typefilter='',$all=false){
		$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM beat_news_content_comment comment
				LEFT JOIN social_member sm ON sm.id = comment.userid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type =1
				AND DATE( comment.date ) >= '{$this->startdate}'
				AND DATE( comment.date ) <= '{$this->enddate}'
				AND sm.n_status =1
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY comment.userid
				ORDER BY totalentourage DESC , sm.id DESC {$qLimit}";
				// pr($sql);
		return $this->apps->fetch($sql,1);
	}
	
	function posting($start=0, $limit=10,$typefilter='',$all=false){
		$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM beat_news_content content
				LEFT JOIN social_member sm ON sm.id = content.authorid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type =1
				AND DATE( content.posted_date ) >= '{$this->startdate}'
				AND DATE( content.posted_date ) <= '{$this->enddate}'
				AND sm.n_status =1
				AND content.articleType <> 5
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY content.authorid
				ORDER BY totalentourage DESC , sm.id DESC {$qLimit}";
				// pr($sql);
		return $this->apps->fetch($sql,1);
	}
	
	function plandata($start=0, $limit=10,$typefilter='',$all=false){
		$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM beat_news_content content
				LEFT JOIN social_member sm ON sm.id = content.authorid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type =1
				AND DATE( content.posted_date ) >= '{$this->startdate}'
				AND DATE( content.posted_date ) <= '{$this->enddate}'
				AND sm.n_status =1
				AND content.articleType = 5
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY content.authorid
				ORDER BY totalentourage DESC , sm.id DESC {$qLimit}";
				// pr($sql);
		return $this->apps->fetch($sql,1);
	}
	
	function checkin($start=0, $limit=10,$typefilter='',$all=false){
			$qLimit  = " LIMIT {$start},{$limit}";
		if($all==true) $qLimit  = "  ";
		$sql = "
				SELECT count( * ) totalentourage, sm.id, sm.name, sm.last_name, sm.img, bcr.city, brandsm.name brand_name
				FROM my_checkin checkin
				LEFT JOIN social_member sm ON sm.id = checkin.userid
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				LEFT JOIN beat_city_reference bcr ON bcr.id = pages.city
				LEFT JOIN (
					SELECT bsm.id, bsm.name, bsm.last_name FROM social_member bsm
				) brandsm ON brandsm.id=pages.brandid
				WHERE pages.type =1
				AND DATE( checkin.join_date ) >= '{$this->startdate}'
				AND DATE( checkin.join_date ) <= '{$this->enddate}'
				AND sm.n_status =1
				{$this->qBrandFilter}
				{$this->qAreaFilter}
				GROUP BY checkin.userid
				ORDER BY totalentourage DESC , sm.id DESC {$qLimit}";
				// pr($sql);
		return $this->apps->fetch($sql,1);
	}
	
	function getCount($start=0, $limit=10,$typefilter=''){
	
	$this->startdate = '2013-06-01';
	$sql =  "	SELECT COUNT(*) total
				FROM my_entourage me
				LEFT JOIN social_member sm ON sm.id = me.referrerbybrand
				LEFT JOIN my_pages pages ON sm.id = pages.ownerid
				WHERE pages.type =1
				AND me.n_status =1
				AND DATE( me.register_date ) >= '{$this->startdate}'
				AND DATE( me.register_date ) <= '{$this->enddate}'";
	
	// pr($sql);
	$qData = $this->apps->fetch($sql);
	return $qData['total'];
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
	
	function getBrand(){
		$sql =" 
		SELECT  sm.id,sm.name ,sm.last_name 
		FROM my_pages  p 
		LEFT JOIN social_member sm ON sm.id = p.ownerid
		WHERE p.masterrole = 1 AND p.type = 3 ";
		
		$qData = $this->apps->fetch($sql,1);
		if($qData)	return $qData;
		return false;
		
	}
	
	function getrecepient($type='all'){
		// pr($type);
			$socialData = false;
			if($type=='all'){
				$auhtorminion = @$this->apps->user->badetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->pldetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->branddetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->areadetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
			}else{
				$data = @$this->apps->user->$type;
				if($data){
					foreach($data as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}
					}
				}
			}
			
			return $socialData;
	
	}
	
}
?>