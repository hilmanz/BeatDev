<?php
class user_statistic{
	
	function __construct($apps=null){		
			$this->apps = $apps;	
			global $LOCALE,$CONFIG;
			$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
			$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
			$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		//$data = $this->apps->homeHelper->getnewestpost();
		//$this->apps->assign('newest_post',$data);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/user-statistic.html");
	
	}


}


?>