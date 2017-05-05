<?php
class weekly_popular{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		$data = $this->apps->contentHelper->weeklyPopular();
		$this->apps->assign('weekly_popular',$data);
		
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/weekly-popular.html");	
	}
}
?>