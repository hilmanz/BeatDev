<?php
class reporting extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->reportingHelper = $this->useHelper("reportingHelper");
		
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
	}
	
	function main(){
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/reporting.html');
	}
}
?>