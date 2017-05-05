<?php
class entourage extends ServiceAPI{

	
	function beforeFilter(){
	
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->deviceMopHelper = $this->useHelper('deviceMopHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function lists(){
		
		$data =  $this->entourageHelper->getEntourage(null,0,10,false);
		if($data){
			foreach($data['result'] as $entkey => $entdata){
					$data['result'][$entkey]['latestengagament']['date'] = date("H:m d/m/Y", strtotime($entdata['latestengagament']['date']));
					$data['result'][$entkey]['latestengagament']['contentdetail']['posted_date'] = date("H:m d/m/Y", strtotime($entdata['latestengagament']['contentdetail']['posted_date']));
			}
			$this->log('surf',"List Of Entourage");
		}
		if(!$data) return array();
		else return $data;

	}
	
	function profile(){		
			$userprofile =  $this->entourageHelper->entourageProfile();	
			if($userprofile){
				 foreach($userprofile['latestengagament'] as $key => $val){
					$userprofile['latestengagament'][$key]['date'] = date("d/m/Y H:m", strtotime($val['date']));
					$userprofile['latestengagament'][$key]['contentdetail']['posted_date'] = date("d/m/Y H:m", strtotime($val['contentdetail']['posted_date']));
				 }
				 foreach($userprofile['entouragedata'] as $key => $friendidsvals){
					foreach($friendidsvals as $keys => $vals){
						$userprofile['entouragedata'][$key][$keys]['date'] = date("d/m/Y H:m", strtotime($vals['date']));
						$userprofile['entouragedata'][$key][$keys]['contentdetail']['posted_date'] = date("d/m/Y H:m", strtotime($vals['contentdetail']['posted_date']));
					}
				 }
				$this->log('surf',"Entourage Profile");
			}
			return $userprofile;

	}
	
		
	function register(){
		GLOBAL $CONFIG;
		$success = false;
		
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/";
			$img = false;
			if (isset($_FILES['img'])&&$_FILES['img']['name']!=NULL) {
				if (isset($_FILES['img'])&&$_FILES['img']['size'] <= 20000000) {
					$img = $this->uploadHelper->uploadThisImage($_FILES['img'],$path);
						
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
		
		if($img)$filename = $img['arrImage']['filename'];
		else $filename = false;
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/signature/";
			$signature = false;
			if (isset($_FILES['signature'])&&$_FILES['signature']['name']!=NULL) {
				if (isset($_FILES['signature'])&&$_FILES['signature']['size'] <= 20000000) {
					$signature = $this->uploadHelper->uploadThisImage($_FILES['signature'],$path);						
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
	
	
		if($signature)$signature = $signature['arrImage']['filename'];
		else $signature = false;
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/signatureba/";
			$signatureba = false;
			if (isset($_FILES['signatureba'])&&$_FILES['signatureba']['name']!=NULL) {
				if (isset($_FILES['signatureba'])&&$_FILES['signatureba']['size'] <= 20000000) {
					$signatureba = $this->uploadHelper->uploadThisImage($_FILES['signatureba'],$path);						
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
	
		if($signatureba)$signatureba = $signatureba['arrImage']['filename'];
		else $signatureba = false;
		
		
		$data = $this->entourageHelper->addEntourage($filename,$signature,$signatureba);
		if($data) $success = true;		
		else $success = false;
		
		return array("result"=>$success);
	}
	
	function search(){
		$data = $this->entourageHelper->getSearchEntourage();
			if($data){
				$keywords = strip_tags($this->_request('keywords'));	
				$this->log('search entourage',"{$keywords}");
			}
		return $data;
	}
	
	function checkemail(){
		
		// return false;
		
		
			$result = false;
			$data = $this->deviceMopHelper->searchProfileUser();
			if($data) {
				if($data['result']) $result = $data;
				else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
					
				}
			}else {
				// $data = $this->entourageHelper->checkentourage();	
				// if($data['result']) $result = $data;
				
			}
			return $result;
	}
	
	function checkgiid(){
			// return false;
			$result = false;
			$data = $this->deviceMopHelper->AdminGetProfileonGiid();
			if($data) {
				if($data['result']) $result = $data;
				else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
				}
			}else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
			}
			return $result;
	}
	
	function chart(){
		$entourage['result'] = array();
		$nextpage = 1;
		while($nextpage>0){
		 
			if($nextpage==1)$nextpage = 0;
			$mydata = $this->entourageHelper->entouragechart(null,$nextpage,30);
			if($mydata){
					if($mydata['result']){
						foreach($mydata['result'] as $val){
								$entourage['result'][] = $val;								
						}
					}
			}
			$nextpage=intval($mydata['pages']['nextpage']);
			usleep(500);
		}
		// $entourage = $this->entourageHelper->getEntourage(null,0,10,true);
		$dataentourage['data'] =  array();
		$dataentourage['gender'] =  array();
		$dataentourage['brand'] =  array();
		$dataentourage['age'] =  array();
		
		$rangedate = false;
			 // pr($entourage);
		// exit;
		if($entourage){
			$dataentourage['data'][1] = array();
			$dataentourage['data'][2] = array();
			$dataentourage['brand']['Our'] = 0;
			$dataentourage['brand']['PMI'] = 0;
			$dataentourage['brand']['Competitor'] = 0;
			$dataentourage['gender']["M"] = 0;
			$dataentourage['gender']["F"]= 0;
			$dataentourage['age'][18]= 0;
			$dataentourage['age'][24]= 0;
			$dataentourage['age'][40]= 0;
			
			if($entourage['result']){
		
				
				foreach($entourage['result'] as $val){			
					$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
					$rangedate[$datetimes] = (string)date("Y-m-d",strtotime($val['register_date']));
					$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage['data'][$val['n_status']][$datetimes]+1;					
					$dataentourage['gender'][strtoupper(substr($val['sex'],0,1))]=@$dataentourage['gender'][substr($val['sex'],0,1)]+1;					
					if(array_key_exists('brandrelevancyour',$val))@$dataentourage['brand']['Our']+=$val['brandrelevancyour'];
					if(array_key_exists('brandrelevancypmi',$val))@$dataentourage['brand']['PMI']+=$val['brandrelevancypmi'];
					if(array_key_exists('brandrelevancycompetitor',$val))@$dataentourage['brand']['Competitor']+=$val['brandrelevancycompetitor'];
					$birthday = $this->entourageHelper->getAge($val['birthday']);
					if(($birthday>=18) && ($birthday <=24) ) $dataentourage['age'][18]=@$dataentourage['age'][18]+1;					
					if(($birthday>=25) && ($birthday <=29) ) $dataentourage['age'][24]=@$dataentourage['age'][24]+1;					
					if( $birthday>=30  ) $dataentourage['age'][40]=@$dataentourage['age'][40]+1;	
					// $dataentourage['age'][$birthday]=@$dataentourage['age'][$birthday]+1;							
				}
				$newdata = false;
				sort($rangedate);
				$mindate = strtotime(min($rangedate));
				$maxdate = strtotime(max($rangedate));
				
				$startdate = strip_tags($this->_p('startdate'));
				$enddate = strip_tags($this->_p('enddate'));
				if($startdate){
					if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
					$mindate = strtotime($startdate);
					$maxdate = strtotime($enddate);
				}
				$totaldate = ($maxdate - $mindate) / (60*60*24);
				
				
				for($i=0;$i<=$totaldate;$i++){
				// pr($totaldate);
					$dates = date("Y-m-d",$mindate);
					$val = date("Y-m-d" , strtotime("{$dates} +{$i} day"));
					// pr($val);
					foreach($dataentourage['data'] as $key => $valve) {					
						if(array_key_exists($val,$dataentourage['data'][$key])) $newdata[$key][$val] = $dataentourage['data'][$key][$val];
						else $newdata[$key][$val] = 0;
					}
					
				}
				if($dataentourage['brand']){
					 
					$compet = intval(@$dataentourage['brand']['Competitor']);
					$our = intval(@$dataentourage['brand']['Our']);
					
					$dataentourage['brand']['Competitor'] =$our ;
					$dataentourage['brand']['Our'] =$compet ;
				}
				$dataentourage['data'] = $newdata;
				
			}
		}
		 
				 
				$this->log('surf',"entourage chart");
		 
		return $dataentourage;
	 
	}
	
	function changephoto(){
	
		global $CONFIG;
		$data = false;
		$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/";
		$img = false;
			if (isset($_FILES['img'])&&$_FILES['img']['name']!=NULL) {
				if (isset($_FILES['img'])&&$_FILES['img']['size'] <= 20000000) {
					$img = $this->uploadHelper->uploadThisImage($_FILES['img'],$path);
						
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
	
		if($img)$filename = $img['arrImage']['filename'];
		else $filename = false;
		
		if($filename) $data = $this->entourageHelper->changephoto($filename);
		
		if($data){
				 
				$this->log('surf',"change entourage photo");
		}
		return $data;
		
	}
	
	function synchenturage(){
		
		$data = $this->entourageHelper->synchenturage_batch();
	
		
		return true;
	}
	
	function synchenturage_batch(){
		
		$data = $this->entourageHelper->synchenturage_batch();
	
		
		return true;
	}
	
	function pmientourage(){
		$data = $this->entourageHelper->getallpmientourage();
		return $data;
	}
}
?>
