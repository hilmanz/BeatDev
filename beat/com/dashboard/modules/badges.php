<?php
class badges extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->badgesHelper = $this->useHelper("badgesHelper");
		$this->userEngageHelper = $this->useHelper("userEngageHelper");
		
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
		$this->assign("getCity", $this->userEngageHelper->getCity());
		
		$this->assign("areaid", $this->_g('areaid'));
		$this->startdate = $this->_g('startdate');
		$this->enddate = $this->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
	}
	
	function main(){
	
		$totalBadgesCollect = $this->badgesHelper->totalBadgesCollect();
		$top10badges = $this->badgesHelper->top10badges();
		$top10userBadges = $this->badgesHelper->top10userBadges();
		// $top10act = $this->badgesHelper->top10act();
		$top10act = $this->badgesHelper->gettopactivitybadges();
		// pr($top10userBadges);
		$this->assign("totalBadgesCollect",$totalBadgesCollect);
		$this->assign("top10badges",$top10badges);
		$this->assign("top10userBadges",$top10userBadges);
		$this->assign("top10act",$top10act);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/badges.html');
	}
	
	function cityAjax(){
	
		$qData = $this->badgesHelper->cityRef();
		if($qData){
			print json_encode (array('status'=>TRUE, 'rec'=>$qData));
		}else{
			print json_encode(array('status'=>FALSE));
		}
		exit;
	}
}
?>