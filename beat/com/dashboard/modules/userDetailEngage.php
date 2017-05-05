<?php
class userDetailEngage extends App{
	
	
	function beforeFilter(){
	
		$this->userDetailEngageHelper = $this->useHelper("userDetailEngageHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		
		$this->assign('locale', $LOCALE[1]);
		

	}
	
	function main(){
		
		$entourageEngage = $this->userDetailEngageHelper->entourageEngage();
		$total = $this->userDetailEngageHelper->totalEntourage();
		// pr($entourageEngage);
		$this->assign("entourageEngage",$entourageEngage);
		$this->assign("total",$total);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/user-detail-engage.html');
		
	}
	
	function userPagingAjax(){
		$start = strip_tags($this->_p('start'));
		$limit = 10;
		$entourageEngage = $this->userDetailEngageHelper->entourageEngage($start,$limit);
		
		$total = $this->userDetailEngageHelper->totalEntourage();
		
		print json_encode(array('data'=>$entourageEngage, 'total'=>$total));exit;
	}
	
}
?>