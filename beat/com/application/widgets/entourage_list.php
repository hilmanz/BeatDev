<?php
class entourage_list{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){
		$start = intval($this->apps->_p('start'));
		$entourage = $this->apps->entourageHelper->getEntourage();
		if($entourage){
			$this->apps->View->assign('entourage',$entourage['result']);
		}else  $this->apps->View->assign('total',0);
		
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/entourage-list.html");
	}
	
}
?>