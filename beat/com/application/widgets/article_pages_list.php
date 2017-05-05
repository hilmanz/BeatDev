<?php
class article_pages_list{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
	}

	function main(){
		$data_article = $this->apps->contentHelper->getArticleContent();
		// pr($data_article);
		$this->apps->assign('article',$data_article['result']);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/article-pages-list.html");
	}
	
}
?>