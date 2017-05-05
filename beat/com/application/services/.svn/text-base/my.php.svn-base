<?php
class my  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->wallpaperHelper = $this->useHelper('wallpaperHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->newsHelper = $this->useHelper('newsHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->merchandiseHelper = $this->useHelper('merchandiseHelper');
		$this->badgeHelper = $this->useHelper('badgeHelper');
		$this->synchHelper = $this->useHelper('synchHelper');
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	function profile(){
		$user = $this->userHelper->getUserProfile(); 
		$this->badgeHelper->generatebadge();
		$data['profile'] = $user;
		$data['role'] = $this->user->leaderdetail->pagetypename;
		
	 
		 
		return $data;		
		// return false;
		
	}
	
	function detailhirarki(){
	
		// $data['badetail'] = @$this->user->badetail;
		 $brands = @$this->user->branddetail;
		 $pl = @$this->user->pldetail;
		 $area = @$this->user->areadetail;
		
		if($brands){
			foreach($brands as $key=> $val){
					$brands[$key]->id=$val->ownerid;
			}
		}
		if($pl){
			foreach($pl as $key=> $val){
					$pl[$key]->id=$val->ownerid;
			}
		}
		if($area){
			foreach($area as $key=> $val){
					$area[$key]->id=$val->ownerid;
			}
		}
		
		$data['branddetail'] = $brands;
		$data['pldetail'] =  $pl;
		$data['areadetail'] =  $area;
		
		return $data;
	}
	
	
	function changepassword(){
		
		 
		$res = $this->userHelper->changepassword();
		if($res['result']){
  
			$this->log('surf',"change password");
		 
		}
		 
		return $res;
	}
	
	function inbox(){
		$data = $this->messageHelper->getMessage();
		if($data){
	  
				$this->log('surf',"lists of inbox");
			 
		}
		return $data;		
	}

	
	function friends(){
				
		$data = $this->userHelper->getFriends(false,10);
		if($data){
	  
				$this->log('surf',"lists of friends");
			 
		}
		return $data;
		
	}
	
	function friendstotags(){
				
 
		$data = $this->userHelper->getFriends(false,10,0, true, true);
 
		if($data){
	  
				$this->log('surf',"lists of friends tags");
			 
		}
		return $data;
		
	}
	
	function multiplefriends(){
				
 
		$data = $this->userHelper->getFriends(false,10,0, true, false,true);
 
		if($data){
	  
				$this->log('surf',"lists of friends message all tags");
			 
		}
		return $data;
		
	}
	
	
	function album(){
		global $CONFIG;
		$typegallery = strip_tags($this->_request('type'));
		
		$plan = $this->contentHelper->getGalleryTypeContent(null,10);
		
		if($plan['result']){
			foreach($plan['result'] as $key => $val){				 
				$plan['result'][$key]['commentlist'] = array();
				
				// foreach($plan['result'][$key]['gallery'] as $keys => $vale){
						// $plan['result'][$key]['gallery'][$keys]['friendtags'] = array();
						// if($plan['result'][$key]['friendtags']){
							
							// foreach($plan['result'][$key]['friendtags'] as $vals){
								// if($vale['id']==$vals['gid']) $plan['result'][$key]['gallery'][$keys]['friendtags'][] = $vals;
							// }
						// }
				// }
			}
		
		
			
		}
		$data['album']['total'] = $plan['total'];
		if($plan['result']) $data['album']['lists'] = $plan['result'];
		else $data['album']['lists'] = array();
		if(!$plan['timeline']['result']) $plan['timeline']['result'] = array();
		$data['album']['timeline'] = $plan['timeline'];
		$data['album']['pages'] =$plan['pages'];
		if($data){
	  
				$this->log('surf',"my Album");
			 
			}
		return $data;
	}
	
	function circle(){
		global $CONFIG;
		
		if(strip_tags($this->_g('do'))=='create') {
			$data = $this->userHelper->createCircleUser();
			$name = strip_tags($this->_request('name'));
			if($data) $this->log('create group',"{$name}");
			return $data;
		}
		if(strip_tags($this->_g('do'))=='loss') {
			$data = $this->userHelper->uncreateCircleUser();
			$name = strip_tags($this->_request('name'));
			if($data) $this->log('destroy group',"{$name}");
			return $data;
		}
		
		exit;			
	}
	
	function cover(){
		$this->log('add cover',$this->user->id);
		global $CONFIG;
		$datas['status'] = false;
		$datas['message'] = "failed save cover";
		
		if(strip_tags($this->_p('action'))=='set') {
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."user/cover/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					if ($data['arrImage']!=NULL) {
						
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;
			}
			
			$result = $this->userHelper->saveImageCover($data);
			if($result) {			
				$data = true;
			} else {
				$data = false;
			}
			if($data){
	  
				$this->log('add cover',"");
			 
			}
		}
		if($data){
			$datas['status'] = true;
			$datas['message'] = "success save cover";
		}
		return $datas;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));		
		$start = intval($this->_request("start"));
		$noteid = intval($this->_p("noteid"));

		if($needs=="contentgallery") $data =  $this->contentHelper->getMygallery($start,$limit=9);
		if($needs=="content") $data =  $this->contentHelper->getListSongs($start);
		if($needs=="comment") $data =  $this->contentHelper->getComment($contentid,false,$start);
		if($needs=="hapusmygallery") $data =  $this->contentHelper->hapusmygallery();
		if($needs=="inbox-trash") $data = $this->newsHelper->trashInbox($noteid);
		if($needs=="inbox-news-letter") $data = $this->newsHelper->saveinboxtime();	
		
		if($needs=="inbox-read") $data = $this->newsHelper->inboxread($noteid);	
		if($needs=="inbox-data-json") $data = $this->newsHelper->getInboxUser($start);	
		if($needs=="friends-list") 	$data = $this->userHelper->getFriends(true,16);	
		
		return $data;
	}
	
	
	function findmyfrienddefault(){
		$data = $this->userHelper->findmyfrienddefault();
	
		
		return true;
	}
	
	
	function findtoclassfrienddefault(){
		$data = $this->userHelper->findtoclassfrienddefault();
	
		
		return true;
	}
	
	function merchandise(){
		$data = $this->merchandiseHelper->getUserMerchandise();	
		return $data;
	}
	 
	function badetail(){
		 
		$data =  $this->userHelper->listBA();
		 
		return $data;
		
		
	}
	
	function branddetail(){
		$datas['result'] = false;
		$datas['data'] = array();
		
		$data = $this->userHelper->getBrand();
		if($data){
			$datas['result'] = true;
			$datas['data'] = $data;
		}
		return $datas;
	}
	
	function areadetail(){
		$datas['result'] = false;
		$datas['data'] = array();
		
		$data = $this->userHelper->getCity();
		if($data){
			$datas['result'] = true;
			$datas['data'] = $data;
		}
		return $datas;
	}
}
?>