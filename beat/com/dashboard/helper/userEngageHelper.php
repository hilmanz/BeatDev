<?php

class userEngageHelper {

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
	
	function totalUserEngage(){
	
		$sql = "
				SELECT count(*) baengagement, DATE(checkin.join_date) dd
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
					AND ( cnt.articleType = 5  OR cnt.categoryid IN (2,3) )
					AND DATE( checkin.join_date ) >= '{$this->startdate}'
					AND DATE( checkin.join_date ) <= '{$this->enddate}'
					{$this->qSbaFilter}
					{$this->qAreaFilter}
					{$this->qBrandFilter}	
					AND e.referrerbybrand=tags.userid 
				GROUP BY dd  
				ORDER BY dd DESC 
				";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($qData);
		if(!$qData) return false;
		 $nextnum = 0;
		foreach($qData as $key => $val){
			$qData[$key]['baengagement']+=$nextnum;
			$nextnum = $qData[$key]['baengagement'];
		}
		// pr($qData);
		/* fixed date format */
		if($qData){
		$newdata = $this->fixeddate($qData,'dd','baengagement','accumulation',$this->startdate,$this->enddate);
		if($newdata) $data = $newdata ;
		else $data = $qData ;
		}else {
				$arrdata = false;
				$arrdata[0]['dd'] = date('Y-m-d');
				$arrdata[0]['baengagement'] = 0;			 
				$newdata = $this->fixeddate($arrdata,'dd','baengagement','accumulation');
				// pr($newdata);
				if($newdata)$data = $newdata ;
		}
		if($data)	return $data;
		else return false;
	
	}
	
	function personalPlant(){
	
		$sql = "
				 
					SELECT count(*) num 
					FROM  {$this->dbshema}_news_content cnt
				
					LEFT JOIN social_member sm   ON cnt.authorid=sm.id 
						LEFT JOIN  my_pages pages ON sm.id=pages.ownerid
					 
					WHERE 
					 
						  cnt.n_status = 1 
					 
						AND  cnt.articleType = 5  AND cnt.categoryid =0 
						 
						{$this->qSbaFilter}
						{$this->qAreaFilter}
						{$this->qBrandFilter} 
				 
				";
			// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
	
	}
	
	function personalEngagement(){
	
		$sql = "
		SELECT SUM(num) num FROM (
				SELECT count(*) num, DATE(checkin.join_date) dd
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
					AND  cnt.articleType = 5  AND cnt.categoryid =0 
					AND DATE( checkin.join_date ) >= '{$this->startdate}'
					AND DATE( checkin.join_date ) <= '{$this->enddate}'
					{$this->qSbaFilter}
					{$this->qAreaFilter}
					{$this->qBrandFilter}	
					AND e.referrerbybrand=tags.userid 
				GROUP BY dd  
				ORDER BY dd DESC 
		) personaleng
				";
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
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
	function fixeddate($data = false,$datedayindex='dd',$valueindex='total',$accumulation='',$startdate=false,$enddate=false){
	if($data==false )return false;
	if($startdate==false) $mindate = strtotime($this->startdate);
	else  $mindate = strtotime($startdate);
	
	if($enddate==false) $maxdate = strtotime($this->enddate);
	else $maxdate = strtotime($enddate);
	
	$totaldate = ($maxdate - $mindate) / (60*60*24);
	$arrdata = false;
	// pr($data);
	foreach($data as $key => $val) {		
		$arrdata[$val[$datedayindex]] = $val[$valueindex];
	}

	// pr($mindate);
	// pr($arrdata);
	
	if(!$arrdata) return false;
		$newdata = false;
		for($i=0;$i<=$totaldate;$i++){
		// pr($totaldate);
			$dates = date("Y-m-d",$mindate);
			$val = date("Y-m-d" , strtotime("{$dates} +{$i} day"));
			// pr($val);		 	
				if(!array_key_exists($val,$arrdata)) $arrdata[$val] = 0;				
		}	
		$n = 0;
		$lastvalue = 0;
		ksort($arrdata);
		// pr($arrdata);
		foreach($arrdata as $key => $val){
			$newdata[$n][$datedayindex] = $key;
	 
			if($accumulation=='accumulation') {
				if($val==0)$val = $lastvalue;	
				$newdata[$n][$valueindex] =  $val;						 
				$lastvalue = $val;
			}	else $newdata[$n][$valueindex] =$val;
			
			$n++;
		}
		if($newdata)return $newdata;
		else return false;
	}
}
?>