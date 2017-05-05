<?php
class plan  extends ServiceAPI{
			

	function beforeFilter(){
	
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		
		$this->searchHelper = $this->useHelper('searchHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		
	}
	
	function feeds(){
		// $featarticle = $this->contentHelper->getArticleFeatured();
		// $data['brand'] = $featarticle[0];
		$articlelist = $this->contentHelper->getArticleContent(null,200,4,array(0,3),"plan");
		if($articlelist['result']){
			foreach($articlelist['result'] as $key => $val){
				if($val['rating']){
					
					$articlelist['result'][$key]['brief'] = $val['rating']['venue'];
				}
				if($val['rating']){
				}else{
					if($val['author']['pagesdetail']['plantype']=='Co-Creation'||$val['author']['pagesdetail']['plantype']=='Brand') {
						$newlisttags = false;
						foreach($articlelist['result'][$key]['friendtags'] as $kyes => $val){
							if(($val['role']!="SBA")&&($val['role']!="PL")&&($val['role']!="BRAND")) $newlisttags[] = $val;
						}
						 $articlelist['result'][$key]['friendtags'] = $newlisttags;
					}
				}
			}
		}
		$data['plan']['total'] =intval($articlelist['total']);
		if($articlelist['result']) $data['plan']['posts'] =$articlelist['result'];
		else $data['plan']['posts'] =array();
		$data['plan']['pages'] =$articlelist['pages'];
		$this->log('surf','plan');
		return $data;
	}
	
	function detail(){		
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
	
		
}
?>
