<?php
class challenge extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		 
		$this->userHelper = $this->useHelper('userHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		
	
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function lists(){
		$data = $this->contentHelper->getArticleContent(null,1,3,array(0,3),"challenge" );
		
		if($data['result']){
			foreach($data['result'] as $key =>$val){
				$data['result'][$key]['badgeid'] = false;
				$data['result'][$key]['point'] = "0";
				$data['result'][$key]['badge_image_full_path'] = "";
				
				$data['result'][$key]['posted_date'] = date("d/m/Y",strtotime($val['posted_date']));
				$data['result'][$key]['expired_date'] = date("d/m/Y",strtotime($val['expired_date']));
				
				
				$challengeData = $this->contentHelper->getChallangeData($val['id']);
				if($challengeData){
					if($challengeData['badgeid']) $data['result'][$key]['badgeid'] = $challengeData['badgeid'];
					if($challengeData['prize'])  $data['result'][$key]['point'] = $challengeData['prize'];
					if($challengeData['image_full_path']) $data['result'][$key]['badge_image_full_path'] = $challengeData['image_full_path'];
				}
			}
		}
		return $data;
	}
	 
	
}
?>