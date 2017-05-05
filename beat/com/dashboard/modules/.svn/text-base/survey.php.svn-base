<?php
class survey extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->surveyHelper = $this->useHelper("surveyHelper");
		
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
	}
	
	function main(){
		global $LOCALE,$CONFIG;
		sendRedirect("{$CONFIG['DASHBOARD_DOMAIN']}");		 
		exit;
		
		$totalPersonalPlant = $this->surveyHelper->totalPersonalPlant();
		$totalCocreationPlant = $this->surveyHelper->totalCocreationPlant();
		// pr($totalPersonalPlant);
		$this->assign("totalPersonalPlant",$totalPersonalPlant);
		$this->assign("totalCocreationPlant",$totalCocreationPlant);
				
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/survey.html');
	}
}
?>