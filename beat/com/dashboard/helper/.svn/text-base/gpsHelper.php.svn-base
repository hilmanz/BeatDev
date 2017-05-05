<?php 

class gpsHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		
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

	function getGPSData(){
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
		$scale = 100000;
			$sql = "
			SELECT gps.*, COUNT( * ) multiples,  ROUND(abs(gps.longitude*{$scale})+abs(gps.latitude*{$scale})) groupdata
			FROM `tbl_activity_gps` gps
			LEFT JOIN social_member sm ON gps.userid = sm.id
			LEFT JOIN my_pages pages ON pages.ownerid = gps.userid 
			WHERE 
			sm.n_status =1 
			AND DATE(gps.datetime) >= '{$this->startdate}'
			AND DATE( gps.datetime) <= '{$this->enddate}'
			{$this->qSbaFilter}
			{$this->qAreaFilter}
			{$this->qBrandFilter}
			GROUP BY groupdata
			ORDER BY groupdata DESC  ";
			
			$qData = $this->apps->fetch($sql,1);
			
			
			// pr($sql);
			// pr($qData);exit;
		 
			return $qData;
		}
		return false;
	}
	 
	
}

?>

