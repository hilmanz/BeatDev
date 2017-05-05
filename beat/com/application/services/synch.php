<?php
class synch  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->activityHelper   = $this->useHelper('activityHelper');
		$this->entourageHelper    = $this->useHelper('entourageHelper');
		$this->messageHelper     = $this->useHelper('messageHelper');
		$this->synchHelper     = $this->useHelper('synchHelper');

		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	
	function friends(){
				
		return $this->synchHelper->getFriends(false,10);

		
		
	}
	
	 function entourage(){
		
		return $this->synchHelper->getEntourage(null,0,1000,true);

	}
	
	function notification(){
			return $this->synchHelper->getNotificationCount();

	}
	
	function tagslist(){
				
		return $this->synchHelper->getFriendsTagList(false,10);

		
		
	}
	
}
?>