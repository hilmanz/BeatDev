<?php
class line_chart{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}


		
	function main($limit=7){
		$start = intval($this->apps->_p('start'));
		
		$startdate = strip_tags($this->apps->_p('startdate'));
		$enddate = strip_tags($this->apps->_p('enddate'));
		if ($startdate == "" && $enddate==""){
			$this->apps->Request->setParamPost('startdate',date("Y-m-d",strtotime(date('Y-m-d'). "-7 day")));
			$this->apps->Request->setParamPost('enddate',date('Y-m-d'));
		}
	
		$this->apps->View->assign('entourage', json_encode($dataentourage));
		// pr($dataentourage); 
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/line-chart.html");
	}
	

}
?>