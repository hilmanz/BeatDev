<?php
class inbox extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->messagemodHelper = $this->useHelper('messagemodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		// pr($this->usermodHelper->getrecepient());
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		$this->assign('recipient',$this->usermodHelper->getrecepient());
		$this->assign('users',$this->user);
		
	}
	
	function main(){
		$message = $this->messagemodHelper->getMessage(intval($this->_p('startdate')),10);
		// pr($this->messagemodHelper->getinboxcount());
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',$message['total']);
		$this->assign('message',$message['result']);
		$this->log('surf','inbox');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/inbox-pages.html');
	}
	
	function detail(){	
		$this->log('surf','detail inbox');
		$id = intval($this->_request('id'));
		$message =$this->messagemodHelper->readMessage();
		// pr($message);
		$this->assign('message',$message);
		$this->assign('parentid',$id);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/message-detail.html');
	}
	
	function create(){
		$loadCity = $this->contentmodHelper->loadCity();
		$this->assign('getCity',$loadCity);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-message.html');
	}
	
	function reply(){
		global $CONFIG;
		
		$fid[] = $this->_p('recipientid');
		$ftype[] = $this->_p('ftype');
		
		if($fid) $fid = implode(',',$fid);
		if($ftype) $ftype = implode(',',$ftype);
		$msg = strip_tags($this->_p('message'));
		$data = false;
		
		// pr($fid);exit;
		if($fid){	
				
			$arrfid = explode(',',$fid);
			$arrftype = explode(',',$ftype);
			$frienddata = false;
			if(is_array($arrfid)){
				foreach($arrfid as $key => $val){
					$frienddata[$key]['fid'] = $val;
					$frienddata[$key]['ftype'] = 1;
					
				}
				
				if($frienddata){
			
					foreach($frienddata as $val){
						
							$data = $this->messagemodHelper->createMessage($val['fid'],"{$msg}",$val['ftype']);	
					}
				
				}
			}else{
				$ftype = 1;
				$fid = intval($fid);
				
					$data = $this->messagemodHelper->createMessage($fid,"{$msg}",$ftype);	
			}
		
		}
		
	
		if($data) {
			$parentid = $data;
			// $url = "inbox/detail/".$parentid;
			 $url = "inbox";
		}else $url = "inbox";
		
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
		
	}
	
	function uninboxmessage(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		
		$data = $this->messagemodHelper->uninboxmessage();
		sendRedirect($CONFIG['BASE_DOMAIN']."inbox");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if($needs=="inbox") $data =  $this->messagemodHelper->getMessage($start,10);
		if($needs=="counter") $data =  $this->messagemodHelper->getinboxcount();
		print json_encode($data);exit;
	}
	function ajax_load_ba(){
		$listBA = $this->contentmodHelper->listBA();
		print json_encode($listBA);exit;
	}
}
?>