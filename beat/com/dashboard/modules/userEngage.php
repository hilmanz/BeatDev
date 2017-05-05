<?php
class userEngage extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->userEngageHelper = $this->useHelper("userEngageHelper");
		
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
		
		/* DROP DOWN FILTER */
		$this->assign('getSba',$this->userEngageHelper->getrecepient('badetail'));
		$this->assign("getBrand",$this->userEngageHelper->getBrand());
		$this->assign("getCity", $this->userEngageHelper->getCity());
		
		$this->assign("sbaid", $this->_g('sbaid'));
		$this->assign("areaid", $this->_g('areaid'));
		$this->assign("brandid", $this->_g('brandid'));
		$this->assign("startdate", $this->_g('startdate'));
		$this->assign("enddate", $this->_g('enddate'));
		
	}
	
	function main(){
		
		$totalUserEngage = $this->userEngageHelper->totalUserEngage();
		$personalPlant = $this->userEngageHelper->personalPlant();
		$personalEngagement = $this->userEngageHelper->personalEngagement();
		// pr($personalPlant);
		$this->assign("totalUserEngage",$totalUserEngage);
		$this->assign("personalPlant",$personalPlant);
		$this->assign("personalEngagement",$personalEngagement);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/user-engagement.html');
	}
	
	function cityAjax(){
	
		$qData = $this->userEngageHelper->cityRef();
		if($qData){
			print json_encode (array('status'=>TRUE, 'rec'=>$qData));
		}else{
			print json_encode(array('status'=>FALSE));
		}
		exit;
	}
}
?>