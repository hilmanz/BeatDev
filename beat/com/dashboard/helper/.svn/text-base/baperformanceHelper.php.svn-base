<?php

class baperformanceHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
		 $this->data = false;
	}
	
	
		 
	function performance() {
		$startdate = $this->apps->_g('startdate');
		$enddate = $this->apps->_g('enddate');	
		if($enddate=='') $enddate = date('Y-m-d');		
		if($startdate=='') $startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($enddate)) );
				
		$sql = "SELECT 
					SUM(page_views) numPageViews , 
					SUM(visits) numVisits, 
					SEC_TO_TIME(AVG(visitDuration)) time_onSite,
					SEC_TO_TIME(AVG(time_onPage)) numTimeOnPage,
					ROUND(AVG(bounce_rate)) numBounceRate,
					SEC_TO_TIME(AVG(time_onSite)) numVisitDuration,
					SUM(unique_visitor) numUniqueVisitor,					
					date_d 

				FROM `ga_daily_data`
				WHERE 
				date_d >= '{$startdate}'
				AND date_d <= '{$enddate}'
				";
				// pr($sql);
		$this->apps->open(1);// this how to use other server data
		$qData = $this->apps->fetch($sql);
		return $qData;
	}
	
	function summaryCompetition() {
		$sql = "SELECT num, provinceName FROM tbl_province WHERE provinceName IS NOT NULL AND provinceName <> ''  ORDER BY num DESC LIMIT 7 ";
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		return $qData;
		
	}
	
	function summaryEntourage() {
		$sql = "SELECT num, provinceName FROM tbl_province WHERE provinceName IS NOT NULL AND provinceName <> ''  ORDER BY num DESC LIMIT 7 ";
		$this->apps->open(1);
		$qData = $this->apps->fetch($sql,1);
		return $qData;
		
	}
	
?>