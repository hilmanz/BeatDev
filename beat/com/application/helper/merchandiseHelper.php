<?php 

class merchandiseHelper {
	
	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
		$this->dbshema= 'tbl';
	}
	function getUserMerchandise(){
		global $CONFIG;
		$sql =" 
		SELECT md.id,md.name,md.detail,md.image  FROM my_merchandise mm
		LEFT JOIN tbl_merchandise_detail md ON md.id = mm.merchandiseid
		WHERE mm.userid = {$this->uid}
		AND md.n_status = 1
		GROUP BY mm.merchandiseid
		";
		$qData =  $this->apps->fetch($sql,1);
		if($qData)	{
			
			foreach($qData as $key => $val){
				
				$qData[$key]['imagepath'] = false;
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}merchandises/{$val['image']}"))  	$qData[$key]['imagepath'] = "merchandises";	
							
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image']}")) $qData[$key]['image'] = "small_{$val['image']}";
				
				
				if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				
			}
			
			return $qData;		
		}
		
		return false;
			
	}
	
	
	function getMerchandise(){
		global $CONFIG;
		
				$brandarrdetail[0] = 0;
				$branddetail = @$this->apps->user->branddetail;
				if($branddetail){
					foreach($branddetail as $val){
							$brandarrdetail[$val->ownerid] = $val->ownerid;
					}
				}
					
				if($brandarrdetail){
					$brandid = implode(',',$brandarrdetail);
				}

				if($this->apps->user->leaderdetail->type == 666){
					$brandid = "0,4,5";
				}
		$sql =" SELECT * FROM tbl_merchandise_detail WHERE n_status = 1 AND merchandise_type IN ({$brandid}) ";
		$qData =  $this->apps->fetch($sql,1);
		if($qData)	{
			
			foreach($qData as $key => $val){
				
				$qData[$key]['imagepath'] = false;
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}merchandises/{$val['image']}"))  	$qData[$key]['imagepath'] = "merchandises";	
							
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image']}")) $qData[$key]['image'] = "small_{$val['image']}";
				
				
				if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				
			}
			
			return $qData;		
		}
		
		return array();
					
	}
	
	function redeemMerchandise(){
	
		// testing only $data = $this->apps->messageHelper->createMessage($this->uid," [ Notification ] <br /> Thank You <br /> You've successfully redeemed a apaaja  ",1);
		
		$arrBadge = false;
		$arrBadgeValue = false;
		$arrBadgeSet = false;
		
		$result['result'] = false;
		$result['message'] = " not changed status ";
		
		// pr($this->apps->user);exit;
		$merchandiseid = intval($this->apps->_p("merchandiseid"));
		
		$badgeid = strip_tags($this->apps->_p("badgeid")); 			/* 3,1,2 */
		$badgevalue = strip_tags($this->apps->_p("badgevalue"));	/* 5,2,3 */
		
	 
		$neededpoint = 0;
		$usergivenpoint = 0;
		
		//explode badge
		$arrBadge = explode(',',$badgeid);
		$arrBadgeValue = explode(',',$badgevalue);
		if(!$arrBadge) {
				$result['message'] = "not badge to send ";
				return $result;
		}
		if(!$arrBadgeValue) {
				$result['message'] = "not badge accumulation to send ";
				return $result;
		}
		if(count($arrBadge)!=count($arrBadgeValue)) {
				$result['message'] = "badge accumulation  and badge not same to send ";
				return $result;
		} 
		
		foreach($arrBadge as $key => $val){
			$arrBadgeSet[$val] = $arrBadgeValue[$key];
		}
		if(!$arrBadgeSet) {
				$result['message'] = "badge not found to send ";
				return $result;
		}  
		
		// check user current badge / point
		$sql = " 
		SELECT mb.* ,COUNT(*) badgevalue ,bd.point realpointbadge , SUM(bd.point) badgepoint 
		FROM my_badge mb
		LEFT JOIN tbl_badge_detail bd ON bd.id = mb.badgecode
		WHERE mb.userid = {$this->uid} 
		AND mb.n_status = 1 
		AND bd.n_status = 1 
		AND mb.badgecode IN ({$badgeid})
		GROUP BY mb.badgecode 
		 ";
		$qData = $this->apps->fetch($sql,1);
	
		if(!$qData){
				$result['message'] = "badge not found on this user ";
				return $result;
		}  
		//check current badge
		
		if(count($qData)!=count($arrBadgeSet)){
				$result['message'] = "badge not founded and badge current not same on this user ";
				return $result;
		}  
		$can = false;
		$badgeuser = false;
		$userusingbadge = false;
		foreach($qData as $val){
			if(array_key_exists($val['badgecode'],$arrBadgeSet)){
				if($arrBadgeSet[$val['badgecode']]<=$val['badgevalue']) $can[] = true;
				else $can[] = false;
			}else $can[] = false;
			$badgeuser[$val['badgecode']] = $val['realpointbadge'];
			$userusingbadge[$val['badgecode']] = $val['badgevalue'];
		}
		if(!$badgeuser) {
				$result['message'] = "badge not found ";
				return $result;
		}
		if(!$userusingbadge) {
				$result['message'] = "badge not found ";
				return $result;
		}
		if(in_array(false,$can)) {
				$result['message'] = "your badge not enough : ".json_encode($can);
				return $result;
		}  
		
		
		// check current merchandise needed point
		$sql ="SELECT * FROM tbl_merchandise_detail WHERE  id ={$merchandiseid} LIMIT 1"; 
		$qData = $this->apps->fetch($sql);
		
		if(!$qData){
				$result['message'] = "  your merchandise not found ";
				return $result;
		}  
		$merchandisename = $qData['name'];
		
		/* s: if using point comparison */
			// compare needed point and given point user
			$setBadgePoint = false;
			foreach($arrBadgeSet as  $badgecode => $val){
				$setBadgePoint[$badgecode] = $val*$badgeuser[$badgecode];
			}
			if(!$setBadgePoint){
					$result['message'] = "  set poin badge not found ";
					return $result;
			}  
			$usergivenpoint = array_sum($setBadgePoint);
			$neededpoint = intval($qData['point']);
			if($usergivenpoint==0||$neededpoint==0) {
					$result['message'] = " your point is zero : {$usergivenpoint} OR your merchandise point is zero : {$neededpoint} ";
					return $result;
			}  
				// pr($countpointusersend);
			if($neededpoint>$usergivenpoint) {
					$result['message'] = " your point not enough : {$usergivenpoint} OR your merchandise point not enough  : {$neededpoint} ";
					return $result;
			}  
		/* e: point comparison */	
		
		
		
		/* s: if use badge comparison for merchandis */
			/*
			
			$sql ="SELECT COUNT(*) badgeneed,badgecode FROM tbl_merchandise_requirement_badge WHERE  merchandiseid ={$merchandiseid} AND n_status = 1 GROUP BY badgecode"; 
			
			$qBadgeNeeds = $this->apps->fetch($sql,1);
			if(!$qBadgeNeeds){
					$result['message'] = "  your merchandise needed badge not found ";
					return $result;
			} 
			$can = false;
			foreach($qBadgeNeeds as $val){
				if(array_key_exists($val['badgecode'],$arrBadgeSet)){
				if($arrBadgeSet[$val['badgecode']]>=$val['badgeneed']) $can[] = true;
				else $can[] = false;
			}else $can[] = false;
			
			}
			// pr($qBadgeNeeds);
			// pr($arrBadgeSet);
			
			if(in_array(false,$can)) {
				$result['message'] = "badge not fill requirement on matching merchandise needs : ".json_encode($can);
				return $result;
			}
			*/
		/* e: badge comparison */	
		
		//if fullfill
			// change status my badge to 2 : locked
			$countpointusersend = 0;
			$requirement = false;
			// $newArrBadgesSet = array();
			// foreach($arrBadgeSet as $badgecode => $amount){
				// $newArrBadgesSet[$badgecode]['amount'] = $amount;
				// $newArrBadgesSet[$badgecode]['badgepoint'] = $badgeuser[$badgecode] ;
			// }
			// if(!$newArrBadgesSet){
					// $result['message'] = " your badges credential not correct : {$usergivenpoint}  ";
					// return $result;
			// }  
			// pr($arrBadgeSet);
			ksort($arrBadgeSet);
			// pr($arrBadgeSet);exit;
			foreach($arrBadgeSet as $badgecode => $val){
				for($i=1;$i<=$val;$i++){
					
					if($countpointusersend>$neededpoint) continue;
					else {
						$sql = " UPDATE my_badge SET n_status = 2 WHERE badgecode = {$badgecode} AND userid = {$this->uid} AND n_status = 1 LIMIT 1 ";
						// pr($sql);
						$arrUpdate[] = $sql;
						$qData = $this->apps->query($sql);
						$requirement[] = $qData;
						if($qData) $countpointusersend+=$badgeuser[$badgecode] ;
					 }
					 
					 // $countpointusersend+=$badgeuser[$badgecode] ; /* do not use this if requirement from badge only */
					 // pr($countpointusersend);
				}
			}
			$this->logger->log(json_encode($arrUpdate));
			if($requirement==false){
				$result['message'] = "badge not fill requirement on matching merchandise needs : ".json_encode($arrBadgeSet);
				return $result;
			}
			if(!in_array(false,$requirement)) {				
				// if success change
					// give merchandise to my_merchandise
						$sql = " INSERT INTO my_merchandise  
						(merchandiseid,userid,redeemdate,date_redeemed,name,address,phonenumber,email) 
						VALUES  
						({$merchandiseid},{$this->uid},NOW(),NOW(),\"{$this->apps->user->name}\",\"{$this->apps->user->StreetName}\",\"{$this->apps->user->phone_number}\",\"{$this->apps->user->email}\")  ";
						$qData = $this->apps->query($sql);
						$result['message'] = " SUCCESS REDEEM.. enough badge : {$usergivenpoint} OR your merchandise point enough  : {$neededpoint} ";
						$result['result'] = true;
						/**
						$data = $this->apps->messageHelper->createMessage($this->uid," 
						 (notification) 
						Thank You   You\'ve successfully redeemed a {$merchandisename}  ",1);
						 **/

						return $result;
			}else{
				$result['message'] = " not enough requirement data badge, your point not enough : {$usergivenpoint} OR your merchandise point not enough  : {$neededpoint} ";
				return $result;
			}  
		
 
		 
			$result['message'] = " You Dont Have Right to use this method ";
			return $result;
	}
	 
	function redeemList(){
		global $CONFIG;
		$start = intval($this->apps->_p("start"));
		$limit = intval($this->apps->_p("limit"));
		$exformdate=strtotime($this->apps->_p("fromdate"));
	
		$exformdate=date('Y-m-d',$exformdate);
		
		$fromdate = strip_tags($exformdate);
		
		$extodate=strtotime($this->apps->_p("todate"));
	
		$extodate=date('Y-m-d',$extodate);
		
		$todate = strip_tags($this->apps->_p("todate"));
		$sorter = strip_tags($this->apps->_p("sorter"));
		$search = strip_tags($this->apps->_p("search"));
		$sorter_type = intval($this->apps->_p("sorter_type"));
		$rdmtype = intval($this->apps->_p("rdmtype"));
		$sorter_sql = "mm.redeemdate DESC";

		if($search){
			$search_sql="AND (mm.name LIKE '%{$search}%' OR mm.email LIKE '%{$search}%' OR tmd.name LIKE '%{$search}%')";
		}

		if($fromdate!=''&&$todate!=''){
			$fromdate = date('Y-m-d',strtotime($fromdate));
			$todate = date('Y-m-d',strtotime($todate));
			$filterDate = "AND mm.redeemdate BETWEEN '{$fromdate} 00:00:00' AND '{$todate} 23:59:59'";
		}

		//Sorter
		if($sorter){
			if($sorter_type>0) $sorter_text = 'ASC';
			else $sorter_text = 'DESC';
			$sorter_sql = "{$sorter} {$sorter_text}";
		}

		$sql="SELECT mm.id,mm.name, mm.redeemdate, mm.email, mm.n_status,
				tmd.name AS badge_name, tmd.image AS badge_image
				FROM my_merchandise mm
				INNER JOIN tbl_merchandise_detail tmd
				ON mm.merchandiseid = tmd.id
				WHERE mm.n_status = {$rdmtype} {$filterDate} {$search_sql}
				ORDER BY {$sorter_sql} LIMIT {$start},{$limit}";
		//var_dump($sql);
		$rs = $this->apps->fetch($sql,1);

		$sql="SELECT COUNT(mm.id) AS total
				FROM my_merchandise mm
				INNER JOIN tbl_merchandise_detail tmd
				ON mm.merchandiseid = tmd.id
				WHERE mm.n_status = {$rdmtype} {$filterDate} {$search_sql}";
		//var_dump($rs);exit;
		$rs_total = $this->apps->fetch($sql);

		if($rs) return array('data'=>$rs,'total'=>$rs_total['total']);
		else return false;
	}
	function redeemListDownload(){
		global $CONFIG;

		$sql="SELECT mm.id,mm.name, mm.redeemdate, mm.email, mm.n_status,
				tmd.name AS badge_name, tmd.image AS badge_image
				FROM my_merchandise mm
				INNER JOIN tbl_merchandise_detail tmd
				ON mm.merchandiseid = tmd.id
				WHERE mm.n_status IN (1,0)
				ORDER BY mm.redeemdate DESC";
		//var_dump($sql);exit;
		$rs = $this->apps->fetch($sql,1);

		return $rs;
	}
	function redeemVerify($id=null){
		global $CONFIG;

		$sql = "UPDATE my_merchandise SET n_status = 1 WHERE id={$id}";
		if($this->apps->query($sql)) return true;
		else return false;
	}
}
?>
