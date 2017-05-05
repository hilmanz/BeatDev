<?php 

class brandQuestionerHelper {

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) {
				$uid = intval($this->apps->_request('uid'));
				if($uid==0) $this->uid = intval($this->apps->user->id);
				else $this->uid = $uid;
			}
			
			
		}
		
		$this->config = $CONFIG;
		$this->dbshema = "beat";	
	}
 
	function saveQuestionerBrand($img=false){
		$data['result'] = false;
		$data['message'] = "you cannot use this method";
		if($this->apps->user->leaderdetail->type!=1) return $data;
		
		$brandid = intval($this->apps->_request("brandid"));
		$brandsubid = intval($this->apps->_request("brandsubid"));
		$entourageid = intval($this->apps->_request("entourageid"));
		
		if($this->uid==0) {
			$data['message'] = " your ID not found ";
			return $data;
		}
		if($brandid==0) {
				$data['message'] = " your brand ID not found ";
			return $data;
		}
		if($brandsubid==0) {
				$data['message'] = " your brand sub ID not found ";
			return $data;
		}
		if($entourageid==0) {
				$data['message'] = " your entourage ID not found ";
			return $data;
		}
		
		
		$sql =" 
		INSERT INTO tbl_brand_questioner 
		( userid,brandid,brandsubid ,entourageid,datetimes) 
		VALUES 
		({$this->uid},{$brandid},{$brandsubid} ,{$entourageid},NOW())
		";
		$qData = $this->apps->query($sql);
		if($this->apps->getLastInsertId()) {
			$data['result'] = true;
			$data['message'] = "success save data questioner";
			
			$this->logger->log("update entourage : ");
			$sql = "SELECT * FROM my_entourage WHERE n_status = 1 AND  id={$entourageid} ORDER BY register_date DESC LIMIT 1 ";
			$val = $this->apps->fetch($sql);
			
			$this->logger->log($sql);
		
			
			if($val) {
				 
				
				// pr($val);exit;
				
					$this->apps->Request->setParamPost("name",$val['name']);
					$this->apps->Request->setParamPost("lastname",$val['last_name']);
					$this->apps->Request->setParamPost("nickname",$val['nickname']);
					$this->apps->Request->setParamPost("email",$val['email']);
					
					
					$sql = "SELECT * FROM beat_city_reference WHERE cityidmop='{$val['city']}' LIMIT 1";
					$city = $this->apps->fetch($sql);		
					
					$this->apps->Request->setParamPost("state",$city['provinceid']);
					$this->apps->Request->setParamPost("city",$city['cityidmop']);
					$this->apps->Request->setParamPost("giidnumber",$val['giidnumber']);
					$this->apps->Request->setParamPost("giidtype",$val['giidtype']);
					$this->apps->Request->setParamPost("sex",$val['sex']);
					$this->apps->Request->setParamPost("birthday",$val['birthday']);
					$this->apps->Request->setParamPost("phone_number",$val['phone_number']);
					$this->apps->Request->setParamPost("Brand1_ID",$brandid);
					$this->apps->Request->setParamPost("Brand1SUB_ID",$brandsubid);
					$this->apps->Request->setParamPost("referrerbybrand",$val['referrerbybrand']);
					$this->apps->Request->setParamPost("companymobile","ST1");
								
					
					$mop = $this->apps->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication",true);
				 
					$sql = "UPDATE my_entourage SET Brand1_ID='{$brandid}',Brand1U_ID='{$brandsubid}' WHERE id={$val['id']} LIMIT 1 ";
					$qData = $this->apps->query($sql);
					
					if($val['referrerbybrand']!=$this->uid){
						$sql = "
						INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
						VALUES ('{$val['id']}','{$this->uid}',0,0,NOW(),1)
						ON DUPLICATE KEY UPDATE n_status=1
						";
					
						$this->apps->query($sql);
					}
			}
			
			
			
		}
		return $data;
		
				
	}
	
	 
}

?>

