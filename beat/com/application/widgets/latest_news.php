<?php
class latest_news{
	
	function __construct($apps=null){		
			$this->apps = $apps;	
			global $LOCALE,$CONFIG;
			$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
			$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
			$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		$start = intval($this->apps->_p('start'));
		$data = $this->apps->contentHelper->getArticleContent($start,5);
		$this->apps->assign('article',$data['result']);
		$this->apps->assign('total',$data['total']);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/latest-news.html");
	
	}


}


?>