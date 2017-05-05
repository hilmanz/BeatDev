<?php

class topVenueHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		$this->rateType = strip_tags($this->apps->_g('rateType'));	
	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
		$this->qRateType = " ORDER BY total DESC, reach DESC  ";		
		if($this->rateType=='rating') $this->qRateType = " ORDER BY  rating DESC,total DESC ";		
		if($this->rateType=='prize') $this->qRateType = " ORDER BY prize DESC,total DESC ";		
		if($this->rateType=='smoking') $this->qRateType = " ORDER BY  smoking DESC,total DESC ";		
		if($this->rateType=='wifi') $this->qRateType = " ORDER BY wifi DESC,total DESC ";		
		
	}
	
	function topVisitedPage(){
	
		$sql = "SELECT COUNT( * ) num, action_value, DATE( date_time ) dd
				FROM tbl_activity_log
				WHERE action_id =6 AND DATE( date_time ) >= '{$this->startdate}'
				AND DATE( date_time ) <= '{$this->enddate}'
				GROUP BY action_value ORDER BY num DESC LIMIT 5";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
	
	}
	
	function top10venue(){
		$searchCity = strip_tags($this->apps->_g('searchCity'));
		if($searchCity!='') {
			 
			$this->qCityFilter = " AND master.city = '{$searchCity}' ";
			 
		}else $this->qCityFilter = " ";
		
	 $sql = "
		
			SELECT COUNT( * ) total, 
			AVG(rating) rating , 
			AVG(prize)  prize, 
			IF( SUM(wifi)  >= (COUNT( * ) - SUM(wifi)),1,0)  wifi , 
			IF( SUM(smoking) >= (COUNT( * ) - SUM(smoking)),1,0) smoking , 
			checkin.venueid, 
			master.venuename, 
			master.id,master.venuecategory, 
			master.city,
			b.total reach
			FROM my_checkin checkin
			LEFT JOIN beat_venue_master master ON checkin.venueid=master.id
			LEFT JOIN(
				SELECT COUNT(*) total , venueid
				FROM (
					SELECT COUNT(*) total,userid,venueid
					FROM my_checkin
					WHERE venueid <> 0 AND ( rating <>0 OR prize <> 0 ) 
					GROUP BY userid,venueid
				) a
				GROUP BY venueid
			) b ON b.venueid = checkin.venueid
			WHERE checkin.venueid<>0  AND ( checkin.rating <>0 OR checkin.prize <> 0 ) {$this->qCityFilter}
			GROUP BY checkin.venueid
			{$this->qRateType} LIMIT 10 ";
			// pr($sql);
	 $qData = $this->apps->fetch($sql,1);
	 if(!$qData) return false;
		 $no = 1;
		 foreach ($qData as $key => $val){
			$qData[$key]['no'] = $no;
			$no++;
		 }
		 
	 // pr($sql);
	 return $qData;
	
	}
	
	function getVenueData(){
		
		global $CONFIG;
		$venueid = $this->apps->_request('venueid');
		$sql =
		"
		SELECT 
			COUNT( * ) total, 
			AVG(mc.rating) rating , 
			AVG(mc.prize)  prize, 
			IF( SUM(mc.wifi)  >= (COUNT( * ) - SUM(mc.wifi)),1,0)  wifi , 
			IF( SUM(mc.smoking) >= (COUNT( * ) - SUM(mc.smoking)),1,0) smoking ,
			mc.contentid,
			vm.address,
			vm.venuename,
			vm.city,
			vc.name categoryname,
			mc.venue,
			nc.title,
			nc.brief,
			nc.content,
			nc.image			
		FROM 
		my_checkin mc
		LEFT JOIN beat_news_content nc ON nc.id=mc.contentid
		LEFT JOIN beat_venue_master vm ON vm.id=mc.venueid
		LEFT JOIN beat_venue_category vc ON vm.venuecategory=vc.id
		WHERE 
		mc.venueid = {$venueid}
		GROUP BY mc.contentid
		";
		
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		
		$no = 1;
		$cid = false;
		foreach ($qData as $key => $val){
			$qData[$key]['imagepath'] = false;
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";		 
			if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
			else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";		
			
			$cid[$val['contentid']]=$val['contentid'];
		
		}
		if($cid) $strCid = implode(',',$cid);
		$data['lists'] = $qData;		
		$data['cid'] = $strCid;
	 	return $data;
		
	}
	
	function top10venueReport($all=false){
		$qLimit  = " LIMIT 10 ";
		if($all==true) $qLimit  = "  ";
		$sql = "
		
			SELECT COUNT( * ) total, 
			AVG(rating) rating , 
			AVG(prize)  prize, 
			IF( SUM(wifi)  >= (COUNT( * ) - SUM(wifi)),1,0)  wifi , 
			IF( SUM(smoking) >= (COUNT( * ) - SUM(smoking)),1,0) smoking , 
			checkin.venueid, 
			master.venuename, 
			master.id,master.venuecategory, 
			master.city,
			b.total reach
			FROM my_checkin checkin
			LEFT JOIN beat_venue_master master ON checkin.venueid=master.id
			LEFT JOIN(
				SELECT COUNT(*) total , venueid
				FROM (
					SELECT COUNT(*) total,userid,venueid
					FROM my_checkin
					WHERE venueid <> 0 AND ( rating <>0 OR prize <> 0 ) 
					GROUP BY userid,venueid
				) a
				GROUP BY venueid
			) b ON b.venueid = checkin.venueid
			WHERE checkin.venueid<>0  AND ( checkin.rating <>0 OR checkin.prize <> 0 )
			GROUP BY checkin.venueid
			{$this->qRateType} {$qLimit} ";
			// pr($sql);
		 $qData = $this->apps->fetch($sql,1);
		 if(!$qData) return false;
			 $no = 1;
			 foreach ($qData as $key => $val){
				$qData[$key]['no'] = $no;
				$no++;
			 }
			 
		 // pr($sql);
		 return $qData;
	}
}

?>