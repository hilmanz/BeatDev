<?php
class gcm extends ServiceAPI{
		
	function beforeFilter(){
		$this->loginHelper = $this->useHelper('loginHelper');
		
	}
	
	function sends(){
	
			$url = "https://android.googleapis.com/gcm/send";
			$data['status'] = false;
			$data['message'] = " failed ";
			$data['data'] = array();
			
			$registatoin_ids = array($this->_p('registeredid'));
			$notification = "ola vin" ;
			$message = array("message"=>$notification);
			$fields = array(
				'registration_ids' => $registatoin_ids,
				'data' => $message,
			);
 
			$headers = array(
				'Authorization: key=AIzaSyD5Au75SgJN-_n5LJCUSw_afZWEvkHXAx0',
				'Content-Type: application/json'
			);
			// Open connection
			$ch = curl_init();
	 
			// Set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);	 
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 
			// Disabling SSL Certificate support temporarly
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);	 
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));	 
			// Execute post
			$result = curl_exec($ch);
			if ($result === FALSE) {
				$data['message'] = curl_error($ch); 
			}else{
				$info = curl_getinfo($ch);
				if($info['http_code']==200){
					$data['status'] = true;
					$data['message'] = " success send message to gcm ";
					$data['data'] = $info;
				}else{
					$data['data'] =  $info;
				}
			}
	 
			// Close connection
			curl_close($ch);
			return $data;
		
	}
	 
}
?>