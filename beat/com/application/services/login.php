<?php
class login extends ServiceAPI{
		
	function beforeFilter(){
		$this->loginHelper = $this->useHelper('loginHelper');
		
	}
	
	function account(){
	
		global $CONFIG,$logger;
	
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$this->assign('basedomain',$basedomain);
			$this->Request->setParamPost('username',strip_tags($this->_request('username')));
			$this->Request->setParamPost('password',strip_tags($this->_request('password')));
			
			if($this->_p('username')&&$this->_p('password')){
				$this->Request->setParamPost('login',1);
				$res = $this->loginHelper->loginSession();
				if($res['result']){
							return array(
							'status'=>true,
							'code'=>$res['code'],
							'crdmp'=>$res['crdmp'],
							'msg'=>'welcomes',
							'login_count'=>@$this->session->getSession($CONFIG['SESSION_NAME'],"WEB")->login_count,
							'login_mop'=>@$this->session->getSession($CONFIG['SESSION_NAME'],"WEB")->login_mop,
							'user_mail'=>@$this->session->getSession($CONFIG['SESSION_NAME'],"WEB")->email
							);
				}else{
						return array(
						'status'=>false,
						'code'=>$res['code'],
						'msg'=>$res['message']
						);
				}
			}
		
				print  $this->error_404();exit;
			
	
		
	}
	
	function mop(){
	
		global $CONFIG,$logger;
		
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$this->assign('basedomain',$basedomain);
			$this->Request->setParamPost('username',strip_tags($this->_request('username')));
			$this->Request->setParamPost('password',strip_tags($this->_request('password')));
			$logger->log(strip_tags($this->_request('username')));
			if($this->_p('username')&&$this->_p('password')){
				$this->Request->setParamPost('login',1);
				$res = $this->loginHelper->moploginsession();
				if($res){
							return array('status'=>true,'msg'=>'welcomes','data'=>@$this->session->getSession($CONFIG['SESSION_NAME'],"MOPADMIN"));
				}
			}
			
				print  $this->error_404();exit;
			
	
		
	}
	
	
	function realeaselock(){
		return $this->loginHelper->realeaselock();
		
	}
}
?>