<?php
class brand  extends ServiceAPI{
			

	function beforeFilter(){
	
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper = $this->useHelper('userHelper');
		
		$this->searchHelper = $this->useHelper('searchHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		
	}
	
	function feeds(){
		global $logger;
		$brandcover = $this->userHelper->getBrandProfile();
		$featarticle = $this->contentHelper->getArticleFeatured();
		
		if($featarticle) {
				// pr($this->user->branddetail);
				$brands = array();
				foreach($this->user->branddetail as $val){
					$brands[$val->id]=$val->id;
				}
				
				$featarticle[0]['image_full_path'] = $brandcover['image_full_path'];
				// pr($brands);
				if(count($brands)>1){
						$featarticle[0]['title'] = "Brand";
						$featarticle[0]['brief'] = "Brand";
				}else{
						$featarticle[0]['title'] = $featarticle[0]['author']['name'];
						$featarticle[0]['brief'] = $featarticle[0]['author']['name'];
				}
				$featarticle[0]['content'] = $brandcover['content'];
			 
			$data['brand'] = $featarticle;
		
		}else $data['brand'] = array();
		
		// $data['brand'] = array();
		
		$articlelist = $this->contentHelper->getArticleContent(null,9,0,array(0,1,3));
		
		$data['timeline']['total'] =intval($articlelist['total']);
		if($articlelist['result'])$data['timeline']['posts'] =$articlelist['result'];
		else $data['timeline']['posts'] = array();
		$data['timeline']['pages'] =$articlelist['pages'];
		
		$this->log('surf','timeline');
		// $logger->log(json_encode($data));
		return $data;
	}
	
	function detail(){		
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
	function ambasador(){
		return $this->userHelper->getsbaentouragelist();
	}
}
?>
