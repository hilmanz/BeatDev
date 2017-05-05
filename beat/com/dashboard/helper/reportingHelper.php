<?php

class reportingHelper {

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

	function allUserRegistration(){
		$sql = "SELECT * FROM user_registrant 
				WHERE 
				datetime >= '{$this->startdate}'
				AND datetime <= '{$this->enddate}' 
				GROUP BY datetime,sex ORDER BY datetime ASC "; 
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){
			@$data['data'][$val['datetime']]+= $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];		
			
			
		// pr($val);
			
		}
		
		$data['count'] = count($qData);
		// pr($data);
		/* $query = "SELECT count(*) as jumlah, sex FROM social_member";
		$result = $this->apps->fetch($query);
		$data['jumlah'] = $result['jumlah'];
		
		$queryFemale = "SELECT count(*) as jumlah_female, sex FROM social_member where sex = 'F' ";
		$resultFemale = $this->apps->fetch($queryFemale);
		$data['jumlah_female'] = $resultFemale['jumlah_female'];
		
		$queryMale = "SELECT count(*) as jumlah_male, sex FROM social_member where sex = 'M' ";
		$resultMale = $this->apps->fetch($queryMale);
		$data['jumlah_male'] = $resultMale['jumlah_male'];
		// pr($data); */
		return $data;
	}
	
	function userUnverified(){
		$sql = "SELECT * FROM user_unverified WHERE 
				datetime >= '{$this->startdate}'
				AND datetime <= '{$this->enddate}'
				GROUP BY  datetime,sex ORDER BY datetime ASC"; 
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			@$data['data'][$val['datetime']]+= $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];
			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];	
		}		
		
		// pr($data);
		return $data;
		
	}
	
	function loginUser($type='login'){
		$qdblogin = "login_user_daily";
		$qDatetimes =  " datetime >= '{$this->startdate}'
				AND datetime <= '{$this->enddate}' ";
		if($type=='active')	$qdblogin = "login_user_daily_active";
		if($type=='weekly')	 {
			$qdblogin = "login_user_weekly";
			$qDatetimes =  " 
				WEEK(datetime) >= WEEK('{$this->startdate}')
				AND WEEK(datetime) <= WEEK('{$this->enddate}') ";
		}
		if($type=='monthly') {
			$qdblogin = "login_user_monthly";
			$qDatetimes =  " MONth(datetime) >= MONTH('{$this->startdate}')
				AND MONTH(datetime) <= MONTH('{$this->enddate}') ";
			
		}
		
		$sql = "SELECT * FROM {$qdblogin} 
				WHERE 
				{$qDatetimes}
				GROUP BY datetime,sex ORDER BY datetime ASC  "; 
		// pr($sql);
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		// pr($qData);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			@$data['data'][$val['datetime']]+= $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];	
		}		
		
		// pr($data);
		return $data;
		
	}
	
	/* function activeUser($type=1){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "
		SELECT count(*) num, DATE_FORMAT(log.date_time,'%Y-%m-%d') datetime , sm.sex
		FROM `tbl_activity_log` log
		LEFT JOIN social_member sm ON sm.id = log.user_id
		WHERE log.action_id = 1 {$qTypeUser} GROUP BY sm.sex,datetime {$qActiveUser} ORDER BY datetime DESC LIMIT 7 "; 
		// pr($sql);
		$qData = $this->apps->open(1);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['datetime']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];		
		}		
		
		return $data;
		
	} */
	
	function superUser ($type=30){

		$sql = "SELECT count( * ) num, sex, dateday FROM user_weekly 
				WHERE dateday >= '{$this->startdate}'
				AND dateday <= '{$this->enddate}'
				GROUP BY dateday,sex HAVING num > 1 
				ORDER BY dateday "; 
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['dateday']]= $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['dateday']] = $val['dateday'];
			
			$data['jumlah']+= $val['num'];		
		}		
		
		return $data;
		
	}
	
	function veryActive ($type=30){

	
			$sql = "SELECT count( * ) num, sex, dateday
				FROM user_monthly
				WHERE dateday >= '{$this->startdate}'
				AND dateday <= '{$this->enddate}'
				GROUP BY dateday,sex HAVING num > 1 
				ORDER BY dateday DESC "; 
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['dateday']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['dateday']] = $val['dateday'];
			
			$data['jumlah']+= $val['num'];		
		}		
		
		return $data;
		
	}
	
	function getChartDataOf($searchData=null){
		
		if($searchData==null) return false;
		
		if(is_array($searchData)) {
			foreach($searchData as $val){
				$nuArr[] = "'{$val}'";
			}
			if($nuArr)	$searchData = implode(',',$nuArr);
			else return false;
		}
		
		$theactivity = "{$searchData}";
		
		/* get activity ID */
		$actionnamedata = $this->getactivitytype($theactivity);

		if($actionnamedata) {
			
			$activityID = implode(',',$actionnamedata['id']);
		}else $activityID = false;
			
		$sql = "SELECT count(*) total, DATE(date_time) dateformatonly  FROM tbl_activity_log WHERE action_id IN ({$activityId}) ORDER BY dateformatonly GROUP BY dateformatonly LIMIT {$start},{$limit}";

		$getChartDataOf[$searchData] = $this->apps->fetch($sql);
		
		//pr($getChartDataOf);
		exit;

	}

	function getactivitytype($dataactivity=null,$id=false){
		if($dataactivity==null)return false;
		if($id) $qAppender = " id IN ({$dataactivity}) ";
		else $qAppender = " activityName IN ({$dataactivity})  ";
		$theactivity = false;
		/* get activity  id */	
		$sql = " SELECT * FROM tbl_activity_actions WHERE {$qAppender} ";

		$qData = $this->apps->open(1);
			
		if($qData) {
			foreach($qData as $val){
				$theactivity['id'][$val['id']]=$val['id'];
				$theactivity['name'][$val['id']]=$val['activityName'];
				
			}
		}
		return $theactivity;
	}
	
	function getSurf($value=null,$userid=null) {
		if ($value) $filter = "AND action_value = 'home'";
		else $filter = "";
		
		if ($userid) $filter .= " AND user_id IN ({$userid}) ";
		else $filter .= "";
		
		$filter .= $this->apps->_g('startdate') ? "AND date_time >= '{$this->apps->_g('startdate')}' " : "";
		$filter .= $this->apps->_g('enddate') ? "AND date_time < '{$this->apps->_g('enddate')}' " : "";
		
		$sql = "SELECT user_id,count(*) total FROM tbl_activity_log WHERE action_id = 6 {$filter} GROUP BY user_id";
		$this->apps->forceopendb(0);
		$qData = $this->apps->fetch($sql,1);
		$arrsurfhome = false;
		if($qData){
			foreach ($qData as $key =>$val) {
				$arrsurfhome[$val['user_id']] = $val['total'];
			}
		}
		
		return $arrsurfhome;
	}
	
	function getBadges($userid=null) {
		if ($userid) $filter = " AND userid IN ({$userid}) ";
		else $filter = "";
		
		$filter .= $this->apps->_g('startdate') ? "AND datetime >= '{$this->apps->_g('startdate')}' " : "";
		$filter .= $this->apps->_g('enddate') ? "AND datetime < '{$this->apps->_g('enddate')}' " : "";
		
		$sql = "SELECT userid,count(*) total FROM my_badge WHERE n_status = 1 {$filter} GROUP BY userid";
		$this->apps->forceopendb(0);
		$qData = $this->apps->fetch($sql,1);
		$arrData = false;
		if($qData){
			foreach ($qData as $key =>$val) {
				$arrData[$val['userid']] = $val['total'];
			}
		}
		return $arrData;
	}
	
	function getUploadContent($userid=null) {
		if ($userid) $filter = " AND user_id IN ({$userid}) ";
		else $filter = "";
		
		$filter .= $this->apps->_g('startdate') ? "AND date_time >= '{$this->apps->_g('startdate')}' " : "";
		$filter .= $this->apps->_g('enddate') ? "AND date_time < '{$this->apps->_g('enddate')}' " : "";
		
		$sql = "
			SELECT tl.id,tl.user_id,tl.action_id,nc.articleType,nct.type,count(nc.articleType) as total
			FROM tbl_activity_log tl
			LEFT JOIN athreesix_news_content nc ON tl.action_value = nc.id
			LEFT JOIN athreesix_news_content_type nct ON nc.articleType = nct.id
			WHERE action_id = 7 {$filter} 
			GROUP BY nc.articleType
			ORDER BY nc.articleType
		";
		
		$this->apps->forceopendb(0);
		$qData = $this->apps->fetch($sql,1);
		$arrData = false;
		if($qData){
			foreach ($qData as $key =>$val) {
				$arrData[$val['user_id']][$val['type']] = $val['total'];
			}
		}
		return $arrData;
	}
	
	function getActivityEntourage($userid=null) {
		if ($userid) $filter = " AND user_id IN ({$userid}) ";
		else $filter = "";
		
		$filter .= $this->apps->_g('startdate') ? "AND date_time >= '{$this->apps->_g('startdate')}' " : "";
		$filter .= $this->apps->_g('enddate') ? "AND date_time < '{$this->apps->_g('enddate')}' " : "";
		
		$sql = "
			SELECT *,count(*) total
			FROM tbl_activity_log 
			WHERE (action_id IN (10,5,19)) {$filter} 
			GROUP BY action_id,user_id
		";
		// pr($sql);
		$this->apps->forceopendb(0);
		$qData = $this->apps->fetch($sql,1);
		$arrData = false;
		if($qData){
			foreach ($qData as $key =>$val) {
				$arrData[$val['user_id']][$val['action_id']] = $val['total'];
			}
		}
		// pr($arrData);
		return $arrData;
	}

	function getBAperformancesummary($act=null){
		$entourage = $this->getBAentourage();
		$entourageid = false;
		if($entourage) {
			foreach ($entourage['result'] as $k => $v) {
				$entourageid[$k] = $v['entourageid'];
			}
		}
		
		if ($entourageid) $identourage = implode(',',$entourageid);
		else return false;
		if ($act=='competition') {
			$filter = $this->apps->_g('from2') ? "AND date_time >= '{$this->apps->_g('from2')}' " : "";
			$filter .= $this->apps->_g('to2') ? "AND date_time < '{$this->apps->_g('to2')}' " : "";
			
			$sql = "
				SELECT *,SUBSTR(date_time,1,10) date,count(*) total
				FROM tbl_activity_log 
				WHERE action_id = 6 AND action_value = 'competition' AND user_id IN ({$identourage}) {$filter}
				GROUP BY SUBSTR(date_time,1,10) 
			";
			// pr($sql);
			$this->apps->forceopendb(0);
			$qData = $this->apps->fetch($sql,1);
			$arrData = false;
			if($qData){
				foreach ($qData as $key =>$val) {
					$arrData[$val['date']] = $val['total'];
				}
			}
		} elseif($act=='activity') {
			$filter = $this->apps->_g('from3') ? "AND date_time >= '{$this->apps->_g('from3')}' " : "";
			$filter .= $this->apps->_g('to3') ? "AND date_time < '{$this->apps->_g('to3')}' " : "";
			$id_activity = $this->apps->_g('id_activity') ? $this->apps->_g('id_activity') : "10,5,19,6";
			
			if ($id_activity==6 || $id_activity=='10,5,19,6') {
				if ($id_activity==6) {
					$act_value = "AND action_value IN ('competition')";
				}
				if ($id_activity=='10,5,19,6') {
					$act_value = "OR action_value IN ('competition')";
				}
			} else $act_value  = "";
			$sql = "
				SELECT *,SUBSTR(date_time,1,10) date,count(*) total
				FROM tbl_activity_log 
				WHERE (action_id IN ({$id_activity}) {$act_value})
				AND user_id IN ({$identourage}) {$filter}
				GROUP BY SUBSTR(date_time,1,10)
			";
			// pr($sql);
			$this->apps->forceopendb(0);
			$qData = $this->apps->fetch($sql,1);
			$arrData = false;
			if($qData){
				foreach ($qData as $key =>$val) {
					$arrData[$val['date']] = $val['total'];
				}
			}
		}
		return $arrData;
	}
	
	function getBAmember() {
		$qData = false;
		$sql = "SELECT * FROM ba_member WHERE n_status = 1 ORDER BY name,last_name";
		$this->apps->forceopendb(1);
		$qData = $this->apps->fetch($sql,1);
		if($qData) return $qData;
		else return false;
	}
	
	function getSocialMember() {		
		$qData = false;
		if (strip_tags($this->apps->_g('search'))=="Search...") $search = "";
		else $search = strip_tags($this->apps->_g('search'));
		$filter = $search!='' ? "AND (name REGEXP '{$search}' OR last_name REGEXP '{$search}' OR email REGEXP '{$search}')" : "";
		
		$sql = "SELECT * FROM social_member WHERE n_status = 1 {$filter}";
		$this->apps->forceopendb(0);
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function getBAentourage($start=null,$limit=10) {
		$data['result'] = false;
		$data['total'] = 0;
		
		if (intval($this->apps->_g('baid'))) $baid = intval($this->apps->_g('baid'));
		else $baid = false;	
		
		$socialmember = $this->getSocialMember();
		if (strip_tags($this->apps->_g('search'))=="Search..." || strip_tags($this->apps->_g('search'))=="") {
			$filter = "";
		} else {
			if ($socialmember) {
				foreach ($socialmember as $k => $v) {
					$idmember[$k] = $v['id'];
				}
				$id_member = implode(',',$idmember);
			} else $id_member = false;
			$filter = $id_member!='' ? "AND be.entourageid IN ({$id_member})" : "";
		}
		
		$start = intval($this->apps->_g('st'));	
		$filter .= $baid!='' ? "AND be.baid = {$baid}" : "";
		
		$sql_total = "
			SELECT count(*) total
			FROM ba_entourage be
			WHERE be.n_status = 1 {$filter}
			ORDER BY be.id";
		$this->apps->forceopendb(1);
		$total = $this->apps->fetch($sql_total);
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql = "
			SELECT be.*
			FROM ba_entourage be
			WHERE be.n_status = 1 {$filter}
			ORDER BY be.id
			LIMIT {$start},{$limit}
		";
		// pr($sql);
		$this->apps->forceopendb(1);
		$qData = $this->apps->fetch($sql,1);
		$entourageid = false;
		if($qData) {
			foreach ($qData as $k => $v) {
				$entourageid[$k] = $v['entourageid'];
			}
		}
		
		if ($entourageid) $identourage = implode(',',$entourageid);
		else return false;
		if ($socialmember) {
			foreach ($socialmember as $k => $v) {
				$name = $v['name']." ".$v['last_name'];
				$socialmember[$v['id']] = $name;
			}
		} else $socialmember = false;
		// pr($socialmember);
		$arrsurfhome = $this->getSurf($value='home',$identourage); //get total surf home
		$arrbadges = $this->getBadges($identourage); //get total badges
		$arrupload = $this->getUploadContent($identourage); //get total uploads
		$ActivityEntourage = $this->getActivityEntourage($identourage); //get total activity
		
		$arrBAentourage = false;
		if($qData) {
			$no = $start+1;
			foreach ($qData as $k => $v) {
				$v['no'] = $no++;
				if ($socialmember) {
					if (array_key_exists($v['entourageid'],$socialmember)) $v['name'] = $socialmember[$v['entourageid']];
					else $v['name'] = false;
				} else $v['surfhome'] = false;
				if ($arrsurfhome) {
					if (array_key_exists($v['entourageid'],$arrsurfhome)) $v['surfhome'] = $arrsurfhome[$v['entourageid']];
					else $v['surfhome'] = false;
				} else $v['surfhome'] = false;
				if ($arrbadges) {
					if (array_key_exists($v['entourageid'],$arrbadges)) $v['badges'] = $arrbadges[$v['entourageid']];
					else $v['badges'] = false;
				} else $v['badges'] = false;
				if ($arrupload) {
					if (array_key_exists($v['entourageid'],$arrupload)) $v['uploads'] = $arrupload[$v['entourageid']];
					else $v['uploads'] = false;
				} else $v['uploads'] = false;
				if ($ActivityEntourage) {
					if (array_key_exists($v['entourageid'],$ActivityEntourage)) $v['activity'] = $ActivityEntourage[$v['entourageid']];
					else $v['activity'] = false;
				} else $v['activity'] = false;
				$arrBAentourage[$k] = $v;
			}
		}
		
		$data['result'] = $arrBAentourage;
		$data['total'] = intval($total['total']);
		// pr($data);
		return $data;
	}
}
?>