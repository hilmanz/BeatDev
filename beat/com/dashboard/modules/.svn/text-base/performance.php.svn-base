<?php
class performance extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->webActivityHelper = $this->useHelper("webActivityHelper");
		$this->dataHelper = $this->useHelper("dataHelper");
		$this->googleAnalyticHelper = $this->useHelper("googleAnalyticHelper");
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
	}
	
	function main(){
		$baid = intval($this->_g('baid'));
		$startdate = $this->_g('startdate');
		$enddate = $this->_g('enddate');
		$startdate_competition = $this->_g('from2');
		$enddate_competition = $this->_g('to2');
		$startdate_activity = $this->_g('from3');
		$enddate_activity = $this->_g('to3');
		$search = strip_tags($this->_g('search'));
		$id_activity = intval($this->_g('id_activity'));
		
		$gaData = $this->googleAnalyticHelper->gaData();
		$data = $this->googleAnalyticHelper->gaDataChart();
		$baMember = $this->dataHelper->getBAmember();
		$baEntourage = $this->dataHelper->getBAentourage();
		$baperformSumCompetition = $this->dataHelper->getBAperformancesummary('competition');
		$baperformSumActivity = $this->dataHelper->getBAperformancesummary('activity');
		
		$this->assign("gaData",$gaData);
		$this->assign("dataChart",$data);
		$this->assign("baid",$baid);
		$this->assign("id_activity",$id_activity);
		$this->assign("startdate",$startdate);
		$this->assign("enddate",$enddate);
		$this->assign("startdate_competition",$startdate_competition);
		$this->assign("enddate_competition",$enddate_competition);
		$this->assign("startdate_activity",$startdate_activity);
		$this->assign("enddate_activity",$enddate_activity);
		$this->assign("search",$search);
		$this->assign("baMember",$baMember);
		$this->assign("baEntourage",$baEntourage['result']);
		$this->assign("baperformSumCompetition",json_encode($baperformSumCompetition));
		$this->assign("baperformSumActivity",json_encode($baperformSumActivity));
		
		$this->Paging = new Paginate();	
		$this->View->assign("paging",$this->Paging->getAdminPaging(intval($this->_g('st')),10,$baEntourage['total'], "?&baid={$baid}&startdate={$startdate}&enddate={$enddate}&search={$search}"));
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/performance.html');
	}
}
?>