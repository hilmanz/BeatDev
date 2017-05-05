<?php
class challenge extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		$this->messagemodHelper  = $this->useHelper('messagemodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->usermodHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->usermodHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->usermodHelper->getrecepient());
		
	}
	
	function main(){
	 
		$articlelist = $this->contentmodHelper->getArticleContent(null,10,3);
		 
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($articlelist['total']));
		$this->assign('challenge',$articlelist['result']);
		$this->log('surf','challenge');
		// pr($articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/challenge-pages.html');
	}
	
	function create(){
		global $CONFIG;
		
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->usermodHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->usermodHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->usermodHelper->getrecepient());
		$this->assign('badge_list',$this->contentmodHelper->getBadgeList(0,1000));
		
		if (strip_tags($this->_p('upload'))=="simpan") {
			$data['arrImage']['filename']='';
			$result = $this->contentmodHelper->addUploadImage($data);
			if ($result) {
				sendRedirect($CONFIG['BASE_DOMAIN']."challenge");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}

		$loadCity = $this->contentmodHelper->loadCity();
		$this->assign('getCity',$loadCity);
	
		$social = $this->usermodHelper->getFriends(true,16);
		$this->assign('social',$social['data']);
		
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->usermodHelper->getrecepient());
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-challenge.html');
	}
	
	function detail(){
		$cidStr = intval($this->_request('id'));	
		$article = $this->contentmodHelper->getDetailArticle();
		
		$challengeHashtag = $this->contentmodHelper->getChallengeHashtag(0,10,$article['result'][0]['tags'],3);
		// pr($challengeHashtag);
		// exit;
		// pr($article);
		$this->assign('detail',$article['result']);
		$this->assign('challengeHashtag',$challengeHashtag['result']);
		$this->assign('cidStr',$cidStr);
		$this->View->assign('challenge_hashtag',$this->setWidgets('challenge_hashtag'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/challenge-detail.html');
	}
	
	function detailHashtag(){
		$cid = intval($this->_request('challenge'));
		$cid_user = intval($this->_request('id'));
		
		$article = $this->contentmodHelper->getDetailArticle();
		
		if (strip_tags($this->_request('tags'))) $tags = strip_tags($this->_request('tags'));
		else $tags = $article['result'][0]['un_tags'];
		
		$challengeHashtag = $this->contentmodHelper->getChallengeHashtag(0,10,$tags,3);
		$cekWinner = $this->contentmodHelper->cekChallengeWinner($article['result'][0]['authorid'],$cid_user);
		
		$this->assign('detailhashtag',$article['result']);
		$this->assign('cekwinner',$cekWinner);
		$this->assign('challengeHashtag',$challengeHashtag['result']);
		$this->assign('total_hashtag',$challengeHashtag['total']);
		$this->assign('cidStr',$cid);
		$this->assign('cid_user',$cid_user);
		$this->assign('tags',$tags);
		// pr($challengeHashtag);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/challengehashtag-detail.html');
	}
	
	function addComment(){
		global $CONFIG;
		
		$cid = intval($this->_p('cid'));
		$data = $this->contentmodHelper->addComment();
		sendRedirect($CONFIG['BASE_DOMAIN']."challenge/detail/{$cid}");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function setwinner(){
		global $CONFIG;
		if (strip_tags($this->_p('set'))=="winner") {
			$cid = intval($this->_p('cid'));
			$cid_user = intval($this->_p('cid_user'));			
			$tags = strip_tags($this->_p('tags'));
			
			$data = $this->contentmodHelper->setWinnerChallenge();
			if($data) print json_encode(array('result'=>true));
			else print json_encode(array('result'=>false));
			exit;
		}
	}
	
	function unpublish(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('type',6);
		
		$data = $this->contentmodHelper->unContentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."challenge");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}

	function setcomplete(){
		global $CONFIG;
		$cid = intval($this->_request('id'));
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('type',6);
		
		$data = $this->contentmodHelper->setComplete();
		sendRedirect($CONFIG['BASE_DOMAIN']."challenge/detail/{$cid}");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$tags = $this->_request("tags");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if ($this->_request("startdate") || $this->_request("enddate") || $this->_request("search")) {
			$this->Request->setParamPost('startdate',$this->_request('startdate'));
			$this->Request->setParamPost('enddate',$this->_request('enddate'));
			$this->Request->setParamPost('search',$this->_request('search'));
		}
		
		if($needs=="challenge") $data =  $this->contentmodHelper->getArticleContent($start,10,3);
		if($needs=="challenge_hashtag") $data =  $this->contentmodHelper->getChallengeHashtag(0,3,$tags,3);
		print json_encode($data);exit;
	}
	function ajax_load_ba(){
		$listBA = $this->contentmodHelper->listBA();
		print json_encode($listBA);exit;
	}
}
?>