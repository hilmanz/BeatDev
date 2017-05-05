<?php
class questioner  extends ServiceAPI{
	
	function beforeFilter(){
	 
		$this->userHelper  = $this->useHelper('userHelper');
		$this->brandQuestionerHelper  = $this->useHelper('brandQuestionerHelper');
		$this->deviceMopHelper  = $this->useHelper('deviceMopHelper');
		 
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		 
	}
	
	function brands(){
		$data =  $this->brandQuestionerHelper->saveQuestionerBrand();
		return $data;		
		
	}

 
}
?>