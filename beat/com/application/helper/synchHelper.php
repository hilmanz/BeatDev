<?php 

class synchHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) {
				$uid = intval($this->apps->_request('uid'));
				if($uid==0) $this->uid = intval($this->apps->user->id);
				else $this->uid = $uid;
		}

		$this->dbshema = "beat";	
		$this->topclass = array(100,4,6);
	}
 
	function getFriends($all=true,$limit=10,$start=0,$useGroup=true){
	//global user id, for list of friend of friend : 21,23,1,5,3
		// $limit = 1000;
				
		$data['result'] = false;
		$data['data'] = array();
		$data['latestversion'] = 0; 
		$data['total'] = 0;
		$data['pages']['nextpage'] = 0;
		$data['pages']['prevpage'] =0;
					
		$uid = strip_tags($this->apps->_request('uid'));
		$version = intval($this->apps->_p('version'));
		$start = intval($this->apps->_p('start'));
		$group = intval($this->apps->_request('groupid'));
		$alphabet = strip_tags($this->apps->_p('alphabet'));
		$circle = array();
		
		if(!$uid) $uid = intval($this->uid);
		
		if($uid!=0 || $uid!=null) {
		
				
			//get circle group
			if($useGroup){
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
					
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}
				
				
				if($group!=0){			
					$strGroupid = $group;
				}else {	
					array_push($arrGroupId,0);
					$strGroupid = implode(',',$arrGroupId);
				}
			}else $strGroupid=0;
			$brandaidarr = false;
			$brandarr = @$this->apps->user->branddetail;
			if($brandarr){
				foreach($brandarr as $val){
						$brandaidarr[$val->ownerid] = $val->ownerid;
				}
			}
			if($brandaidarr){
				$brandid = implode(',',$brandaidarr);
				$qBrandarr = " AND ( brandid  IN ({$brandid}) OR brandsubid IN ({$brandid}) ) ";
			}else $qBrandarr = "";
			$sql =" SELECT name FROM my_pages WHERE ownerid ={$this->uid} LIMIT 1";
			$pagesnames = $this->apps->fetch($sql);
			if($pagesnames)$groupname = $pagesnames['name'];
			else $groupname = $this->apps->user->leaderdetail->name;
			
			$groupname = rtrim($groupname);
			$groupname = ltrim($groupname);
			if(strpos($groupname,' ')) $parseKeywords = explode(' ', $groupname);
			else $parseKeywords = false;
			
			if(is_array($parseKeywords)) {
				$groupname = strtoupper("AREA {$parseKeywords[1]}|PL {$parseKeywords[1]}|SBA {$parseKeywords[1]}");
			}else  $groupname = trim($groupname);
			
			if($alphabet!='') $qFilterAlphabet = " AND sm.name like '{$alphabet}%' ";
			else $qFilterAlphabet = " ";
			
			if($version!='') {
				if($version==0) $version = -1;
				$qFilterVersion = " AND mc.version > {$version} ";
			}else $qFilterVersion = " ";
			
			
			if(in_array($this->apps->user->leaderdetail->type,$this->topclass)) $groupname = " ";
			// if(is_array($parseKeywords)) $groupname = $groupname.'|'.trim(implode('|',$parseKeywords));
			// else  $groupname = trim($groupname);
			
			// get all friend of this user
			$sql =	" 
			
			SELECT count(*) total,MAX(version) version FROM 
			( 
				SELECT count(*) total,MAX(version) version FROM 
				( 
					 
					( 
						SELECT mc.id,mc.userid,mc.friendid,mc.ftype,mc.date_time,mc.n_status ,mc.version
						FROM my_circle mc
						LEFT JOIN social_member sm ON sm.id=mc.friendid
						WHERE mc.groupid IN ({$strGroupid})  {$qFilterAlphabet} {$qFilterVersion} AND mc.userid IN ({$uid}) AND  mc.n_status IN (0,1)   AND EXISTS (SELECT id FROM social_member s  WHERE mc.friendid=s.id   AND s.n_status IN (0,1) ) AND mc.ftype=1 GROUP BY mc.friendid,mc.ftype  
					)
					UNION
					( 
						SELECT mc.id,mc.userid,mc.friendid,mc.ftype,mc.date_time,mc.n_status  ,mc.version
						FROM my_circle mc
						LEFT JOIN my_entourage sm ON sm.id=mc.friendid
						WHERE  mc.groupid IN ({$strGroupid}) AND  sm.referrerbybrand <> {$uid} {$qFilterAlphabet} {$qFilterVersion} AND mc.userid IN ({$uid}) AND  mc.n_status IN (0,1) AND mc.ftype=0 GROUP BY mc.friendid,mc.ftype  
					)
					 
				) a
				GROUP BY friendid,ftype
			) b
			";
			// pr($sql);
			// //$this->logger->log($sql);
			$friends = $this->apps->fetch($sql);
				// $this->logger->log(json_encode($friends));
	// pr($friends);
			if(!$friends) return $data;
			if($friends['total']==0) return $data;
			
			if($all) $qAllQData = "  ";
			else  $qAllQData = " LIMIT {$start},{$limit} ";
			$circle =array();
			//get circle
			
			
			$sql =	"
			SELECT id,userid,friendid,ftype,date_time,n_status, TRIM(name) name,TRIM(last_name ) last_name,isfriends,version
			FROM 
			( 
					 
					(  
						SELECT   mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,sm.name, sm.last_name,mc.version,mc.n_status isfriends
						FROM my_circle mc
						LEFT JOIN social_member sm ON sm.id=mc.friendid
						WHERE  mc.groupid IN ({$strGroupid}) {$qFilterAlphabet} {$qFilterVersion}  AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (0,1) AND  mc.ftype =1 AND EXISTS (SELECT id FROM social_member s  WHERE mc.friendid=s.id AND s.n_status IN (0,1) ) 
						GROUP BY  mc.friendid, mc.ftype 
					)
					UNION
					( 
						SELECT mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,sm.name, sm.last_name,mc.version,mc.n_status isfriends
						FROM my_circle mc
						LEFT JOIN my_entourage sm ON sm.id=mc.friendid
						WHERE  mc.groupid IN ({$strGroupid}) AND  sm.referrerbybrand <> {$uid} {$qFilterAlphabet} {$qFilterVersion} AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (0,1) AND  mc.ftype=0 
						GROUP BY  mc.friendid, mc.ftype   
					)					
					 
			) a GROUP BY friendid,ftype  ORDER BY  name ASC , last_name ASC  {$qAllQData}
			";
		
			$qData = $this->apps->fetch($sql,1);
			// pr($sql);
			$this->logger->log($sql);
			if($qData) {
				$arrSocialFid = false;
			$arrEntourageFid = false;
				foreach($qData as $val){	
					/* BA */	
					if($val['ftype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['ftype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$circledata[$val['ftype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['ftype'] = 1;							
							$socialfid[$key]['fgroup'] = 1;							
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					// $this->logger->log($strentouragefid);
					$entouragefid = $this->entouragedata($strentouragefid,false);
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['ftype'] = 0;
								$entouragefid[$key]['fgroup'] = 1;	
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				

				
			
				if(!$circledata) return $data;
				
						// $this->logger->log(" this friends: ".json_encode($socialdata));
				// pr($entouragedata);
				// pr($circledata);
				//merge data
				foreach($circledata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
						if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype])) {
							$socialdata[$keyftype][$key]['isfriends'] = $val['isfriends'];
							$circle[] = $socialdata[$keyftype][$key];
						}
						if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))
							{
								$entouragedata[$keyftype][$key]['isfriends'] = $val['isfriends'];
								$circle[] = $entouragedata[$keyftype][$key];
							}
						 // pr($val);
					}
					
				}
				 
			
			}
	
			// pr($lversion);
			if($circle) $data['result'] = true;
			else  $data['result'] = false;
			$data['data'] = $circle;
			$data['latestversion'] = intval($friends['version']);
			$data['total'] = intval($friends['total']);
			
			
			$totals = intval($friends['total']);

			if($totals>$start) $nextstart = $start;
			else $nextstart = 0;
								
			if($start<=0)$countstart = $limit;
			else $countstart = $limit+$nextstart;
			
			$thenextpage = intval($limit+$nextstart);
			if($totals<=$thenextpage)	$thenextpage = 0;	
			$data['pages']['nextpage'] = $thenextpage;
			$data['pages']['prevpage'] = $countstart-$limit;
			
			// pr($data);
			return $data;
			
			
		}
		return $data;
	}
	
	
	
	function getEntourage($streid=null,$start=0,$limit=10,$all=false,$summary=false,$filter=false){
		global $CONFIG;
		// $limit = 10000;
		$version = intval($this->apps->_request('version'));

		$data['result'] = array();
		$data['latestversion'] =0;
		$data['total'] = 0;
		$data['pages']['nextpage'] = 0;
		$data['pages']['prevpage'] = 0;		

		$sql = "SELECT code,brandtype FROM tbl_brand_preferences_references ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData)return $data;
		$competitorarr = array();
		$pmiarr = array();
		foreach($qData as $val){
			if($val['brandtype']==0) $competitorarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==1) $pmiarr[(string)$val['code']] =(string)$val['code'];
		}
		
		if(intval($this->apps->_request('start'))!=0) $start = intval($this->apps->_request('start'));
		if($streid){			
			$qEntourage = " AND entou.id IN ({$streid}) ";
			$limit = 10;
		}else{
			$qEntourage = "";
		}
		if($all){
			$qLimit = " LIMIT {$start},{$limit} ";
		}else $qLimit = " LIMIT {$start},{$limit} ";
		
		if(!$filter)$filter = strip_tags($this->apps->_p('filter'));
		$alphabet = strip_tags($this->apps->_p('alphabet'));
		$cityid = strip_tags($this->apps->_p('cityid'));
		$brandid = strip_tags($this->apps->_p('brandid'));
		$totalengagement = intval($this->apps->_p('totalengagement'));
		
		$qFilter =" AND entou.n_status IN (0,1,2) ";
		if($filter=="pending") $qFilter = " AND entou.n_status = 0 ";
		if($filter=="accept") $qFilter = " AND entou.n_status = 1 ";
		if($filter=="reject") $qFilter = " AND entou.n_status = 2 ";
		if($filter=="engagement") 	{
			if($totalengagement==0) $qFilter = " AND stat.total<>{$totalengagement} ";
			else  $qFilter = " AND stat.total ={$totalengagement} ";
		}
		
		if($alphabet!='') $qFilterAlphabet = " AND entou.name like '{$alphabet}%' ";
		else $qFilterAlphabet = " ";
		
		$qCityid = "";
		if($cityid) $qCityid = " AND entou.city='{$cityid}' ";
		
		
		$qBrandid = "";
		if($brandid) $qBrandid = " AND pages.brandid='{$brandid}' ";
		
		if($version!='') {
			if($version==0) $version = -1;
			// $qFilterVersion = " AND entou.version > {$version} ";
			$qFilterVersion = "   ";
		}else $qFilterVersion = " ";
			
			
		
		$qDate = "";
		$startdate = strip_tags($this->apps->_p('startdate'));
		$enddate = strip_tags($this->apps->_p('enddate'));
		
		if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
		
		if($startdate&&$enddate){
			$qDate = " AND DATE(entou.register_date) >= DATE('{$startdate}') AND DATE(entou.register_date) <= DATE('{$enddate}') ";
		}
		$data['result'] = array();
		$data['total'] = 0;
		if($this->apps->user->leaderdetail->type!=1) {
							$uid = intval($this->apps->_request('uid'));
							$auhtorarrid = false;
							if($uid==0)	{
								$uid = $this->uid;								
								$auhtorarrid[$uid] = $uid;
								$auhtorminion = @$this->apps->user->branddetail;
								if($auhtorminion){
									foreach($auhtorminion as $val){
											$auhtorarrid[$val->ownerid] = $val->ownerid;
									}
								}
								
								$auhtorminion = @$this->apps->user->areadetail;
								if($auhtorminion){
									foreach($auhtorminion as $val){
											$auhtorarrid[$val->ownerid] = $val->ownerid;
									}
								}		
								
								$auhtorminion = @$this->apps->user->pldetail;
								if($auhtorminion){
									foreach($auhtorminion as $val){
											$auhtorarrid[$val->ownerid] = $val->ownerid;
									}
								}	
								
								$auhtorminion = @$this->apps->user->badetail;
								if($auhtorminion){
									foreach($auhtorminion as $val){
											$auhtorarrid[$val->ownerid] = $val->ownerid;
									}
								}	
							}
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $uid;
							
					 $qUserid = " AND entou.referrerbybrand IN ({$authorids}) ";		
		}else $qUserid = " AND  entou.referrerbybrand ={$this->uid} ";
		if($summary) $qFilter = " AND entou.n_status = 1 ";
		
		$sql = "	
		SELECT COUNT(*) total, MAX(entou.version) version 
		FROM my_entourage entou 
		LEFT JOIN my_pages pages ON pages.ownerid = entou.referrerbybrand
		LEFT JOIN ( 
				SELECT count(*) total,tags.id,tags.friendid
					FROM {$this->dbshema}_news_content_tags tags
					LEFT JOIN {$this->dbshema}_news_content content ON content.id = tags.contentid 
				WHERE  
					tags.n_status=1 
					AND tags.friendtype = 0  
					AND content.articleType=5
					AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=tags.contentid AND n_status = 1 )
				GROUP BY tags.friendid  
			) stat ON stat.friendid= entou.id
		WHERE  1 {$qUserid} {$qEntourage} {$qFilter} {$qFilterAlphabet} {$qFilterVersion} {$qDate} {$qCityid} {$qBrandid} ";	
		$total = $this->apps->fetch($sql);		
		// pr($total);
		if(!$total)return $data;
		$sql = "
		SELECT entou.*, IF(stat.total IS NULL,0,stat.total) total 
		FROM my_entourage entou 
		LEFT JOIN my_pages pages ON pages.ownerid = entou.referrerbybrand
		LEFT JOIN ( 
				SELECT count(*) total,tags.id,tags.friendid
					FROM {$this->dbshema}_news_content_tags tags
					LEFT JOIN {$this->dbshema}_news_content content ON content.id = tags.contentid 
				WHERE  
					tags.n_status=1 
					AND tags.friendtype = 0  
					AND content.articleType=5
					AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=tags.contentid AND n_status = 1 )
				GROUP BY tags.friendid  
			) stat ON stat.friendid= entou.id
		WHERE 1 {$qUserid} {$qEntourage} {$qFilter} {$qFilterAlphabet} {$qFilterVersion} {$qDate} {$qCityid} {$qBrandid} ORDER BY entou.register_date DESC  {$qLimit} ";		
		
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		// $this->logger->log($sql);
		// pr();

		
		if($qData) {
		
			$arrentourage = false;
			$strentourage = false;
			$entouragedata = false;
			foreach($qData as $val){
				$arrentourage[$val['id']] = $val['id'];
			}
			if($arrentourage){
				$strentourage = implode(',',$arrentourage);
				$entouragedata = $this->entouragestatistic($strentourage);
			}
			if($arrentourage){
				$strentourage = implode(',',$arrentourage);
				$latestengagement = $this->getlatestengagement($strentourage);
				// pr($latestengagement);
			}			
			foreach($qData as $key => $val){
					
					$qData[$key]['name'] =  ucwords(strtolower($qData[$key]['name']." ".$qData[$key]['last_name']));
					$qData[$key]['last_name'] =  ucwords($qData[$key]['last_name']);
					
					if(is_file($CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/small_".$val['img'])) {
						$qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/small_".$val['img'];
					}else  $qData[$key]['image_full_path'] =  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
					
					$qData[$key]['entouragetype'] = "Our";
					if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['entouragetype'] = "Competitor";				
					if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['entouragetype'] = "PMI";
					
					if($latestengagement){					
						if(array_key_exists($val['id'],$latestengagement))  $qData[$key]['latestengagament'] = $latestengagement[$val['id']];
						else  $qData[$key]['latestengagament'] = false;
					}else  $qData[$key]['latestengagament'] = false;
					if($entouragedata){
						
						if(array_key_exists($val['id'],$entouragedata)){
							$qData[$key]['total']= (string)count($entouragedata[$val['id']]);
							$qData[$key]['entouragedata']= $entouragedata[$val['id']];
						}else  $qData[$key]['entouragedata']= false;
						
					 
					}else  $qData[$key]['entouragedata']= false;
					
				
			}
			
			$data['result'] = $qData;
			$data['latestversion'] = intval($total['version']);
			$data['total'] = $total['total'];
			
			$totals = intval($total['total']);
		
			if($totals>$start) $nextstart = $start;
			else $nextstart = 0;
								
			if($start<=0)$countstart = $limit;
			else $countstart = $limit+$nextstart;
			
			$thenextpage = intval($limit+$nextstart);
			if($totals<=$thenextpage)	$thenextpage = 0;	
			$data['pages']['nextpage'] = $thenextpage;
			$data['pages']['prevpage'] = $countstart-$limit;
			
		}
		// pr($data);exit;
		
		return $data;
		// return $list;
		
		
	}
	
	
	function getGroupUser(){
			$uid = strip_tags($this->apps->_request('uid'));
			if(!$uid) $uid = intval($this->uid);
			if($uid!=0 || $uid!=null) {
				$sql = "SELECT * FROM my_circle_group WHERE userid IN ({$uid}) AND n_status = 1 ORDER BY datetime DESC ";
				
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					foreach($qData as $val){
						$groupCircle[$val['id']] = $val['name'];
					}	
					if($groupCircle)	return $groupCircle;
					else return false;
				}
				
				
			}
			return false;
	}
	
	 
	function socialdata($strsocialfid=false){
			
			if(!$strsocialfid)return false;
			global $CONFIG;
					//get friend detail
			$sql =	" 
			SELECT sm.id, sm.name ,sm.img,sm.sex,sm.last_name , pagetype.name role,cityref.city,1 fgroup
			FROM social_member sm
			LEFT JOIN my_pages pages ON pages.ownerid=sm.id
			LEFT JOIN my_pages_type pagetype ON pages.type=pagetype.id
			LEFT JOIN beat_city_reference cityref ON cityref.id=pages.city
			WHERE sm.id IN ({$strsocialfid}) AND  sm.n_status = 1  ";
			// $this->logger->log(" this friends sql: ".$sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $key => $val){
			
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
				
				$qData[$key]['latestengagement'] =$this->getbaengagement(1,$val['id']);	
				
				$qData[$key]['name'] =ucwords(strtolower($val['name']." ".$val['last_name']));
				$qData[$key]['last_name'] =ucwords($val['last_name']);
				$qData[$key]['city'] =ucwords($val['city']);
				
				$qData[$key]['persontype'] =false;
				 
			}
			return $qData;
			
	}
	
		
	function entouragedata($strentouragefid=false,$usingowner=true){
			
			if(!$strentouragefid) return false;
				global $CONFIG;
			$competitorarr = array('221','273','10','12','61');
			$pmiarr = array('00AM','Marlboro');
			
			if($usingowner) $ownerthisentourage = " AND entou.referrerbybrand = {$this->uid} ";
			else $ownerthisentourage = "";
			//get friend detail
			$sql =	" 
			SELECT entou.id,entou.name,entou.img,entou.sex,entou.last_name,
			IF(entou.referrerbybrand={$this->uid},'Entourage','Friend') role,entou.Brand1_ID ,cityref.city ,IF(entou.referrerbybrand = {$this->apps->user->id},0,1) fgroup
			FROM my_entourage entou
			LEFT JOIN beat_city_reference cityref ON cityref.cityidmop=entou.city
			WHERE entou.id IN ({$strentouragefid}) {$ownerthisentourage}  AND  entou.n_status = 1  ";
				//$this->logger->log($sql);
				// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			$latestengagement = $this->getlatestengagement($strentouragefid);
			
			foreach($qData as $key => $val){
					if(is_file($CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/".$val['img'])) $qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'];
					else  $qData[$key]['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
					
					$qData[$key]['name'] =ucwords(strtolower($val['name']." ".$val['last_name']));
					$qData[$key]['last_name'] =ucwords($val['last_name']);
					$qData[$key]['city'] =ucwords($val['city']);
					
					$qData[$key]['persontype'] = "Our";
					if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['persontype'] = "Competitor";				
					if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['persontype'] = "PMI";
					
					if($latestengagement){					
						if(array_key_exists($val['id'],$latestengagement))  $qData[$key]['latestengagement'] = $latestengagement[$val['id']];
						else  $qData[$key]['latestengagement'] = false;
					}
				
			}
			//$this->logger->log($sql);
			return $qData;
			
	}
	
	
	function getbaengagement($limit=5,$authorids=false){
		/* must have checkin */
		if(!$authorids){
		$leadertype = intval($this->apps->user->leaderdetail->type);
			if($leadertype){
							$auhtorarrid[$this->uid] = $this->uid;
							$auhtorminion = @$this->apps->user->branddetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}
							
							$auhtorminion = @$this->apps->user->areadetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}		
							
							$auhtorminion = @$this->apps->user->pldetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
							
							$auhtorminion = @$this->apps->user->badetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
								
						
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $this->uid;
							
			}
		}
		$sql ="
		SELECT cnt.id,contentid,userid,cnt.title, IF(cnt.brief IS NULL,cnt.title,cnt.brief) brief,join_date as posted_date
		FROM my_checkin checkin 
		LEFT JOIN social_member sm ON sm.id=checkin.userid
		LEFT JOIN {$this->dbshema}_news_content cnt ON cnt.id=checkin.contentid
		WHERE checkin.n_status = 1 AND contentid<>0 AND articleType = 5 AND checkin.userid IN ({$authorids})		
		GROUP BY contentid   ORDER BY join_date DESC LIMIT {$limit}";
		// pr($sql);
		$rs = $this->apps->fetch($sql,1);
		if(!$rs) return false;
		
		return $rs;
		
	}
	
	
	
	
	function entouragestatistic($strentourage=null){
	
		// pr($this->apps->user);exit;
			if($strentourage==null) return false;
			global $CONFIG;
				
			//get enggement of entourage
			$sql ="
			SELECT *
				FROM
				(
					SELECT tags.*
						FROM {$this->dbshema}_news_content_tags tags
						LEFT JOIN {$this->dbshema}_news_content content ON content.id = tags.contentid 
						WHERE  
							tags.n_status=1 
							AND tags.friendid IN ({$strentourage})
							AND tags.friendtype = 0  
							AND ( content.articleType=5 OR content.categoryid IN (2,3) ) 
							AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=tags.contentid AND n_status = 1  )
						GROUP BY tags.friendid , DATE(tags.date) ORDER BY tags.date ASC
					) a
				GROUP BY a.friendid, DATE(a.date) ORDER BY a.date DESC 
			";	
			$rqData = $this->apps->fetch($sql,1);
			$strcid = false;
			// pr($rqData);
			if(!$rqData) return false;
				$arrfid = false;
			foreach($rqData as $val){
				$arrcid[$val['contentid']] = $val['contentid'];
			}
			if($arrcid) $strcid = implode(',',$arrcid);
			
			//get contentid detail
			$sql="
			SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid ,articleType,can_save
			FROM {$this->dbshema}_news_content anc
			WHERE id IN ({$strcid}) ";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			foreach($qData as $key => $val){
				$qData[$key]['imagepath'] = false;
				
				
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";					
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$qData[$key]['banner'] = false;
				else $qData[$key]['banner'] = true;
								
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image']}")) $qData[$key]['image'] = "small_{$val['image']}";
				
				//PARSEURL FOR VIDEO THUMB
				$video_thumbnail = false;
				if($val['url']!='')	{
					//PARSER URL AND GET PARAM DATA
					$parseUrl = parse_url($val['url']);
					if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
					else $parseQuery = false;
					if($parseQuery) {
						if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
					} 
					$qData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $qData[$key]['video_thumbnail'] = false;
				
				if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				$contentdata[$val['id']] =  $qData[$key];
				
			}
			
			
			foreach($rqData as $key => $val){
				$arrfid[$val['friendid']][$key] = $val;
				if(array_key_exists($val['contentid'],$contentdata)) $arrfid[$val['friendid']][$key]['contentdetail'] = $contentdata[$val['contentid']];
				else  $arrfid[$val['friendid']][$key]['contentdetail']  = false;
			}
			if($arrfid) return $arrfid;
			
			return false;
	
			
		// i need check how many entourage of this BA
		// check how many times the entourage has engagement
	}
	
	
	function getlatestengagement($strentourage=false){
		if($strentourage==false) return false;
		global $CONFIG;
		//get enggement of entourage
		
			$sql ="
				SELECT tags.*, MAX(tags.contentid) contentid, MAX(tags.date) date
				FROM {$this->dbshema}_news_content_tags tags
					LEFT JOIN {$this->dbshema}_news_content content ON content.id = tags.contentid 
				WHERE  
					tags.n_status=1 
					AND tags.friendid IN ({$strentourage})
					AND tags.friendtype = 0  
					AND content.articleType=5
					AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=tags.contentid AND n_status = 1 )
				GROUP BY tags.friendid ORDER BY tags.date ASC 
			";	
		
			$qData = $this->apps->fetch($sql,1);
				// pr($qData); 
			$arrfid = false;
			if(!$qData) return false;
			foreach($qData as $key => $val){
					
				$contentid[$val['contentid']] = $val['contentid'];				
			}
				$contentarr = false;
			if($contentid){
		
				$strcid = implode(',',$contentid);
				$sql="
				SELECT anc.id,anc.title,anc.brief,anc.image,anc.posted_date,tpages.name pagetypes
				FROM {$this->dbshema}_news_content anc			
				LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid		
				LEFT JOIN my_pages_type tpages ON tpages.id=pages.type 				
				WHERE anc.id IN ({$strcid})   ";
					// pr($sql);exit;
				$rqData = $this->apps->fetch($sql,1);
				foreach($rqData as $key => $val){
					$rqData[$key]['engagementtype'] = "Personal";
					if($val['pagetypes']=='SBA') $rqData[$key]['engagementtype'] = "Personal";
					if($val['pagetypes']=='PL') $rqData[$key]['engagementtype'] = "Co-Creation";
					if($val['pagetypes']=='Brand') $rqData[$key]['engagementtype'] = "BRAND";
					if($val['pagetypes']=='121') $rqData[$key]['engagementtype'] = "Co-Creation";
					if($val['pagetypes']=='IS') $rqData[$key]['engagementtype'] = "BRAND";
					
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))   $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/".$val['image'];
					else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";	
					
					$contentarr[$val['id']] = $rqData[$key];				
				}
			}
			if(!$qData) return false;
			foreach($qData as $key => $val){						
				$qData[$key]['engagementtype'] = "Personal";
				if($contentarr){			
				
						if(array_key_exists($val['contentid'],$contentarr))  {
							$qData[$key]['contentdetail'] = $contentarr[$val['contentid']];
							$qData[$key]['engagementtype'] =  $contentarr[$val['contentid']]['engagementtype'];
						}else  $qData[$key]['contentdetail'] = false;
				}	
				
				$arrfid[$val['friendid']] = $qData[$key];	
			}
			
			
			// pr($arrfid);exit;
			return $arrfid;
	}
	
	
	function getNotificationCount(){
	
			$plan = $this->apps->contentHelper->getArticleContent(null,1,4,array(0,3),"plan",false,false,false,true,true,true,false,true);
 
			$notification = $this->apps->activityHelper->getA360activity(0,1,false,false,false,'3',false);	
			$data['notification'] = $notification['total'];
			$data['plan'] = $plan['totalnotif'];
	 		 
	 		$inbox  = $this->apps->messageHelper->getMessage(0,1);
	 		$data['inbox'] = $inbox['totalnotif'];
			return $data;
	}
	
	
	function getFriendsTagList($all=true,$limit=10,$start=0,$useGroup=true,$friendtotags=false,$usermember=false){
	//global user id, for list of friend of friend : 21,23,1,5,3
		$friendtotags = true;
		if($friendtotags) $limit = 10;
		if($usermember) $limit = 10;
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_p('start'));
		$group = intval($this->apps->_request('groupid'));
		$alphabet = strip_tags($this->apps->_p('alphabet'));
		$circle = array();
		$version = intval($this->apps->_p('version'));
		
		$data['result'] = false;
		$data['data'] = array();
		$data['total'] = 0;
		 
		$data['pages']['nextpage'] = 0;
		$data['pages']['prevpage'] = 0;
		
		if(!$uid) $uid = intval($this->uid);
		
		if($uid!=0 || $uid!=null) {
		
			if($version!='') {
			if($version==0) $version = -1;
				$qFilterVersion = " AND mc.version > {$version} ";
				$qFilterVersionEN = " AND sm.version > {$version} ";
			}else {
				$qFilterVersion = " ";
				$qFilterVersionEN = " ";
			}

			//get circle group
			if($useGroup){
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
					
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}
				
				
				if($group!=0){			
					$strGroupid = $group;
				}else {	
					array_push($arrGroupId,0);
					$strGroupid = implode(',',$arrGroupId);
				}
			}else $strGroupid=0;
			$brandaidarr = false;
			$brandarr = @$this->apps->user->branddetail;
			if($brandarr){
				foreach($brandarr as $val){
						$brandaidarr[$val->ownerid] = $val->ownerid;
				}
			}
			if($brandaidarr){
				$brandid = implode(',',$brandaidarr);
				$qBrandarr = " AND ( brandid  IN ({$brandid}) OR brandsubid IN ({$brandid}) ) ";
			}else $qBrandarr = "";
			$sql =" SELECT name FROM my_pages WHERE ownerid ={$this->uid} LIMIT 1";
			$pagesnames = $this->apps->fetch($sql);
			if($pagesnames)$groupname = $pagesnames['name'];
			else $groupname = $this->apps->user->leaderdetail->name;
			
			$groupname = rtrim($groupname);
			$groupname = ltrim($groupname);
			if(strpos($groupname,' ')) $parseKeywords = explode(' ', $groupname);
			else $parseKeywords = false;
			
			if(is_array($parseKeywords)) {
				$groupname = strtoupper("AREA {$parseKeywords[1]}|PL {$parseKeywords[1]}|SBA {$parseKeywords[1]}");
			}else  $groupname = trim($groupname);
			
			if($alphabet!='') $qFilterAlphabet = " AND sm.name like '{$alphabet}%' ";
			else $qFilterAlphabet = " ";
			
			if(in_array($this->apps->user->leaderdetail->type,$this->topclass)) $groupname = " ";
			// if(is_array($parseKeywords)) $groupname = $groupname.'|'.trim(implode('|',$parseKeywords));
			// else  $groupname = trim($groupname);
			$qFriendtotaltags = "";
			$qFriendtags = "";
			if($friendtotags){
				$qFriendtotaltags = "
					UNION
					( 
					SELECT sm.id,sm.referrerbybrand userid,sm.id friendid,0 ftype,sm.register_date date_time,sm.n_status,sm.name, sm.last_name ,sm.version 
					FROM my_entourage sm 
					WHERE  1 {$qFilterAlphabet}  {$qFilterVersionEN}  AND sm.referrerbybrand IN ({$uid}) AND  sm.n_status IN (1,2)  
					)";
				$qFriendtags = "
					UNION
					( 
					SELECT sm.id, sm.referrerbybrand userid,sm.id friendid,0 ftype, sm.register_date date_time, sm.n_status ,sm.name, sm.last_name ,sm.version
					FROM my_entourage sm
				 
					WHERE 1 {$qFilterAlphabet}   {$qFilterVersionEN}  AND  sm.referrerbybrand IN ({$uid})   AND  sm.n_status IN (1,2)  
					)";
			}
			
		if($usermember)	{
				$qFriendtotalwithentourage = "";
				$qFriendwithentourage = "";
			
			}else{
				
			
				$qFriendtotalwithentourage = "
					
					UNION
					( 
					SELECT mc.id,mc.userid,mc.friendid,mc.ftype,mc.date_time,mc.n_status ,sm.name, sm.last_name ,sm.version
					FROM my_circle mc
					LEFT JOIN my_entourage sm ON sm.id=mc.friendid
					WHERE mc.groupid IN ({$strGroupid})  {$qFilterAlphabet} AND mc.userid IN ({$uid}) AND  mc.n_status IN (1) AND mc.ftype=0 GROUP BY mc.friendid,mc.ftype  
					)
				
				";
				
				$qFriendwithentourage = "
				
				UNION
					( 
					SELECT mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,sm.name, sm.last_name ,sm.version
					FROM my_circle mc
					LEFT JOIN my_entourage sm ON sm.id=mc.friendid
					WHERE  mc.groupid IN ({$strGroupid}) {$qFilterAlphabet}  AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (1) AND  mc.ftype=0 
					GROUP BY  mc.friendid, mc.ftype   
					)
				
				";
			
			}
			// get all friend of this user
			$sql =	" 
			
			SELECT count(*) total,MAX(version) version FROM 
			( 
				SELECT count(*) total,MAX(version) version FROM 
				( 
					(
					SELECT mp.id,{$uid} userid,mp.ownerid friendid,1 ftype,mp.created_date date_time,mp.n_status,sm.name, sm.last_name ,0 version  
					FROM my_pages mp
					LEFT JOIN social_member sm ON sm.id=mp.ownerid
					WHERE EXISTS  ( SELECT friendid FROM my_circle mc  WHERE mc.friendid=mp.ownerid AND mc.n_status=1 AND userid={$uid}  {$qFilterVersion} ) AND mp.masterrole=0    {$qFilterAlphabet} {$qBrandarr} 
					)
					UNION
					( 
					SELECT mc.id,mc.userid,mc.friendid,mc.ftype,mc.date_time,mc.n_status,sm.name, sm.last_name ,mc.version  
					FROM my_circle mc
					LEFT JOIN social_member sm ON sm.id=mc.friendid
					WHERE mc.groupid IN ({$strGroupid}) AND EXISTS (SELECT id FROM social_member s  WHERE mc.friendid=s.id   AND s.n_status IN (0,1) ) {$qFilterAlphabet}  {$qFilterVersion} AND mc.userid IN ({$uid}) AND  mc.n_status IN (1) AND mc.ftype=1 GROUP BY mc.friendid,mc.ftype  
					)
					{$qFriendtotalwithentourage}
					{$qFriendtotaltags}
				) a
				GROUP BY friendid,ftype
			) b
			";
			//$this->logger->log($sql);
			// pr($sql);
			$friends = $this->apps->fetch($sql);
	// pr($friends);
			if(!$friends) return $data;
			if($friends['total']==0) return $data;
			
			if($all) $qAllQData = "  ";
			else  $qAllQData = " LIMIT {$start},{$limit} ";
			$circle =array();
			//get circle
			
			
			$sql =	"
			SELECT id,userid,friendid,ftype,date_time,n_status, TRIM(name) name,TRIM(last_name ) last_name, version
			FROM 
			( 
					(
					SELECT mp.id,{$uid} userid,mp.ownerid friendid,1 ftype,mp.created_date date_time,mp.n_status ,sm.name, sm.last_name , 0 version
					FROM my_pages mp
					LEFT JOIN social_member sm ON sm.id=mp.ownerid
					WHERE EXISTS (SELECT friendid FROM my_circle mc  WHERE mc.friendid=mp.ownerid AND mc.n_status=1 AND userid={$uid}  {$qFilterVersion}  ) AND mp.masterrole=0   {$qFilterAlphabet}   AND  mp.ownerid <> {$uid} {$qBrandarr} 
					)
					UNION
					( 
					SELECT   mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,sm.name, sm.last_name ,mc.version  
					FROM my_circle mc
					LEFT JOIN social_member sm ON sm.id=mc.friendid
					WHERE  mc.groupid IN ({$strGroupid}) AND EXISTS (SELECT id FROM social_member s  WHERE mc.friendid=s.id   AND s.n_status IN (0,1) ) {$qFilterAlphabet}  {$qFilterVersion}  AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (1) AND  mc.ftype=1 
					GROUP BY  mc.friendid, mc.ftype 
					)
					{$qFriendwithentourage}
					{$qFriendtags} 
			) a GROUP BY friendid,ftype  ORDER BY  name ASC , last_name ASC  {$qAllQData}
			";
		
			$qData = $this->apps->fetch($sql,1);
			// pr($sql);
			//$this->logger->log($sql);
			if($qData) {
			$arrSocialFid = false;
			$arrEntourageFid = false;
				foreach($qData as $val){	
					/* BA */	
					if($val['ftype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['ftype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$circledata[$val['ftype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['ftype'] = 1;
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					// $this->logger->log($strentouragefid);
					$entouragefid = $this->entouragedata($strentouragefid,false);
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['ftype'] = 0;
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				

				
			
				if(!$circledata) return $data;
				
				// pr($socialdata);
				// pr($entouragedata);
				// pr($circledata);
				//merge data
				foreach($circledata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
					if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype]))  $circle[] = $socialdata[$keyftype][$key];
					if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))  $circle[] = $entouragedata[$keyftype][$key];
					 // pr($val);
					}
					
				}
			
			
				
			
			}
			// pr($circle);
			if($circle) $data['result'] = true;
			else  $data['result'] = false;
			$data['data'] = $circle;
			$data['latestversion'] = intval($friends['version']);
			$data['total'] = intval($friends['total']);
			
			
			$totals = intval($friends['total']);

			if($totals>$start) $nextstart = $start;
			else $nextstart = 0;
								
			if($start<=0)$countstart = $limit;
			else $countstart = $limit+$nextstart;
			
			$thenextpage = intval($limit+$nextstart);
			if($totals<=$thenextpage)	$thenextpage = 0;	
			$data['pages']['nextpage'] = $thenextpage;
			$data['pages']['prevpage'] = $countstart-$limit;
			
			// pr($data);
			return $data;
			
			
		}
		return $data;
	}
	
}

?>

