<?php
class gps extends App{
	
	
	function beforeFilter(){
	
		$this->gpsHelper = $this->useHelper("gpsHelper");
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

		$datacitygps = $this->gpsHelper->getGPSData();
 
		$brandPref = $this->dataHelper->brandPref();
		$genderPref = $this->dataHelper->genderPref();
		$agePref = $this->dataHelper->agePref();
		// pr($brandDropref);
		$this->assign("datacitygps",$datacitygps);
		 
		$this->assign("brandPref",$brandPref);
		$this->assign("genderPref",$genderPref);
		$this->assign("agePref",$agePref);
				
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','gps');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/gps.html');

	}
	 
}
?>