<?php

class dataHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		
		$startdate = $this->apps->_g('startdate');
		$enddate =  $this->apps->_g('enddate');	
		if($startdate) $startdate = date('Y-m-d',strtotime($startdate));
		if($enddate) $enddate = date('Y-m-d',strtotime($enddate));
		$this->startdate =$startdate;
		$this->enddate = $enddate;
		
		$this->monthdate = intval($this->apps->_g('monthdate'));	
		if($this->monthdate==0){
			$this->qmonthdate = "AND MONTH( NOW() ) = MONTH( me.register_date ) ";
		}else {
			$this->qmonthdate = " AND MONTH( me.register_date ) = '{$this->monthdate}' ";
		}
		
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
		
		$this->startdatesub = $this->apps->_g('startdatesub');
		$this->enddatesub = $this->apps->_g('enddatesub');	
		
		if($this->enddatesub=='') $this->enddatesub = date('Y-m-d');		
		if($this->startdatesub=='') $this->startdatesub = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddatesub)) );
		
		
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
	 
	
	function weekAchievmentKpi(){
		
	
		
		$sql = "SELECT COUNT( * ) num, DATE(me.register_date) dd
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status
				IN ( 1 )
				AND sm.n_status =1 AND DATE( me.register_date ) >= '{$this->startdate}'
				AND DATE( me.register_date ) <= '{$this->enddate}' 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}
				GROUP BY dd
				ORDER BY dd, num DESC ";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		if($qData){
		$newdata = $this->fixeddate($qData,'dd','num');
		if($newdata) $data = $newdata ;
		else $data = $qData ;
		}else {
				$arrdata = false;
				$arrdata[0]['dd'] = date('Y-m-d');
				$arrdata[0]['num'] = 0;			 
				$newdata = $this->fixeddate($arrdata,'dd','num');
				// pr($newdata);
				if($newdata)$data = $newdata ;
		}
		if($data)	return $data;
		else return false;
	}
	
	function weeklyCumulativeEngage(){
		
	
		$sql = "SELECT SUM(num) num , MIN(dd) dd
				FROM (
					SELECT COUNT( * ) num, DATE(me.register_date) dd
					FROM my_entourage me
					LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
					LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
					WHERE me.n_status
					IN ( 1 )
					AND sm.n_status =1 {$this->qmonthdate} 
					{$this->qSbaFilter}
					{$this->qAreaFilter}
					{$this->qBrandFilter}
					GROUP BY dd
					ORDER BY dd, num DESC 
				) a 
				GROUP BY WEEK(dd)
				ORDER BY WEEK(dd)
				";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$n = 1;
		foreach($qData as $key => $val){
			$qData[$key]['dd'] =" WEEK ".$n++;
		}
		
		
		return $qData;
		// $nextnum = 0;
		// foreach($qData as $key => $val){
			// $qData[$key]['num']=$nextnum;
			// $nextnum = $qData[$key]['num'];
		// }
 
		// if($qData){
		// $newdata = $this->fixeddate($qData,'dd','num','');
		// if($newdata) $data = $newdata ;
		// else $data = $qData ;
		// }else {
				// $arrdata = false;
				// $arrdata[0]['dd'] = date('Y-m-d');
				// $arrdata[0]['num'] = 0;			 
				// $newdata = $this->fixeddate($arrdata,'dd','num','');
			
				// if($newdata)$data = $newdata ;
		// }
		// if($data)	return $data;
		// else return false;
	
	}
	
	function brandPref(){
		
		$sql = "SELECT code,brandtype FROM tbl_brand_preferences_references ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData)return false;
		$competitorarr = array();
		$pmiarr = array();
		$ourarr = array();
		foreach($qData as $val){
			if($val['brandtype']==0) $competitorarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==1) $pmiarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==2) $ourarr[(string)$val['code']] =(string)$val['code'];
		}
		
		$sql = "SELECT COUNT( * ) total, me.Brand1_ID
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status
				IN ( 1 )
				AND sm.n_status =1 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}						 
				GROUP BY me.Brand1_ID
				ORDER BY total";
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		
		foreach($qData as $key => $val){
				$qData[$key]['brandname'] = "Our";
				if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['brandname'] = "Competitor";				
				if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['brandname'] = "PMI";
				if(in_array($val['Brand1_ID'],$ourarr)) $qData[$key]['brandname'] = "Our";
					
		}
		$data['Our'] = 0;
		$data['Competitor'] = 0;
		$data['PMI'] = 0;
		
		foreach($qData as $key => $val){
				if($qData[$key]['brandname']=='Our') $data[$qData[$key]['brandname']]+=$val['total'];
				if($qData[$key]['brandname']=='Competitor')$data[$qData[$key]['brandname']]+=$val['total'];
				if($qData[$key]['brandname']=='PMI')$data[$qData[$key]['brandname']]+=$val['total'];
		}
		// pr($data);
		return $data;
	
	}
	
	function genderPref(){
	
		$sql = "SELECT COUNT( * ) num, me.sex, DATE(me.register_date) dd
				FROM my_entourage me 
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status IN ( 1 )
				AND sm.n_status = 1 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}	 
				GROUP BY me.sex
				ORDER BY num";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		foreach($qData as $val){
			
			$data[$val['sex']] = $val['num'];
		}
		// pr($qData);
		return $qData;
	
	}
	
	function agePref(){
		 
		$sql = "
				SELECT count( * ) num, DATE_FORMAT( me.register_date, '%Y-%m-%d' ) datetime, me.sex, YEAR(
				CURRENT_TIMESTAMP ) - YEAR( me.birthday ) - ( RIGHT(
				CURRENT_TIMESTAMP , 5 ) < RIGHT( me.birthday, 5 ) ) AS age
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status IN ( 1 )
				AND me.register_date <> '0000-00-00'
				AND me.register_date IS NOT NULL 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}
				GROUP BY age
				HAVING age <> '2013'
				AND age >= 0
				ORDER BY age ASC";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['18 - 24']= 0;
		$data['25 - 29']= 0;
		$data['30 - above']= 0;
		foreach( $qData as $val ){
			if($val['age']==null ) $data['null']+= $val['num'];
			else{
			if($val['age']<=24 ) $data['18 - 24'] += $val['num']; 
			if($val['age']>=25 && $val['age']<=29 ) $data['25 - 29'] += $val['num'];
			if($val['age']>=30 ) $data['30 - above']+= $val['num'];
			}
			
		}		
		return $data;	
	}
	
	function engagemenentpreformance(){
		
		$sql = "
				SELECT baid, SUM(engagement) baengagement FROM
				 (
					SELECT count(1) engagement, DATE(checkin.join_date) dd, e.id baid 
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
						{$this->qSbaFilter} 
						{$this->qBrandFilter}	
						{$this->qAreaFilter}	
						AND e.referrerbybrand=tags.userid 
						AND e.n_status = 1
					GROUP BY dd,e.id 
				 ) sm
				GROUP BY baid  
				";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		$data = false;
		$performance =false;
		$n = 0;
		foreach($qData as $key => $val){
			for($i=1;$i<=$val['baengagement'];$i++){
				$data[$val['baid']][] = 1;
				if($n==0) $performance[] = 0;
			}
			$n=1;
		}
		foreach($data as $engagement){
			 $n=0;
			foreach($engagement as $key => $val){
				if($n<6){	
					$performance[$n]+=$val;
					$n++;
				}else $n=0;
			}
		}
		$arrperformance  = array();
		foreach($performance as $key => $val){
			if($val!=0) $arrperformance[$key] = $val;
		}
		// pr($arrperformance);
		return $arrperformance;
		 
	}
	
	function engagemenentpreformancebycity(){
		
		$searchCity = strip_tags($this->apps->_g('searchCity'));
		if($searchCity!='') {
			$cityid = 0;
			$sql ="SELECT cityidmop  FROM  beat_city_reference WHERE city = '{$searchCity}' LIMIT 1";
			$qData = $this->apps->fetch($sql);	
			if($qData) $cityid = $qData['cityidmop'];
			
			if($cityid!=0) $this->qCityFilter = " AND e.city = '{$cityid}' ";
			else $this->qCityFilter = " ";
		}else $this->qCityFilter = " ";
		
		$sql = "
				SELECT baid, SUM(engagement) baengagement FROM
				 (
					SELECT count(1) engagement, DATE(checkin.join_date) dd, e.id baid 
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
						{$this->qSbaFilter} 
						{$this->qBrandFilter}	
						{$this->qAreaFilter}	
						{$this->qCityFilter} 
						AND e.referrerbybrand=tags.userid 
						AND e.n_status = 1
					GROUP BY dd,e.id 
				 ) sm
				GROUP BY baid  
				
				 
				";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		
		$data = false;
		$performance =false;
		$n = 0;
		foreach($qData as $key => $val){
			for($i=1;$i<=$val['baengagement'];$i++){
				$data[$val['baid']][] = 1;
				if($n==0) $performance[] = 0;
			}
			$n=1;
		}
		
		foreach($data as $engagement){
			 $n=0;
			foreach($engagement as $key => $val){
				if($n<6){	
					$performance[$n]+=$val;
					$n++;
				}else $n=0;
			}
		}
		$arrperformance  = array();
		foreach($performance as $key => $val){
			if($val!=0) $arrperformance[$key] = $val;
		}
		// pr($arrperformance);
		return $arrperformance;
		 
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
	
	function fixeddate($data = false,$datedayindex='dd',$valueindex='total',$accumulation='',$startdate=false,$enddate=false,$type='date'){
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
				 // $val+=$lastvalue;		
				if($val==0)$val = $lastvalue;
				$newdata[$n][$valueindex] =  $val;						 
				$lastvalue = $val;
				
				
			}	else $newdata[$n][$valueindex] =$val;
			
			$n++;
		}
		if($newdata)return $newdata;
		else return false;
	}
	
	function cityRef(){
			$cityname = strip_tags($this->apps->_p('cityname'));
			$sql ="SELECT city label  FROM  beat_city_reference WHERE city like '%{$cityname}%' AND city <> '(not specified)' ";
			$qData = $this->apps->fetch($sql,1);	
			if($qData) return $qData;
			return false;
	}

	function listBA(){
		global $CONFIG;

		$area = intval($this->apps->_p('area'));
		if($area>0){
			$filterArea = "AND mp.areaid = {$area}";
		}
		$brand = intval($this->apps->_p('brand'));
		if($brand>0){
			$filterBrand = "AND mp.brandid = {$brand}";
		}

		$sql="SELECT sm.id, sm.name, sm.last_name, mp.city, mp.brandid
				FROM social_member sm
				INNER JOIN my_pages mp
				ON sm.id=mp.ownerid
				WHERE mp.type=1 {$filterArea} {$filterBrand} 
				AND sm.n_status = 1
				AND sm.id not in (33,283,170,171,196,197,294,295,296,297,298,299,300,226,466,494,495,250,251)";
		//pr($sql);
		$rs = $this->apps->fetch($sql,1);

		if($rs) return $rs;
		return false;
	}
	
	function weekAchievmentKpiReport($all=false){	
		
		$sql = "SELECT COUNT( * ) num, DATE(me.register_date) dd
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status
				IN ( 1 )
				AND sm.n_status =1 AND DATE( me.register_date ) >= '{$this->startdate}'
				AND DATE( me.register_date ) <= '{$this->enddate}'
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}
				GROUP BY dd
				ORDER BY dd, num DESC ";
				// pr($sql);exit;
		$qData = $this->apps->fetch($sql,1);		
		if(!$qData) return false;
		return $qData;
	}
	
	function weeklyCumulativeEngageReport($all=false){
			
		$sql = "SELECT SUM(num) num , MIN(dd) dd
				FROM (
					SELECT COUNT( * ) num, DATE(me.register_date) dd
					FROM my_entourage me
					LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
					LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
					WHERE me.n_status
					IN ( 1 )
					AND sm.n_status =1 {$this->qmonthdate} 
					{$this->qSbaFilter}
					{$this->qAreaFilter}
					{$this->qBrandFilter}
					GROUP BY dd
					ORDER BY dd, num DESC 
				) a 
				GROUP BY WEEK(dd)
				ORDER BY WEEK(dd)
				";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false; 	
		return $qData;
	
	}
	
	function brandPrefReport($all=false){
		
		$sql = "SELECT code,brandtype FROM tbl_brand_preferences_references ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData)return false;
		$competitorarr = array();
		$pmiarr = array();
		$ourarr = array();
		foreach($qData as $val){
			if($val['brandtype']==0) $competitorarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==1) $pmiarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==2) $ourarr[(string)$val['code']] =(string)$val['code'];
		}
		
		$sql = "SELECT COUNT( * ) total, me.Brand1_ID
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status
				IN ( 1)
				AND sm.n_status =1 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}						 
				GROUP BY me.Brand1_ID
				ORDER BY total";
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		
		foreach($qData as $key => $val){
				$qData[$key]['brandname'] = "Our";
				if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['brandname'] = "Competitor";				
				if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['brandname'] = "PMI";
				if(in_array($val['Brand1_ID'],$ourarr)) $qData[$key]['brandname'] = "Our";
					
		}
		$data['Our'] = 0;
		$data['Competitor'] = 0;
		$data['PMI'] = 0;
		
		foreach($qData as $key => $val){
				if($qData[$key]['brandname']=='Our') $data[$qData[$key]['brandname']]+=$val['total'];
				if($qData[$key]['brandname']=='Competitor')$data[$qData[$key]['brandname']]+=$val['total'];
				if($qData[$key]['brandname']=='PMI')$data[$qData[$key]['brandname']]+=$val['total'];
		}
		// pr($data);
		return $data;
	
	}
	
	function genderPrefReport($all=false){
	
		$sql = "SELECT COUNT( * ) num, me.sex, DATE(me.register_date) dd
				FROM my_entourage me 
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status IN ( 1 )
				AND sm.n_status = 1 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}	 
				GROUP BY me.sex
				ORDER BY num";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		foreach($qData as $val){
			
			$data[$val['sex']] = $val['num'];
		}
		// pr($qData);
		return $qData;
	
	}
	
	function agePrefReport($all=false){
		 
		$sql = "
				SELECT count( * ) num, DATE_FORMAT( me.register_date, '%Y-%m-%d' ) datetime, me.sex, YEAR(
				CURRENT_TIMESTAMP ) - YEAR( me.birthday ) - ( RIGHT(
				CURRENT_TIMESTAMP , 5 ) < RIGHT( me.birthday, 5 ) ) AS age
				FROM my_entourage me
				LEFT JOIN social_member sm ON me.referrerbybrand = sm.id
				LEFT JOIN my_pages pages ON pages.ownerid = me.referrerbybrand 
				WHERE me.n_status IN ( 1 )
				AND me.register_date <> '0000-00-00'
				AND me.register_date IS NOT NULL 
				{$this->qSbaFilter}
				{$this->qAreaFilter}
				{$this->qBrandFilter}
				GROUP BY age
				HAVING age <> '2013'
				AND age >= 0
				ORDER BY age ASC";
				// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['18 - 24']= 0;
		$data['25 - 29']= 0;
		$data['30 - above']= 0;
		foreach( $qData as $val ){
			if($val['age']==null ) $data['null']+= $val['num'];
			else{
			if($val['age']<=24 ) $data['18 - 24'] += $val['num']; 
			if($val['age']>=25 && $val['age']<=29 ) $data['25 - 29'] += $val['num'];
			if($val['age']>=30 ) $data['30 - above']+= $val['num'];
			}
			
		}		
		return $data;	
	}
	
	function engagemenentpreformanceReport($all=false){
		
		$sql = " 
				SELECT baid,sm.name,sm.last_name,sm.email,sm.img,SUM(engagement) baengagement FROM
				 (
					SELECT count(1) engagement, DATE(checkin.join_date) dd,sm.name,sm.last_name,sm.email,sm.img, sm.id baid 
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
						{$this->qSbaFilter} 
						{$this->qBrandFilter}	
						AND e.referrerbybrand=tags.userid 
						AND e.n_status = 1
					GROUP BY dd,sm.id 
				 ) sm
				GROUP BY baid  
				";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);exit;
		if(!$qData) return false;
		$data = false;
		$performance =false;
		$n = 0;
		foreach($qData as $key => $val){
			for($i=1;$i<=$val['baengagement'];$i++){
				$data[$val['baid']][] = 1;
				if($n==0) $performance[] = 0;
			}
			$n=1;
		}
		foreach($data as $engagement){
			 $n=0;
			foreach($engagement as $key => $val){
				if($n<6){	
					$performance[$n]+=$val;
					$n++;
				}else $n=0;
			}
		}
		$arrperformance  = array();
		foreach($performance as $key => $val){
			if($val!=0) $arrperformance[$key] = $val;
		}
		// pr($arrperformance);
		return $arrperformance;
		 
	}
	
	function engagemenentpreformancebycityreport(){
		
		$searchCity = strip_tags($this->apps->_g('searchCity'));
		if($searchCity!='') {
			$cityid = 0;
			$sql ="SELECT cityidmop  FROM  beat_city_reference WHERE city = '{$searchCity}' LIMIT 1";
			$qData = $this->apps->fetch($sql);	
			if($qData) $cityid = $qData['cityidmop'];
			
			if($cityid!=0) $this->qCityFilter = " AND e.city = '{$cityid}' ";
			else $this->qCityFilter = " ";
		}else $this->qCityFilter = " ";
		
		$sql = "
				SELECT baid,sm.name,sm.last_name,sm.email,sm.img,SUM(engagement) baengagement FROM
				 (
					SELECT count(1) engagement, DATE(checkin.join_date) dd,sm.name,sm.last_name,sm.email,sm.img, sm.id baid 
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
						{$this->qCityFilter}  
						AND e.referrerbybrand=tags.userid 
						AND e.n_status = 1
					GROUP BY dd,sm.id 
				 ) sm
				GROUP BY baid  
				";
				
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		
		$data = false;
		$performance =false;
		$n = 0;
		foreach($qData as $key => $val){
			for($i=1;$i<=$val['baengagement'];$i++){
				$data[$val['baid']][] = 1;
				if($n==0) $performance[] = 0;
			}
			$n=1;
		}
		
		foreach($data as $engagement){
			 $n=0;
			foreach($engagement as $key => $val){
				if($n<6){	
					$performance[$n]+=$val;
					$n++;
				}else $n=0;
			}
		}
		$arrperformance  = array();
		foreach($performance as $key => $val){
			if($val!=0) $arrperformance[$key] = $val;
		}
		// pr($arrperformance);
		return $arrperformance;
		 
	}
	function getAge($birthDate){
		
         $birthDate = explode("-", $birthDate);
         $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
        return $age;
	}
	function entouragechart($streid=null,$start=0,$limit=10,$all=false,$summary=false,$alldata=false){
		global $CONFIG;
 
		$sql = "SELECT preferenceid,brandtype,id FROM tbl_brand_preferences_references WHERE preferenceid <> 0 ";
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData)return  array();
		$competitorarr = array();
		$pmiarr = array();
		$ourarr = array();
		foreach($qData as $val){
			if($val['brandtype']==0) $competitorarr[(string)$val['preferenceid']] =(string)$val['id'];
			if($val['brandtype']==1) $pmiarr[(string)$val['preferenceid']] =(string)$val['id'];
			if($val['brandtype']==2) $ourarr[(string)$val['preferenceid']] =(string)$val['id'];
		}
		
			if(intval($this->apps->_request('start'))!=0) $start = intval($this->apps->_request('start'));

			$qEntourage = "";

			 $qLimit = " LIMIT {$start},{$limit} ";
	 
		
		$filter = strip_tags($this->apps->_p('filter'));
		$alphabet = strip_tags($this->apps->_p('alphabet'));
		
		$cityid = strip_tags($this->apps->_g('areaid'));
		$brandid = strip_tags($this->apps->_g('brandid'));
		$areaid = strip_tags($this->apps->_g('areaid'));
		$sbaid = strip_tags($this->apps->_g('sbaid'));
	
		$qFilter ="";
		if($filter=="pending") $qFilter = " AND n_status = 0 ";
		if($filter=="accept") $qFilter = " AND n_status = 1 ";
		if($filter=="reject") $qFilter = " AND n_status = 2 ";
		 
		if($alphabet!='') $qFilterAlphabet = " AND name like '{$alphabet}%' ";
		else $qFilterAlphabet = " ";
		 
		$qDate = "";
		$startdate = strip_tags($this->apps->_p('startdate'));
		$enddate = strip_tags($this->apps->_p('enddate'));
		
		if($startdate) $startdate =date("Y-m-d",strtotime($startdate));
		if($enddate) $enddate =date("Y-m-d",strtotime($enddate));
		// pr($startdate);
		if(!$startdate)  $startdate = date("Y-m-d",strtotime($startdate. "-7 day"));
		if(!$enddate) if($startdate)  $enddate = date("Y-m-d" );
		
		if($startdate&&$enddate){
			$qDate = " AND DATE(entou.register_date) >= DATE('{$startdate}') AND DATE(entou.register_date) <= DATE('{$enddate}') ";
		}
		$data['result'] = array();
		$data['total'] = 0;
		if($this->apps->user->leaderdetail->type!=1) {
							$uid = intval($this->apps->_request('uid'));
							$auhtorarrid = false;
							if($uid==0)	{
								$filterArea = "";
								$filterBrand = "";
								$filterPL = "";
								if($cityid>0)$filterArea = "AND mp.areaid = {$cityid}";
								if($brandid>0)	$filterBrand = "AND ( mp.brandid = {$brandid} OR mp.brandsubid = {$brandid} ) ";
								if($sbaid>0)	$filterPL = "AND mp.ownerid = {$sbaid}";
								
								$sql="SELECT sm.id
								FROM social_member sm
								INNER JOIN my_pages mp
								ON sm.id=mp.ownerid
								WHERE mp.type=1 {$filterArea} {$filterBrand} {$filterPL} ";
								$rs = $this->apps->fetch($sql,1);
								//$this->logger->log($sql);
								if($rs){
									foreach($rs as $val){
										$auhtorarrid[$val['id']] = $val['id'];
									}
								}
							}
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $uid;
							
					 $qUserid = " AND entou.referrerbybrand IN ({$authorids}) ";		
		}else $qUserid = " AND  entou.referrerbybrand ={$this->uid} ";
		 
		 
		$sql = "	
		SELECT COUNT(*) total FROM my_entourage entou 	
		WHERE  1 {$qUserid} {$qEntourage} {$qFilter} {$qFilterAlphabet} {$qDate} AND entou.n_status IN (1) AND entou.referrerbybrand <>0 ";	
		$total = $this->apps->fetch($sql);		
		//pr($total);
		  // pr($total);
		if(!$total)return  array();
		$sql = "
		SELECT entou.Brand1_ID,entou.Brand1U_ID,entou.id,entou.birthday,entou.sex,entou.register_date,entou.n_status FROM my_entourage entou  
		WHERE 1 {$qUserid} {$qEntourage} {$qFilter} {$qFilterAlphabet} {$qDate} AND entou.n_status IN (1) AND entou.referrerbybrand <>0 {$qLimit}   ";		
		
		$qData = $this->apps->fetch($sql,1);
			
		if($qData) {
			$brandid = @$this->apps->user->branddetail;
			if($brandid){
				foreach($brandid as $val){
						$brandid = $val->ownerid;
				}
			}
			$brandcode = 0;
			if($brandid==5) $brandcode = 2;
			if($brandid==4) $brandcode = 22;
			$arrentourage = false;
			 
			foreach($qData as $val){
				$arrentourage[$val['id']] = $val['id'];
			}
			 		
			 
			foreach($qData as $key => $val){
					 
					$qData[$key]['entouragetype'] = "Our";
					if(array_key_exists($val['Brand1_ID'],$competitorarr)) $qData[$key]['entouragetype'] = "Competitor";				
					if(array_key_exists($val['Brand1_ID'],$pmiarr)) {
						if($brandcode==$pmiarr[$val['Brand1_ID']]) $qData[$key]['entouragetype'] = "Our";
						else $qData[$key]['entouragetype'] = "PMI";
					}
			 			
					if(array_key_exists($val['Brand1_ID'],$competitorarr))  $qData[$key]['brandrelevancycompetitor']=1	;	
					if(array_key_exists($val['Brand1_ID'],$pmiarr)) {
			 
						if($brandcode==$pmiarr[$val['Brand1_ID']]) $qData[$key]['brandrelevancyour']=1;
						$qData[$key]['brandrelevancypmi']=1;
					}
					if(array_key_exists($val['Brand1U_ID'],$competitorarr))  $qData[$key]['brandrelevancycompetitor']=1	;		
					if(array_key_exists($val['Brand1U_ID'],$pmiarr)) {
						if($brandcode==$pmiarr[$val['Brand1_ID']])  $qData[$key]['brandrelevancyour']=1;
						$qData[$key]['brandrelevancypmi']=1;
					
					} 
				
			}
			
			$data['total'] = $total['total'];
			//pr($data);
			$totals = intval($total['total']);
		
			if($totals>$start) $nextstart = $start;
			else $nextstart = 0;
			
					
			if($start<=0)$countstart = $limit;
			else $countstart = $limit+$nextstart;
			
			$thenextpage = intval($limit+$nextstart);
			if($totals<=$thenextpage)	$thenextpage = 0;	
			$data['pages']['nextpage'] = $thenextpage;
			$data['pages']['prevpage'] = $countstart-$limit;
			
			$data['result'] = $qData;
		  // pr($data);
		}
		 
		return $data;
		// return $list;
		
		
	
	}
	
}
?>
