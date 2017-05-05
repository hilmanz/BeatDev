<?php
class myband_calendar{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		$this->apps->assign('user',$this->apps->user);
		$data = $this->apps->contentHelper->getMyCalendar($this->apps->user->pageid,2);

		$this->apps->assign('my_calendar',$data);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/myband-calendar.html");
	
	}


}


?>