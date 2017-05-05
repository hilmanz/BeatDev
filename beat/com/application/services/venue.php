<?php
class venue extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function main(){
		return false;
	}
	
	function add(){
		$data = $this->checkinHelper->addvenue();
		$venuename = strip_tags($this->_p('venuename'));
		if($data) $this->log("venue","add_{$venuename}");
		return $data;
	}

		
	function search(){
		$data = $this->checkinHelper->searchvenue();
		$coord = strip_tags($this->_request('coor'));
		if($data) $this->log("venue","search_{$coord}");
		if(!$data) return array();
		else return $data;
	}
	
}
?>