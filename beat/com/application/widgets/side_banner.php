<?php
class side_banner{
	
	function __construct($apps=null){		
			$this->apps = $apps;
			global $LOCALE,$CONFIG;
			$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
			$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
			$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		$page = $this->apps->_g('page');
		if($page=='')$page='home';
		$data = $this->apps->contentHelper->getBanner($page,"side_banner");
		// pr($data);
		$this->apps->assign('banner',$data);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/side-banner.html");	
	}
}
?>