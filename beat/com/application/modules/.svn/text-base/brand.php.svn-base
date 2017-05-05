<?php
class brand extends App{
	
	function beforeFilter(){	
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		$this->uploadmodHelper = $this->useHelper('uploadmodHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->usermodHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->usermodHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		$this->assign('user',$this->user);
		//pr($this->user);exit;
	}
	
	function main(){
		$data = $this->contentmodHelper->getArticleContent(null,10,0,array(0,1),"brand");
		//pr($data['result']);exit;

		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($data['total']));
		$this->assign('brand',$data['result']);
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
	
	function create(){
		global $CONFIG;
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		
		if(strip_tags($this->_p('upload'))=='simpan') {
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			$type = intval($this->_p('type'));
			if($type==4){
				$checkSticky = $this->_p('sticky');
				if($checkSticky==1){
					$dimension = getimagesize($_FILES['image']['tmp_name']);
					$width = intval($dimension[0]);
					$height = intval($dimension[1]);

					if($width>1614 || $height>352){
						sendRedirect($CONFIG['BASE_DOMAIN']."brand/create");
						$this->View->assign('msg',"The dimension should not exceed from 1614px x 352px.");
						return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
					}
				}
			}
			//var_dump(getimagesize($_FILES['image']['tmp_name']));exit;

			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					//var_dump("foo");exit;
					$data = $this->uploadmodHelper->uploadThisImage($_FILES['image'],$path);
					
				}
			}

			$result = $this->contentmodHelper->addUploadImage($data);
		 
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."brand");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		$this->View->assign('date',date('d/m/Y'));

		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-brand.html');
	}

	function edit(){
		global $CONFIG;
		$loadBrandDetail = $this->contentmodHelper->loadBrandDetail();
		
		$this->View->assign('brand', $loadBrandDetail);

		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/edit-brand.html');
	}
	
	function detail(){
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
	function editContent(){
		global $CONFIG,$LOCALE;
		if ($this->_p('authorid')==$this->user->id || $this->_p('authorid')==$this->user->pageid) {
			$data = $this->contentmodHelper->setEditContent();			
			if ($data) {
				$data;
			} else {
				$data= false;
			}
		} else {
			$data= false;
		}
		print json_encode($data);exit;
	}
	function unpublish(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('type',4);
		
		$data = $this->contentmodHelper->unContentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."brand");
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
		
		$data =  $this->contentmodHelper->getArticleContent($start,10,0,array(0,1),"brand");
		
		print json_encode($data);exit;
	}
	
	function article(){
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_list'));
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
	
	function highlight(){
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_list'));
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
	
	function cover(){
		$this->log('add cover brands',$this->user->id);
		global $CONFIG;
		$datas['status'] = false;
		$datas['message'] = "failed save cover";
		
		if(strip_tags($this->_p('action'))=='set') {
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."user/cover/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadmodHelper->uploadThisImage($_FILES['image'],$path);
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
			
			$brands = strip_tags($this->_p('brandsid'));
			if($brands=='marlboro') $userid = 5;
			if($brands=='amild') $userid = 4;
			
			$result = $this->usermodHelper->saveImageCover($data,$userid);
			
			if($result) {			
				$data = true;
			} else {
				$data = false;
			}
			 
			if($data) {
				$this->log('add cover',"brands");
				sendRedirect($CONFIG['BASE_DOMAIN']."brand/cover");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}else{
			$sql =" SELECT id,small_img,description FROM social_member WHERE id IN (4,5) ";
			$qData = $this->fetch($sql,1);
			$newdata = array();
			$newdata['amild']['small_img'] = 'default.jpg';
			$newdata['amild']['description'] = '';
			$newdata['marlboro']['small_img'] = 'default.jpg';
			$newdata['marlboro']['description'] = '';
			if($qData){
				
				foreach($qData as $val){
					if($val['id']==4){
						$newdata['amild']['small_img'] = $val['small_img'];
						$newdata['amild']['description'] = $val['description'];
					}
					if($val['id']==5){
						$newdata['marlboro']['small_img'] = $val['small_img'];
						$newdata['marlboro']['description'] = $val['description'];
					}
				
				}
			}
			$this->assign('cover',$newdata);
		}
	 
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-brand-cover.html');
	}
}
?>