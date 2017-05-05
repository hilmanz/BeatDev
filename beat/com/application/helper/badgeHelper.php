<?php 

class badgeHelper {
	
	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
		$this->dbshema= 'tbl';
	}
	
	function getBadges(){
		global $CONFIG;
		
		$mybadgedata['data']  = array();
		$mybadgedata['mybadge']  = 0;
		$mybadgedata['mypoint']  = 0;
		
		$sql =" 
		SELECT id,name,detail,image,point,image_2 FROM tbl_badge_detail  WHERE n_status = 1
		";
		$qData = $this->apps->fetch($sql,1);
		
		if(!$qData) return $mybadgedata;
		$badgedata = false;
		$badgedataarr = false;
		foreach($qData as $key => $val){
			$qData[$key]['imagepath'] = false;
				
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}badges/{$val['image_2']}"))  	$qData[$key]['imagepath'] = "badges";	
						
			//CHECK FILE SMALL
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image_2']}")) $qData[$key]['image_2'] = "small_{$val['image_2']}";
			
			
			if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image_2'];
			else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
			
			$badgedata[$val['id']]['id'] = $val['id'];
			$badgedata[$val['id']]['name'] = $val['name'];
			$badgedata[$val['id']]['detail'] =$val['detail'];
			$badgedata[$val['id']]['point'] =$val['point'];
			$badgedata[$val['id']]['image_full_path'] = $qData[$key]['image_full_path'];
			$badgedata[$val['id']]['grey_image_file'] = $qData[$key]['image_2'];
			$badgedata[$val['id']]['image_file'] = $qData[$key]['image'];
			$badgedata[$val['id']]['totalbadge'] = "0";
			$badgedata[$val['id']]['totalpoint'] = "0";
			 
		}
		if(!$badgedata) return $mybadgedata;
		
		$sql =" 
		SELECT mb.badgecode id ,COUNT(*) totalbadge ,SUM(bd.point) totalpoint 
		FROM my_badge mb
		LEFT JOIN tbl_badge_detail bd ON bd.id = mb.badgecode
		WHERE mb.userid = {$this->uid} 
		AND mb.n_status = 1 
		AND bd.n_status = 1 
		GROUP BY mb.badgecode 
		";
		$qData = $this->apps->fetch($sql,1);
		
		$totalbadge = 0;
		$totalpoint = 0;
		
		if($qData) {
			
			$mybadgedata['data']  = array();
			$mybadgedata['mybadge']  = "0";
			$mybadgedata['mypoint']  = "0";
			$totalbadge = 0;
			$totalpoint = 0;
			// pr($badgedata);
			foreach($qData as $key => $val){
				// pr($val['id']);
				// pr($val['totalbadge']);
				if(array_key_exists($val['id'],$badgedata)){
					$badgedata[$val['id']]['totalbadge'] =$val['totalbadge'];
					$badgedata[$val['id']]['totalpoint'] = $val['totalpoint'];
					$totalbadge+=$val['totalbadge'];
					$totalpoint+=$val['totalpoint'];
					 
					if($val['totalbadge']>0){
						
						$badgedata[$val['id']]['imagepath_2'] =false;
						if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}badges/{$badgedata[$val['id']]['image_file']}"))  	$badgedata[$val['id']]['imagepath_2'] = "badges";
						
						//CHECK FILE SMALL
						if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$badgedata[$val['id']]['imagepath_2']}/small_{$badgedata[$val['id']]['image_file']}")) $badgedata[$val['id']]['image_file'] = "small_{$badgedata[$val['id']]['image_file']}";

						if($badgedata[$val['id']]['imagepath_2']) $badgedata[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$badgedata[$val['id']]['imagepath_2']."/".$badgedata[$val['id']]['image_file'];
						else $badgedata[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
						 
					}
				}
			}
			
		
		}
			foreach($badgedata as $val){
				$badgedataarr[] = $val;
			}
		$mybadgedata['data'] = $badgedataarr;
		$mybadgedata['mybadge']  = (string)$totalbadge;
		$mybadgedata['mypoint']  = (string)$totalpoint;
		return $mybadgedata;
	 
		
	}
	
	function generatebadge($userid=false){
		
		$socialData = false;
		if($userid){
			$sql = "SELECT id FROM social_member WHERE n_status =1 ";
			$socialData  =  $this->apps->fetch($sql,1);
			
			foreach($socialData as $val){
				$data[] = 	$this->launch($val['id']);	
			}
		
		}else {
			$data[] = $this->launch($this->uid);	
		}
				
			return $data;		
	}
	
	function launch($userid=false){
		if($userid==false) return false;
		//initial badge user
		$getbadgedetail = $this->getbadgedetail();
		if(!$getbadgedetail) return false;
		// $this->logger->log("badge for user : ".$userid);
		foreach($getbadgedetail as $val){
			$badgedata = false;
				// $this->logger->log("badge name : ".$val['name']);
				// $this->logger->log("badge name : ".$val['checkcondition']);
				$getbadges = false;
				// check user limit of this badge
				$databadgeuser = $this->checkbadge($val['id'],$val['maxhavebadge'],$userid);
					// get badge if user dont have badge
					// $this->logger->log('generate data badge : '.json_encode($databadgeuser));
					if($databadgeuser['result']){
						//check badge condition for this user
						 
							if($val['checkcondition']=='entourage') $badgedata = $this->checkentourage($databadgeuser['total'],$userid,$val);
							if($val['checkcondition']=='engagement') $badgedata = $this->checkengagement($databadgeuser['total'],$userid,$val);
							if($val['checkcondition']=='checkin') $badgedata = $this->checkusercheckin($databadgeuser['total'],$userid,$val);
							if($val['checkcondition']=='brand') $badgedata = $this->checkbrandevent($databadgeuser['total'],$userid,$val);
							if($val['checkcondition']=='cocreation') $badgedata = $this->checkcocreationevent($databadgeuser['total'],$userid,$val);
							
							 
							//inset badge to user
							
							$databadgeuser = $this->checkbadge($val['id'],$val['maxhavebadge'],$userid);
							if($databadgeuser['result']) $getbadges = $this->givebadge($badgedata,$val,$userid);
						//flush var
					}
					
					// $this->logger->log("get badges : ".$getbadges);
					$data[] = $getbadges;
		//change user badge checkin -> loop 12 badge
		}
		
		return $data;
	}
	
	function checkbadge($badgecode=false,$caps=false,$userid=false){
		if($badgecode==false) return false;
		if($caps==false) return false;
		if($userid==false) $userid = $this->uid;
		$data['result'] = false;
		$data['total'] = 0;
		$data['badgecode'] = 0;
		$sql ="SELECT COUNT(*) total,badgecode  FROM my_badge WHERE userid={$userid} AND badgecode={$badgecode} GROUP BY badgecode ";
		
		$qData = $this->apps->fetch($sql);
	
		$data = false;
		$total = 0;
			if($qData) {
			$total = intval($qData['total']);
			}
			if($total>$caps)  {
				 
				return $data;
			}
			else {
				$data['result'] = true;
				$data['total'] = intval($qData['total']);
				$data['id'] = $qData['badgecode'];
				return $data;
			}
			 
		
		 
		return false;
	}
	
	 function getbadgedetail(){
		
		$sql = " SELECT * FROM tbl_badge_detail ";
		
		$qData = $this->apps->fetch($sql,1);
		
		if(!$qData) return false;
		
		return $qData;
		
	 }
	
	function givebadge($giveit=false,$data=false,$userid=false){
	
		if($giveit==false) return false;
		if($data==false) return false;
			if($userid==false) $userid = $this->uid;
		$qInsert  = array();
		for($i=1;$i<=$giveit;$i++){
			$qInsert[] =" ('{$data['id']}',{$userid},NOW(),1,'{$data['point']}') ";
		}
		$strQInsert = '';
		if(count($qInsert)>0){
			$strQInsert = implode(',',$qInsert);
		}		
		if($strQInsert=='') return false;
		
		$sql = " 
		INSERT INTO my_badge ( badgecode ,	userid ,	datetime 	,n_status ,badgepoint) 
		VALUES {$strQInsert}
		";
		
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()>0) return true;
		else return false;
	}
	
	function checkentourage($havebadge=-1,$userid=false,$badgedetail=false){
			 
				if($havebadge==-1) return false; 
			if($userid==false) $userid = $this->uid;
			if($badgedetail==false) return false;
			if($badgedetail['ncondition']==0) $badgedetail['ncondition'] = 1;
			$caps = intval($badgedetail['minimalcheckcondition']* $badgedetail['ncondition']);
			if($caps==0) return false;
			
			$sql = " 
			SELECT COUNT(*) total 
			FROM my_entourage 
			WHERE 
				referrerbybrand ={$userid} 
				AND n_status = 1
			GROUP BY referrerbybrand ";
			$qData = $this->apps->fetch($sql);
			// pr($sql);
			if(!$qData) return false;
			$getbadges = floor($qData['total'] / $caps );
			// pr($caps);
			// pr($havebadge);
			// pr($getbadges);
			// exit;
			if($havebadge<$getbadges) {
				$loopers = $getbadges - $havebadge;
				return $loopers;
			}return false;
			
	}
	
	function checkengagement($havebadge=-1,$userid=false,$badgedetail=false){
			if($havebadge==-1) return false;
			if($badgedetail==false) return false;
				if($userid==false) $userid = $this->uid;
			//other condition
			if($badgedetail['ncondition']==0) $badgedetail['ncondition'] = 1;
			$caps = intval($badgedetail['minimalcheckcondition']* $badgedetail['ncondition']);
			if($caps==0) return false;
			// check on checkin user
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
						AND checkin.userid ={$userid} 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (0) 
						AND e.referrerbybrand=tags.userid  
			GROUP BY checkin.userid	";
			$qData = $this->apps->fetch($sql);
			
			if(!$qData) return false;
	 
			$getbadges = floor(($qData['total']+intval($qData['total'])) / $caps );
			if($havebadge<$getbadges) {
				$loopers = $getbadges - $havebadge;
				return $loopers;
			}return false;
	}
	
	function checkusercheckin($havebadge=-1,$userid=false,$badgedetail=false){
			
			if($havebadge==-1) return false;
			
			if($badgedetail==false) return false;
			if($userid==false) $userid = $this->uid;
			if($badgedetail['ncondition']==0) $badgedetail['ncondition'] = 1;
			$caps = intval($badgedetail['minimalcheckcondition']* $badgedetail['ncondition']);
			
			if($caps==0) return false;
			// user checkin
			$sql = " SELECT COUNT(*) total 
			FROM my_checkin 
			WHERE userid ={$userid}
			AND n_status = 1  GROUP BY userid ";
			$qData = $this->apps->fetch($sql);
		
			if(!$qData) return false;
			$getbadges = floor($qData['total'] / $caps );
		 
			if($havebadge<$getbadges) {
				$loopers = $getbadges - $havebadge;
				return $loopers;
			}return false;
	}
	
	function checkbrandevent($havebadge=-1,$userid=false,$badgedetail=false){
			if($havebadge==-1) return false;
			if($badgedetail==false) return false;
			if($userid==false) $userid = $this->uid;
			if($badgedetail['ncondition']==0) $badgedetail['ncondition'] = 1;
			$caps = intval($badgedetail['minimalcheckcondition']* $badgedetail['ncondition']);
			if($caps==0) return false;	
				
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
						AND checkin.userid ={$userid} 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (3) 
						AND e.referrerbybrand=tags.userid  
			GROUP BY checkin.userid			 ";
			$qData = $this->apps->fetch($sql);
			// pr($sql);exit;
			if(!$qData) return false;
			$getbadges = floor($qData['total'] / $caps );
			if($havebadge<$getbadges) {
				$loopers = $getbadges - $havebadge;
				return $loopers;
			}return false;
	}
	function checkcocreationevent($havebadge=-1,$userid=false,$badgedetail=false){
			if($havebadge==-1) return false;
			if($badgedetail==false) return false;
			if($userid==false) $userid = $this->uid;
			if($badgedetail['ncondition']==0) $badgedetail['ncondition'] = 1;
			$caps = intval($badgedetail['minimalcheckcondition']* $badgedetail['ncondition']);
			if($caps==0) return false;
			
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
						AND checkin.userid ={$userid} 
						AND checkin.n_status = 1 
						AND cnt.n_status = 1 
						AND checkin.contentid<>0 
						AND  cnt.articleType = 5  AND cnt.categoryid IN (2) 
						AND e.referrerbybrand=tags.userid  
			GROUP BY checkin.userid			
			";
			$qData = $this->apps->fetch($sql);
			// pr($sql);exit;
			if(!$qData) return false;
			$getbadges = floor($qData['total'] / $caps );
			if($havebadge<$getbadges) {
				$loopers = $getbadges - $havebadge;
				return $loopers;
			}return false;
	}
}
?>