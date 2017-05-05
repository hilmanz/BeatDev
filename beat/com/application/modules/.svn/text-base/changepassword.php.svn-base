<?php
class changepassword extends App{	
	
	function beforeFilter(){
		global $CONFIG;
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->passwordHelper = $this->useHelper('passwordHelper');
	
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
	}
	
	function main(){

		/*$getuseremailonreset = $this->passwordHelper->getuseremailonreset();

		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);*/
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/changepassword.html');		
	}
	
	function changeit(){
		global $CONFIG;
		
		$data = $this->userHelper->changepassword();
		if($data['code']==1){
			$this->log('login','welcome');
			$this->assign("msg","login-in.. please wait");
			$this->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
			sendRedirect("{$CONFIG['BASE_DOMAIN']}home");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}else{
			// $this->assign("msg","please assign correct credential password");
			$this->assign("msg","The password must be at least eight characters long, contains at least one uppercase letter, one lowercase letter, and one number");
			sendRedirect("{$CONFIG['BASE_DOMAIN']}logout.php");
			// pr('masuk');
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}
	}
	
}
?>