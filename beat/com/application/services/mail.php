<?php
class mail  extends ServiceAPI{
			

	function beforeFilter(){
	
		$this->newsHelper = $this->useHelper('newsHelper');
	
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		
	}
	
	function pin(){
		$data['result'] = false;
		$data['message'] = "oops,  sorry your pin or email not founded  ";
		$data['code'] = 0;
		$res =  $this->newsHelper->sendpinmail();
		if($res) {
			$data['result'] = true;
			$data['message'] = "success send pin to mail";
			$data['code'] = 1;
		}
		return $data;
	
	}
	
	function forgotpassword(){
		$data['result'] = false;
		$data['message'] = "oops, sorry your email not founded ";
		$data['code'] = 0;
		$res = $this->newsHelper->forgotpassword();
		if($res) {
			$data['result'] = true;
			$data['message'] = "success send new password to mail";
			$data['code'] = 1;
		}
		return $data;
	
	}
	
		
}
?>
