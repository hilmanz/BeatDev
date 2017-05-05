<?php
class post extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
		$this->dbshema = "athreesix";
	}
	
	function main(){
		return false;
	}
	
	function upload(){
		global $CONFIG,$LOCALE,$logger;
		$data['result'] = false;
		$data['message'] = "oops,  sorry you cannot post items  ";
		$data['code'] = 0;
				
		$username = ucwords($this->user->name);
		$type = intval($this->_p('type'));
		$logger->log(" type of post : ".$type);
		// $logger->log(" type of uploads : ".@$this->_p('upload'));
		// $logger->log(" files mime  : ".json_encode(@$_FILES['image']));
		
		if(strip_tags($this->_p('upload'))=='timeline') {
				$logger->log(" uses uploads : timeline");
				$res = $this->contentHelper->addUploadImage(false,$type);
						 					
		} else {
			$res = false;
		}
	
		
		
		if(strip_tags($this->_p('upload'))=='image') {
			$logger->log(" uses uploads : image");
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$uploaddata = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
						 
					if ($uploaddata['arrImage']!=NULL) {
						
					} else {
						$uploaddata = false;
					}
				} else {
					$uploaddata = false;
				}
			} else {
				$uploaddata = false;
			}
			
			$res = $this->contentHelper->addUploadImage($uploaddata,$type);
			 
			
		} 
		
		if(strip_tags($this->_p('upload'))=='video') {
			$logger->log(" uses uploads : video");
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/video/";
			if (isset($_FILES['video'])&&$_FILES['video']['name']!=NULL) {
				if (isset($_FILES['video'])&&$_FILES['video']['size'] <= 2000000) {
					$uploaddata = $this->uploadHelper->uploadThisVideo($_FILES['video'],$path);
					if ($uploaddata['arrVideo']!=NULL) {
						
					} else {
						$uploaddata = false;
					}
				} else {
					$uploaddata = false;
				}
			} else {
				$uploaddata = false;			
			}
						
			if ($type) {
		
				$res = $this->contentHelper->addUploadImage($uploaddata,$type);
				
							
			} else $res = false;			
		}
		if($res) {
			$typeupload = strip_tags($this->_p('upload'));
			$this->log('uploads',"{$type}_{$typeupload}");
		}
		
		if($res) {
			$data['result'] = true;
			$data['message'] = "your post saved";
			$data['code'] = 1;
		}else{
		
			if($type==5){
			
				$captimesplan = date('Y-m-d H:i:s');
				$posted_date = strip_tags($this->_p('posted_date'));
				$div = floor((strtotime($captimesplan)-strtotime($posted_date))/60);			
				 
				if($div>-5) {
					
					$data['result'] = false;
					$data['message'] = " your plan must planned gT 5 minutes ";
					$data['code'] = 0;
				}
			}
		
		}
		return $data;
	
	}

		
	function getJenis(){
		$valJenis = intval($this->_p('jenis_info'));
		print json_encode($valJenis);exit;
	}
	
	function ajaxpost($data){
		print json_encode($data);exit;
	}
	
	function plan(){
		global $CONFIG,$LOCALE;
		$edit = $this->_p('edit');
		$data['result'] = false;
		$data['message'] = "oops,  sorry you cannot update items  ";
		$data['code'] = 0;
		
		if($edit=='do'){
		 
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$res = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$res = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($res);
			if($result) {
				$this->log('uploads',5);
				$data['result'] = true;
				$data['message'] = "your plan updated saved";
				$data['code'] = 1;
				 
			}else{
				 
			
					$captimesplan = date('Y-m-d H:i:s');
					$posted_date = strip_tags($this->_p('posted_date'));
					$div = floor((strtotime($captimesplan)-strtotime($posted_date))/60);			
					 
					if($div>-5) {
						
						$data['result'] = false;
						$data['message'] = " your plan must planned gT 5 minutes ";
						$data['code'] = 0;
					}
				 	
				 
			}
		}
		return $data;
	}
	
}
?>