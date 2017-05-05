<?php 

class passwordHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) {
				$uid = intval($this->apps->_request('uid'));
				if($uid==0) $this->uid = intval($this->apps->user->id);
				else $this->uid = $uid;
		}

		$this->dbshema = "beat";	
		$this->topclass = array(100,4,6);
	}

	function changepassword(){
		$oldpass = strip_tags($this->apps->_p('oldpass'));
		$newpass = strip_tags($this->apps->_p('newpass'));
		$confirmnewpass = strip_tags($this->apps->_p('confirmnewpass'));
		$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
		if($newpass!=$confirmnewpass) return false;
			$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
		// if(preg_match("/^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/",$newpass)) {
		if($newpass) {
			$sql = "SELECT * FROM social_member WHERE id={$this->uid} LIMIT 1";
			$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			
			$oldhashpass = sha1($oldpass.$rs['salt']);
			if($oldhashpass!=$rs['password']) return false;

			$hashpass = sha1($newpass.$rs['salt']);
			$sql ="UPDATE social_member SET password='{$hashpass}' WHERE id={$this->uid} LIMIT 1";
			$rs = $this->apps->query($sql);
		}
		$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass.'-'.'not have secury password');
		return false;
	}

	function checkuseremail($email=null){
		if($email){
			$sql = "SELECT * FROM social_member WHERE email='{$email}' AND n_status=1 LIMIT 1";
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			return $rs;
		}
		return false;
	}
	function getuseremail($id=null){
		if($id){
			$sql = "SELECT * FROM social_member WHERE id={$id} AND n_status=1 LIMIT 1";
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			return $rs;
		}
		return false;
	}
	function getuseremailonreset(){
		
			$sql = "SELECT * FROM social_member WHERE id={$this->uid} LIMIT 1";
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			return $rs;
		
	}

	function newpassword($id=null){
		if($id){
			$sql = "SELECT * FROM social_member WHERE id={$id} LIMIT 1";
			// pr($sql);exit;
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			
			//generate Password
			// $newpass = $this->incrementalHash(5);
			$newpass = $this->substringshash(sha1('email_token_validasi'.date('Y-m-d H:i:s').$rs['email']),5);

			$hashpass = sha1($newpass.$rs['salt']);
					
			$sql ="UPDATE social_member SET password='{$hashpass}', login_count=0 WHERE id={$id} LIMIT 1";
			$rs = $this->apps->query($sql);
			if($rs){
				return $newpass;
			}else{
				return false;
			}
		}
		return false;
	}
	function incrementalHash($len = 5){
		  $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		  $base = strlen($charset);
		  $result = '';

		  // $now = explode(' ', microtime())[1];
		  $now = explode(' ', microtime());
		  while ($now >= $base){
		    $i = $now % $base;
		    $result = $charset[$i] . $result;
		    $now = $base;
		  }
		  return substr($result, -5);
	}
	
	
	function substringshash($hasher=null,$limit=10){
			if($hasher==null) return false;
			$strings = substr($hasher,0,$limit);
			
			return $strings;
	}
	
}

?>

