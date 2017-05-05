<?php
class barank extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
	
		$this->baRankHelper = $this->useHelper("baRankHelper");

		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		$this->assign('basedomainpath',$CONFIG['BASE_DOMAIN_PATH']);
		$this->assign('locale', $LOCALE[1]);
		
		/* DROP DOWN FILTER */
		$this->assign('getSba',$this->baRankHelper->getrecepient('badetail'));
		$this->assign("getBrand",$this->baRankHelper->getBrand());
		$this->assign("getCity", $this->baRankHelper->getCity());
		
		$this->assign("sbaid", $this->_g('sbaid'));
		$this->assign("areaid", $this->_g('areaid'));
		$this->assign("brandid", $this->_g('brandid'));
		$this->assign("activity", $this->_g('activity'));
		$this->assign("startdate", $this->_g('startdate'));
		$this->assign("enddate", $this->_g('enddate'));
	}
	
	function main(){
		
		$baRankList = $this->baRankHelper->baRankList();
		$total = $this->baRankHelper->getCount();
		// pr($total);
		$this->assign("baRankList",$baRankList);
		$this->assign("total",$total);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/ba-rank.html');
	}
	
	function cityAjax(){
	
		$qData = $this->dataHelper->cityRef();
		if($qData){
			print json_encode(array('status'=>TRUE, 'rec'=>$qData));
		}else{
			print json_encode(array('status'=>FALSE));
		}
		exit;
	}
	
	function brandAjax(){
	
		$qData = $this->dataHelper->brandDropref();
		if($qData){
			print json_encode(array('status'=>TRUE, "rec"=>$qData));
		}else{
			print json_encode(array("status"=>FALSE));
		}
		
	}
	
	function baRankListAjax(){
	
		mysql_query("SET CHARACTER SET utf8");
		
		$start = strip_tags($this->_p('start'));
		$typefilter = strip_tags($this->_request('activity'));
		$limit = 10;
		$baRankList = $this->baRankHelper->baRankList($start,$limit);
		
		$total = $this->baRankHelper->getCount();
		
		print json_encode(array('data'=>$baRankList, 'total'=>$total));exit;
	}
	
	function barankReport(){
			$baRankList = $this->baRankHelper->baRankList(0,10,'',true);
			$this->assign("baRankList",$baRankList);
			$filename = "BA_RANK_report".date('Ymd_gia').".xls";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			// echo "Some Text"; //no ending ; here
			// pr($baRankList);
			print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'widgets/ba-rank-report.html');
			exit;
	}
	
}
?>