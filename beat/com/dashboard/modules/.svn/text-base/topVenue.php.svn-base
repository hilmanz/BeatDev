<?php
class topVenue extends App{
	
	
	function beforeFilter(){
	
		$this->topVenueHelper = $this->useHelper("topVenueHelper");
		$this->webActivityHelper = $this->useHelper("webActivityHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		
		$this->assign('rateType',$this->_g('rateType'));
		
		$this->assign('locale', $LOCALE[1]);
		

	}
	
	function main(){
	
		$top10venue = $this->topVenueHelper->top10venue();
		$venue = $this->topVenueHelper->getVenueData();
		$venueid = strip_tags($this->_request('venueid'));
		$data['venue'] =$venue['lists'];
		$data['wordcloud'] = json_decode($this->webActivityHelper->contentComment('content',$venue['cid']));
		// pr($topVisitedPage);
		$this->assign("top10venue",$top10venue);
		$this->assign("venuedata",$data);
		$this->assign("venueid",$venueid);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/topvenue.html');
		
	}
	
	function ajax(){
		$data['result'] = false;
	 
		$needs = strip_tags($this->_p('needs'));
		if($needs=='getvenuedata'){
			$data['result'] = true;
			$venue = $this->topVenueHelper->getVenueData();
			$data['venue'] =$venue['lists'];
			$data['wordcloud'] = json_decode($this->webActivityHelper->contentComment('content',$venue['cid']));
		}
		print json_encode($data);
		exit;
	}
}
?>