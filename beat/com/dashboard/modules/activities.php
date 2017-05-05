<?php
class activities extends App{
	
	
	function beforeFilter(){
	
		$this->webActivityHelper = $this->useHelper("webActivityHelper");
		$this->topVenueHelper = $this->useHelper("topVenueHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		
		$this->assign('rateType',$this->_g('rateType'));
		$this->assign('searchCity',$this->_g('searchCity'));
		$this->assign('locale', $LOCALE[1]);
		
		$this->assign("startdate", $this->_g('startdate'));
		$this->assign("enddate", $this->_g('enddate'));
		

	}
	
	function main(){
		
		$topVisitedPage = $this->webActivityHelper->topVisitedPage();
		$top10venue = $this->topVenueHelper->top10venue();
		$contentComment = $this->webActivityHelper->contentComment();
		$commentUser = $this->webActivityHelper->contentComment('comment');
		// pr($commentUser);
		$topRedeemMerch = $this->webActivityHelper->topRedeemMerch('comment');
		// pr($topVisitedPage);
		$this->assign("topVisitedPage",$topVisitedPage);
		$this->assign("top10venue",$top10venue);
		$this->assign("cloud",$contentComment);
		$this->assign("commentcloud",$commentUser);
		$this->assign("topRedeemMerch",$topRedeemMerch);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/activities.html');
		
	}
	
	function cityref(){
			$cityRef = $this->webActivityHelper->cityRef();
			print json_encode($cityRef);
			exit;
	}
	
	function visitPageReport(){
			$topVisitedPageReport = $this->webActivityHelper->topVisitedPageReport(true);
			$this->assign("topVisitedPageReport",$topVisitedPageReport);
			$filename = "visit_page_report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
	
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/visit-page-report.html');
			exit;
	}
	
	function venueReport(){
			$top10venueReport = $this->topVenueHelper->top10venueReport(true);
			$this->assign("top10venueReport",$top10venueReport);
			$filename = "venue_report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
		// pr($top10venueReport);
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/venue-report.html');
			exit;
	}
	
}
?>