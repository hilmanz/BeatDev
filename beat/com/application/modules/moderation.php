<?php
class moderation extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		$this->checkinmodHelper = $this->useHelper('checkinmodHelper');
		$this->entouragemodHelper = $this->useHelper('entouragemodHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
		
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		
		$branduser = false;
		$areauser = false;
		$pluser = false;
		$bauser = false;
		
		if(in_array($this->user->leaderdetail->type,array(2,4))) $branduser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4))) $areauser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4,5))) $pluser = true;
		if(in_array($this->user->leaderdetail->type,array(2))) $bauser = true;
		
		$this->assign('branduser',$branduser);
		$this->assign('areauser',$areauser);
		$this->assign('pluser',$pluser);
		$this->assign('bauser',$bauser);
		
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('publishedtype',$this->_request('publishedtype'));
	}
	
	function main(){
		$artikeltype="timeline";
		$content=0;
		
		if($this->_request('articeltype')=='plan'||$this->_request('articeltype')=='5')
		{
			$artikeltype= 'plan';
			$content=4;
		}
		
		$articlelist = $this->contentmodHelper->getArticleContent(null,10,$content,array(0,3),$artikeltype);
		
		$time['time'] = '%H:%M:%S';
		$this->assign('act',$artikeltype);
		$this->assign('total',intval($articlelist['total']));
		$this->assign('timeline',$articlelist['result']);
		$this->assign('timelines','ok');
		$this->assign('time',$time);
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	}
	
	function detail(){
		$cidStr = intval($this->_request('id'));		
		$article = $this->contentmodHelper->getDetailArticle();
		$comment = $this->contentmodHelper->getComment($cidStr,false,0,10);
		
		$this->assign('detail',$article['result']);
		$this->assign('comment',$comment[$cidStr]);
		$this->assign('cidStr',$cidStr);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/inbox-detail.html');
	}
	
	function addComment(){
		global $CONFIG;
		
		$cid = intval($this->_p('cid'));
		$data = $this->contentmodHelper->addComment();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/detail/{$cid}");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function unpublish(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('publishedtype',3);
		$this->Request->setParamPost('type',3);
		
		$data = $this->contentmodHelper->unContentPost();
		if($this->_request('articeltype')=='plan'||$this->_request('articeltype')=='5')
		{
			sendRedirect($CONFIG['BASE_DOMAIN']."moderation?articeltype=plan&&publishedtype=1");
			
		}
		else
		{
			sendRedirect($CONFIG['BASE_DOMAIN']."moderation");
		}
		
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	function unpublishGallery(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('publishedtype',3);
		$this->Request->setParamPost('type',3);
		pr($this->_request('id'));
		$data = $this->contentmodHelper->unContentPost();
		
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/galleryList?publishedtype=1");
			
		
		
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	function commentList(){
		$commentlist = $this->contentmodHelper->getCommentModeration(null,10,3);
		//pr($commentlist);
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($commentlist['total']));
		$this->assign('comment',$commentlist['result']);
		$this->assign('act',"commentList");
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	}
	
	function venueList(){
		$venuelist = $this->checkinmodHelper->getVenue(null,10);

		$this->assign('search',$this->_p('search'));
		$this->assign('searchType',$this->_p('searchType'));
		$this->assign('total_venue',intval($venuelist['total']));
		$this->assign('venue',$venuelist['result']);
		$this->assign('act',"venueList");
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	}
	
	function galleryList(){
		$articlelist = $this->contentmodHelper->getGalleryTypeContent(null,10);
		
		$time['time'] = '%H:%M:%S';
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($articlelist['total']));
		$this->assign('gallery',$articlelist['result']);
		$this->assign('time',$time);
		$this->assign('act',"galleryList");
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	
	}
	
	
	function detailVenue(){
		$cidStr = intval($this->_request('id'));
		$article = $this->checkinmodHelper->getVenue(null,10,"detail");
	
		
		$category = $this->contentmodHelper->getCategoryVenue();
		$province = $this->contentmodHelper->getListProvinceVenue();
		$venueCat = $this->contentmodHelper->getCategoryVenue();
		
		$this->assign('detail',$article['result'][0]);
		$this->assign('category',$category);
		$this->assign('province',$province);
		$this->assign('venue_category',$venueCat);
		$this->assign('cidStr',$cidStr);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/edit-venue.html');
	}
	
	function unpublishvenue(){
		global $CONFIG;
		$this->Request->setParamPost('publishedtype',3);
		$data = $this->contentmodHelper->unVenueReference();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/venueList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function editVenue(){
		global $CONFIG;
		
		$data = $this->contentmodHelper->editVenue();
		
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/venueList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function uncomment(){
		global $CONFIG;
		$this->Request->setParamPost('publishedtype',3);
		$this->Request->setParamPost('id',intval($this->_request('id')));		
		$data = $this->contentmodHelper->unCommentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/commentList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));

		$start = intval($this->_request("start"));
		if ($this->_request("startdate") || $this->_request("enddate") || $this->_request("search")) {
			$this->Request->setParamPost('startdate',$this->_request('startdate'));
			$this->Request->setParamPost('enddate',$this->_request('enddate'));
			$this->Request->setParamPost('search',$this->_request('search'));
		}
		
		if($needs=="post_moderation") $data =  $this->contentmodHelper->getArticleContent($start,10,0,array(0,3),"timeline");
		if($needs=="plan_moderation") $data =  $this->contentmodHelper->getArticleContent($start,10,4,array(0,3),"plan");
		if($needs=="gallery_moderation") $data =  $this->contentmodHelper->getGalleryTypeContent($start,10);
		if($needs=="comment_moderation") $data =  $this->contentmodHelper->getCommentModeration($start,10,3);
		if($needs=="venue_moderation") $data =  $this->checkinmodHelper->getVenue($start,10);
		if($needs=="load_city_moderation"){
			$data =  $this->contentmodHelper->getListCityVenue($this->_p('provinceName'));
		}
		
		print json_encode($data);exit;
	}
	
	
	function publishit(){
		
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('publishedtype',1);	
		$this->Request->setParamPost('type',3);
		
		$data = $this->contentmodHelper->unContentPost();
		if($this->_request('articeltype')=='plan'||$this->_request('articeltype')=='5')
		{
			sendRedirect($CONFIG['BASE_DOMAIN']."moderation?articeltype=plan&&publishedtype=3");
			
		}
		else
		{
			sendRedirect($CONFIG['BASE_DOMAIN']."moderation");
		}
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	function publishitGallery(){
		
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('publishedtype',1);	
		$this->Request->setParamPost('type',3);
		
		$data = $this->contentmodHelper->unContentPost();
	
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/galleryList?publishedtype=3");
		
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function commentpublished(){
			
		global $CONFIG;
		$this->Request->setParamPost('publishedtype',1);	
		$this->Request->setParamPost('id',intval($this->_request('id')));		
		$data = $this->contentmodHelper->unCommentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/commentList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function publishitvenue(){
			
		global $CONFIG;
			$this->Request->setParamPost('publishedtype',1);
		$data = $this->contentmodHelper->unVenueReference();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/venueList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
}
?>