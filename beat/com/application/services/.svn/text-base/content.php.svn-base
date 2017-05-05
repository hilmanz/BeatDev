<?php
class content extends ServiceAPI{

	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->contentHelper = $this->useHelper('contentHelper');

		$this->searchHelper = $this->useHelper('searchHelper');
		
	}
	
	function detail(){		
		$article = $this->contentHelper->getDetailArticle();		
		
		// pr($article);exit;	
		$this->log('read article',intval($this->_request('id')));
		$datacomment = false;
		if($article) 
		{
			$comment  = $this->contentHelper->getComment();
			if($comment){
				
				foreach($comment as $val){
					$datacomment = $val;
				}
			}
			$data['result'] =  true;
		
			
			
			
				$data['article'] = $article['result'][0];
				if($datacomment) $data['comment'] = $datacomment;
			else $data['comment'] = false;
			
		}else {
			$data['result'] = false;
		}		
		return $data;
		
		 
	}
	
	function comment(){
		$data = $this->contentHelper->addComment();
		if($data)$this->log("add comments", intval($this->_p('cid')));
		return $data;
	}
	
	function favorite(){
		$data = $this->contentHelper->addFavorite();
		if($data)$this->log("add favorite", intval($this->_p('cid')));
		return $data;
	}

	function unpost(){
		return true;
	
	}
}
?>