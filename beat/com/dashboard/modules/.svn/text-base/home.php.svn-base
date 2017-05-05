<?php
class home extends App{
	
	
	function beforeFilter(){
	
		$this->dataHelper = $this->useHelper("dataHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
				
		$this->assign('locale', $LOCALE[1]);
		$this->assign('startdate', $this->_g('startdate'));
		$this->assign('enddate', $this->_g('enddate'));
		
		$this->assign('startdatesub', $this->_g('startdatesub'));
		$this->assign('enddatesub', $this->_g('enddatesub'));

		/* DROP DOWN FILTER */
		//$this->assign('getSba',$this->dataHelper->getrecepient('badetail'));
		$this->assign("getBrand",$this->dataHelper->getBrand());
		$this->assign("getCity", $this->dataHelper->getCity());
		
		$this->assign("sbaid", $this->_g('sbaid'));
		$this->assign("areaid", $this->_g('areaid'));
		$this->assign("brandid", $this->_g('brandid'));
		$this->assign("monthdate", $this->_g('monthdate'));
		$this->assign("cityname",strip_tags($this->_g('searchCity')));
	}
	
	function main(){

		$weekAchievmentKpi = $this->dataHelper->weekAchievmentKpi();
		$weeklyCumulativeEngage = $this->dataHelper->weeklyCumulativeEngage();
		$engagemenentpreformance = $this->dataHelper->engagemenentpreformance();		 
		$cityEngagementPerformance = $this->dataHelper->engagemenentpreformancebycity();		 
		
		
		
		$brandPref = $this->dataHelper->brandPref();
		$genderPref = $this->dataHelper->genderPref();
		$agePref = $this->dataHelper->agePref();
		 
				// pr($brandDropref);
		$this->assign("weekAchievmentKpi",$weekAchievmentKpi);
		$this->assign("weeklyCumulativeEngage",$weeklyCumulativeEngage);
		
				
		$this->View->assign('thedata',json_encode($this->getchartarchivement()));
			
		$this->assign("brandPref",$brandPref);
		$this->assign("genderPref",$genderPref);
		$this->assign("agePref",$agePref);
		
		$this->assign("engagemenentpreformance",$engagemenentpreformance);
		$this->assign("cityEngagementPerformance",$cityEngagementPerformance);
	
		
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/user.html');

	}
	
	function getchartarchivement(){
		$entourage['result'] = array();
		$nextpage = 1;
		while($nextpage>0){
		 
			if($nextpage==1)$nextpage = 0;
			$mydata = $this->dataHelper->entouragechart(null,$nextpage,30);
			if($mydata){
					if($mydata['result']){
						foreach($mydata['result'] as $val){
								$entourage['result'][] = $val;								
						}
					}
			}
			$nextpage=intval($mydata['pages']['nextpage']);
			usleep(500);
		}
		// $entourage = $this->entourageHelper->getEntourage(null,0,10,true);
		$dataentourage['data'] =  array();
		$dataentourage['gender'] =  array();
		$dataentourage['brand'] =  array();
		$dataentourage['age'] =  array();
		
		$rangedate = false;
			 // pr($entourage);
		// exit;
		if($entourage){
			$dataentourage['data'][1] = array();
			$dataentourage['data'][2] = array();
			$dataentourage['brand']['Our'] = 0;
			$dataentourage['brand']['PMI'] = 0;
			$dataentourage['brand']['Competitor'] = 0;
			$dataentourage['gender']["M"] = 0;
			$dataentourage['gender']["F"]= 0;
			$dataentourage['age'][18]= 0;
			$dataentourage['age'][24]= 0;
			$dataentourage['age'][40]= 0;
			
			if($entourage['result']){
		
				
				foreach($entourage['result'] as $val){			
					$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
					$rangedate[$datetimes] = (string)date("Y-m-d",strtotime($val['register_date']));
					$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage['data'][$val['n_status']][$datetimes]+1;					
					$dataentourage['gender'][strtoupper(substr($val['sex'],0,1))]=@$dataentourage['gender'][substr($val['sex'],0,1)]+1;					
					if(array_key_exists('brandrelevancyour',$val))@$dataentourage['brand']['Our']+=$val['brandrelevancyour'];
					if(array_key_exists('brandrelevancypmi',$val))@$dataentourage['brand']['PMI']+=$val['brandrelevancypmi'];
					if(array_key_exists('brandrelevancycompetitor',$val))@$dataentourage['brand']['Competitor']+=$val['brandrelevancycompetitor'];
					$birthday = $this->dataHelper->getAge($val['birthday']);
					if(($birthday>=18) && ($birthday <=24) ) $dataentourage['age'][18]=@$dataentourage['age'][18]+1;					
					if(($birthday>=25) && ($birthday <=29) ) $dataentourage['age'][24]=@$dataentourage['age'][24]+1;					
					if( $birthday>=30  ) $dataentourage['age'][40]=@$dataentourage['age'][40]+1;	
					
					if( $val['n_status']==1 ) $dataentourage['total_per_status'][1]++;	
					if( $val['n_status']==2 ) $dataentourage['total_per_status'][2]++;		
				 						
				}
				$newdata = false;
				sort($rangedate);
				$mindate = strtotime(min($rangedate));
				$maxdate = strtotime(max($rangedate));
				
				$startdate = strip_tags($this->_p('startdate'));
				$enddate = strip_tags($this->_p('enddate'));
				if($startdate){
					if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
					$mindate = strtotime($startdate);
					$maxdate = strtotime($enddate);
				}
				$totaldate = ($maxdate - $mindate) / (60*60*24);
				
				
				for($i=0;$i<=$totaldate;$i++){
				// pr($totaldate);
					$dates = date("Y-m-d",$mindate);
					$val = date("Y-m-d" , strtotime("{$dates} +{$i} day"));
					// pr($val);
					foreach($dataentourage['data'] as $key => $valve) {					
						if(array_key_exists($val,$dataentourage['data'][$key])) $newdata[$key][$val] = $dataentourage['data'][$key][$val];
						else $newdata[$key][$val] = 0;
					}
					
				}
				if($dataentourage['brand']){
					 
					$compet = intval(@$dataentourage['brand']['Competitor']);
					$our = intval(@$dataentourage['brand']['Our']);
					
					$dataentourage['brand']['Competitor'] =$our ;
					$dataentourage['brand']['Our'] =$compet ;
				}
				$dataentourage['data'] = $newdata;
				
			}
		}
		// pr($dataentourage);
		return $dataentourage;
	}
	
	function ajax_load_ba(){
		$listBA = $this->dataHelper->listBA();
		print json_encode($listBA);exit;
	}

	function  ajax(){	
	
		$qData = $this->dataHelper->testBA();
		// pr($qData);
		if($qData){
			print json_encode(array('status'=>TRUE, 'rec'=>$qData));
		}else{
			print json_encode(array('status'=>FALSE));
		}
		
		exit;		
		
	}
	
	function cityAjax(){
	
		$qData = $this->dataHelper->cityRef();
		if($qData){
			print json_encode(array('status'=>TRUE, 'rec'=>$qData));
		}else{
			print json_encode(array('status'=>FALSE));
		}
		exit;
	}
	
	function brandAjax(){
	
		$qData = $this->dataHelper->brandDropref();
		if($qData){
			print json_encode(array('status'=>TRUE, "rec"=>$qData));
		}else{
			print json_encode(array("status"=>FALSE));
		}
		
	}
	
	function cityref(){
			$cityRef = $this->dataHelper->cityRef();
			print json_encode($cityRef);
			exit;
	}
	
	function wavskpireport(){
			$weekAchievmentKpiReport = $this->dataHelper->weekAchievmentKpiReport(true);
			$this->assign("weekAchievmentKpiReport",$weekAchievmentKpiReport);
			$filename = "wavskpi_report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
	
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/user-report.html');
			exit;
	}
	
	function cumulativevskpireport(){
			$weeklyCumulativeEngageReport = $this->dataHelper->weeklyCumulativeEngageReport(true);
			$this->assign("weeklyCumulativeEngageReport",$weeklyCumulativeEngageReport);
			$filename = "cumulative_wavskpi_report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
	
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/cumulative-engage-report.html');
			exit;
	}
	
	function genderAgeBrandReport(){
			$brandPrefReport = $this->dataHelper->brandPrefReport(true);
			$genderPrefReport = $this->dataHelper->genderPrefReport(true);
			$agePrefReport = $this->dataHelper->agePrefReport(true);
			$this->assign("brandPrefReport",$brandPrefReport);
			$this->assign("genderPrefReport",$genderPrefReport);
			$this->assign("agePrefReport",$agePrefReport);
			$filename = "genderAgeBrand_Report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
			// pr($brandPrefReport);
			// pr($genderPrefReport);
			// pr($agePrefReport);
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/genderAgeBrand-report.html');
			exit;
	}
	
	function engengereport(){
			$engagemenentpreformanceReport = $this->dataHelper->engagemenentpreformanceReport(true); 
			$this->assign("engagemenentpreformanceReport",$engagemenentpreformanceReport); 
			$filename = "engagement_Report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
			// pr($engagemenentpreformanceReport);
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/engagement-report.html');
			exit;
	}
	
	function engengecityreport(){
			$engagemenentpreformancebycityreport = $this->dataHelper->engagemenentpreformancebycityreport(true); 
			$this->assign("engagemenentpreformancebycityreport",$engagemenentpreformancebycityreport); 
			$this->assign("searchCity",strip_tags($this->_g('searchCity'))); 
			$filename = "engengecity_Report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
			// pr($engagemenentpreformancebycityreport);
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/engagement-city-report.html');
			exit;
	}
	
}
?>