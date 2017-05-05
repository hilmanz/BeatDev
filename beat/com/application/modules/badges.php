<?php
class badges extends App{

	function beforeFilter(){
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		$this->uploadmodHelper = $this->useHelper('uploadmodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
		
	}
	
	function main(){
		
		$badgeList = $this->contentmodHelper->getBadgeList(null,12);
		// pr($badgeList);
		$this->assign('badges',$badgeList);
		$this->assign('total',$badgeList['total']);
		$this->log('surf','badges');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/badge-pages.html');
	}

	function create(){
		global $CONFIG;
		if(strip_tags($this->_p('badges'))=='1') {
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."badges/";
			$data = false;
			if (isset($_FILES['badge_active']) && $_FILES['badge_active']['name']!=NULL) {
				if (isset($_FILES['badge_active']) && $_FILES['badge_active']['size'] <= 20000000) {
					$data = $this->uploadmodHelper->uploadThisImage($_FILES['badge_active'],$path);
					
				}
			}

			//if($data){
				if (isset($_FILES['badge_non']) && $_FILES['badge_non']['name']!=NULL) {
					if (isset($_FILES['badge_non']) && $_FILES['badge_non']['size'] <= 20000000) {
						$temp = $this->uploadmodHelper->uploadThisImage($_FILES['badge_non'],$path);
						$data['arrImage2']['filename'] = $temp['arrImage']['filename'];
						
					}
				}
			//}


			$result = $this->contentmodHelper->addBadges($data);
			if($result) {
				$this->log('submitBadges',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."badges");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		$this->log('surf','create - badges');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/create-badge.html');
	}
	
	function sendTo(){
		global $CONFIG;
		$id = intval($this->_g('id'));
		if(strip_tags($this->_p('send_badges'))=='1'){
			$sendtouser = $_POST['sendBadge'];

			$this->log('surf','badges - send badge');
			$send = $this->contentmodHelper->sendBadge($sendtouser);

			if($send){
				sendRedirect($CONFIG['BASE_DOMAIN']."badges");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}elseif($id>0){
			$this->log('surf','badges - send form');
			$result = $this->contentmodHelper->detailBadges($id);
			$loadCity = $this->contentmodHelper->loadCity();
			$listBA = $this->contentmodHelper->listBA();
			//pr($result);exit;
			$this->assign('badge',$result);
			$this->assign('loadCity',$loadCity);
			$this->assign('listBA',$listBA);
		}else{
			sendRedirect($CONFIG['BASE_DOMAIN']."badges");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}

		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/send-badge.html');
	}
	
	function ajax_load_ba(){
		$listBA = $this->contentmodHelper->listBA();
		print json_encode($listBA);exit;
	}

	function edit(){
		global $CONFIG;
		$id = intval($this->_g('id'));
		$result = $this->contentmodHelper->detailBadges($id);
		$this->assign('detailBadge',$result);

		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/edit-badge.html');
	}

	function deleteBadge(){
		global $CONFIG;
		$deleteBadge = $this->contentmodHelper->deleteBadge();
		if($deleteBadge){
			sendRedirect($CONFIG['BASE_DOMAIN']."badges");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}


	}

	function ajax(){
		// pr('masukkkk');exit;
		$needs = $this->_request("needs");
		$start = intval($this->_request("start"));
		if($needs=="badges") $data =  $this->contentmodHelper->getBadgeList($start,12);
		print json_encode($data);exit;
	}
}
?>