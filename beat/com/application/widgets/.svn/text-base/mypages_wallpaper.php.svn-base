<?php
class mypages_wallpaper{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){
		$data = $this->apps->wallpaperHelper->getPagesWallpaper();
		$pid = intval($this->apps->_request('pid'));
		$id_user = $this->apps->user->id;
		
		$this->apps->assign('pid',$pid);
		$this->apps->assign('id_user',$id_user);
		$this->apps->assign('mypages_wallpaper',$data);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/mypages-wallpaper.html");	
	}
}
?>