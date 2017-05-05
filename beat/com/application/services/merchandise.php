<?php
class merchandise  extends ServiceAPI{
	
	function beforeFilter(){
	 
		$this->userHelper  = $this->useHelper('userHelper');
		$this->merchandiseHelper  = $this->useHelper('merchandiseHelper');
		$this->messageHelper  = $this->useHelper('messageHelper');
		 
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		 
	}
	
	function lists(){
		$data =  $this->merchandiseHelper->getMerchandise();
		return $data;		
		
	}

	function redeem(){
		$data =  $this->merchandiseHelper->redeemMerchandise();

		return $data;		
		
	}
}
?>