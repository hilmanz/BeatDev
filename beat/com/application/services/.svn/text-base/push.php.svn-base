<?php
class push extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function main(){
		return false;
	}
	
	function comment(){
		/*
		if($cid==null) $cid = intval($this->_p('cid'));
		if($comment==null) $comment = $this->_p('comment');
		*/
		$cid = intval($this->_p('cid'));
		$data = $this->contentHelper->addComment();
		if($data) {
			$sentuserdata = $this->contentHelper->getallusercommentoncontent($cid);
			
			if($sentuserdata) {
				if(count($sentuserdata)==1){
					$this->notif(" {$this->user->name} {$this->user->last_name} comment on your post ",$cid,false,"comment");
				}else{
					foreach($sentuserdata as $val){
						$this->notif(" {$this->user->name} {$this->user->last_name} comment on post ",$cid,intval($val['userid']),"comment");
					}
				}
			}
			$this->log('add comments',"{$cid}");
		}
		return $data;
	}

		
	function emoticon(){
	/*
		$cid = intval($this->_p("cid"));
		$emoid = intval($this->_p("emoid"));
	*/
 
		GLOBAL $LOCALE;
		
		$arremoticon['1']=$LOCALE[1]['Frowned'];
		$arremoticon['2']=$LOCALE[1]['Liked'];
		$arremoticon['3']=$LOCALE[1]['Gasped'];
		$arremoticon['4']=$LOCALE[1]['Grinded'];
		$arremoticon['5']=$LOCALE[1]['Loved'];
	 
		$emoid = intval($this->_p("emoid"));
		if(!array_key_exists($emoid,$arremoticon)) return false;
		
		$data = $this->contentHelper->addFavorite();
		if($data) {			
			$cid = intval($this->_p('cid'));
			$this->notif(" {$this->user->name} {$this->user->last_name} {$arremoticon[$emoid]} at your post ",$cid,false,"emoticon");
			
			$this->log('add emoticon',"{$arremoticon[$emoid]}_{$cid}");
		}
		return $data;
	}
	
	function checkin(){
		/*
				$venueid = intval($this->_p('venueid'));
				$contentid = $this->_p('contentid');
				$venue = $this->_p('venue');
				$venuerefid = $this->_p('venuerefid');
				$coor = $this->_p('coor');
				$mypagestype = $this->_p('mypagestype');
				$friendtags = $this->_p('fid');
				$friendtypetags = $this->_p('ftype');
		*/
		$data = $this->checkinHelper->checkin();
		if($data){
			$coor = strip_tags($this->_p('coor'));
			$this->log('checkin',"{$coor}");
			return $data;
		}else return false;
	}
	
	function friendTags(){
		/*
			$cid = intval($this->_p("cid"));
			$fid = intval($this->_p("fid"));
			$ftype = intval($this->_p("ftype"));
		*/
		$cid = intval($this->_p("cid"));
		$fid = $this->_p('fid');
		$ftype = $this->_p('ftype');
		$arrfid = explode(',',$fid);
		$arrftype = explode(',',$ftype);
		$frienddata = false;
		$data = false;
		$datas = false;
		$datas['result'] = false;
		$datas['message'] = " failed to tag  ";
		if(is_array($arrfid)){
			foreach($arrfid as $key => $val){
				$frienddata[$key]['fid'] = $val;
				$frienddata[$key]['ftype'] = $arrftype[$key];				
			}
				if($frienddata){
			
					foreach($frienddata as $val){
						$data =	$this->contentHelper->addFriendTags($cid,$val['fid'],$val['ftype'],false);					
					}
				
				}
			}else{
				$ftype = intval($ftype);
				$data =$this->contentHelper->addFriendTags($cid,intval($fid),intval($ftype),false);						
				
			}
			$this->log('tags',"add_{$fid}");
			if($data){
			$datas['result'] = true;
			$datas['message'] = " success to tag  ";
			}
			return $datas;
	}
	
	function friendUnTags(){
	/*
		$cid = intval($this->_p("cid"));
		$fid = intval($this->_p("fid"));
		$ftype = intval($this->_p("ftype"));
	*/
		$cid = intval($this->_p("cid"));
		$fid = $this->_p('fid');
		$ftype = $this->_p('ftype');
		$arrfid = explode(',',$fid);
		$arrftype = explode(',',$ftype);
		$frienddata = false;
		$data = false;
		$datas = false;
		$datas['result'] = false;
		$datas['message'] = " failed to un tag  ";
		if(is_array($arrfid)){
		foreach($arrfid as $key => $val){
			$frienddata[$key]['fid'] = $val;
			$frienddata[$key]['ftype'] = $arrftype[$key];				
		}
			if($frienddata){
		
				foreach($frienddata as $val){
					$data =	$this->contentHelper->unFriendTags($cid,$val['fid'],$val['ftype'],false);					
				}
			
			}
		}else{
			$ftype = intval($ftype);
			$data =$this->contentHelper->unFriendTags($cid,intval($fid),intval($ftype),false);						
			
		}
		$this->log('tags',"un_{$fid}");
		if($data){
			$datas['result'] = true;
			$datas['message'] = " success to un tag  ";
		}
		
		return $datas;
	}
	
	
	function friendTagsSearch(){
	/*
		$keywords = intval($this->_p("keywords"));
	
	*/
		$data = $this->contentHelper->friendTagsSearch();
		return $data;
	}
	
	
	function gallery(){
		global $CONFIG,$logger;
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$logger->log(" gallery push :".json_encode($_FILES));
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					if ($data['arrImage']!=NULL) {
						$result = $this->contentHelper->addUploadImageGallery($data);
						if($result) {
						
							$data = true;
						} else {
							$data = false;
						}
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;
			}
			
		
		$url = "gallery";
		if($data)		$this->log('uploads',"gallery");
		return $data;
	}
		
	
}
?>