<?php
class message extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		
	
		global $LOCALE,$CONFIG ;
		 
		 
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function lists(){
		$data = $this->messageHelper->getMessage();
		return $data;
	}
	
	function create(){
		GLOBAL $CONFIG;
		$res['result'] = false;
		$res['parentid'] = 0;
		$fid[] = $this->_p('recipientid');
		$ftype[] = $this->_p('ftype');
		 
		if($fid) $fid = implode(',',$fid);
		if($ftype) $ftype = implode(',',$ftype);
		
		$msg = strip_tags($this->_p('message'));
		$bugreport = intval($this->_p('bugreport'));
		
	
		
		if($fid){
			$attachment = false;
			if($bugreport==1){
				$path = $CONFIG['LOCAL_PUBLIC_ASSET']."reportbug/";
				
				if (isset($_FILES['attachment'])&&$_FILES['attachment']['name']!=NULL) {
					if (isset($_FILES['attachment'])&&$_FILES['attachment']['size'] <= 20000000) {
						$uploadata = $this->uploadHelper->uploadThisFile($_FILES['attachment'],$path);
							 // pr($uploadata);
						if ($uploadata['arrFile']!=NULL) {
							$attachment['filerealpath']=$path.$uploadata['arrFile']['filename'];
						}  
					} 
				} 
			}
			
			$arrfid = explode(',',$fid);
			$arrftype = explode(',',$ftype);
			$frienddata = false;
			if(is_array($arrfid)){
				foreach($arrfid as $key => $val){
					$frienddata[$key]['fid'] = $val;
					$frienddata[$key]['ftype'] = 1;
					
					
				}
				
				if($frienddata){
					$parentid = 0;
					$multimessages = false;
					foreach($frienddata as $val){
						
							$data = $this->messageHelper->createMessage($val['fid'],"{$msg}",$val['ftype'],$parentid,$multimessages,$attachment);
							if($data)  {
								$parentid  = intval($data);
								$multimessages  = true;
								$this->apns($val['fid'],$msg);							
							
							}
					}
				
				}
			}else{
				$ftype = intval($ftype);
				$fid = intval($fid);
				
					$data = $this->messageHelper->createMessage($fid,"{$msg}",$ftype,0,false,$attachment);
					if($data) $this->apns($val['fid'],$msg);						
			}
			 
		}
		
		if($data) {
			
			$this->log("message","create_{$fid}");
			
			$res['result'] = true;
			$res['parentid'] = $data;
		}else {
			$res['result'] = false;
			$res['parentid'] = 0;
		}
		return $res;
	}
	
	function readmessage(){
	
		$data = $this->messageHelper->readMessage();
		$cid = intval($this->_p('id'));
		if($data)$this->log("message","read_{$cid}");
		
		if(!$data)return array($data);
		else return $data;
		
	}
	
	function unlists(){
		$data = $this->messageHelper->uninboxmessage();
		$cid = intval($this->_p('cid'));
		if($data)$this->log("message","unlist_{$cid}");
		if(!$data)return array($data);
		else return $data;
	}
	
}
?>