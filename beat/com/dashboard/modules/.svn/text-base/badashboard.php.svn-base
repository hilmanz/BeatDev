<?php
error_reporting(0);
class badashboard extends App{
	
	
	function beforeFilter(){
	
		$this->dataHelper = $this->useHelper("dataHelper");
		$this->baentourageHelper = $this->useHelper('baentourageHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
				
		$this->assign('locale', $LOCALE[1]);
		$this->assign('startdate', $this->_g('startdate'));
		$this->assign('enddate', $this->_g('enddate'));
	
	}
	
	function main(){

		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/ba-dashboard.html');
		
	}
	
	function uploads(){
		// pr('masuk');exit;
		if($this->_p('upload')=='badata'){
			 $out = $this->baentourageHelper->senddatabatoentourage();		 
		}
		$this->assign('out',$out);
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/ba-dashboard.html');
	}
}
?>