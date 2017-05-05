<?php
class home extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->passwordHelper  = $this->useHelper('passwordHelper');
		$this->entouragemodHelper = $this->useHelper('entouragemodHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->activitymodHelper = $this->useHelper('activitymodHelper');
		$this->searchmodHelper  = $this->useHelper('searchmodHelper');
		$this->messagemodHelper = $this->useHelper('messagemodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		$this->uploadmodHelper = $this->useHelper('uploadmodHelper');
			
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->registerHelper = $this->useHelper('registerHelper');
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);
		$this->assign('user', $this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		
		$data = $this->usermodHelper->getUserProfile(); 	
		
		$this->View->assign('userprofile',$data);
	
		
		$branduser = false;
		$areauser = false;
		$pluser = false;
		$bauser = false;
		$goduser = false;
		
		
		
		if(in_array($this->user->leaderdetail->type,array(2,4))) $branduser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4))) $areauser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4,5))) $pluser = true;
		if(in_array($this->user->leaderdetail->type,array(2))) $bauser = true;
		if(in_array($this->user->leaderdetail->type,array(666))) $goduser = true;
		
		$this->assign('branduser',$branduser);
		$this->assign('areauser',$areauser);
		$this->assign('pluser',$pluser);
		$this->assign('bauser',$bauser);
		$this->assign('goduser',$goduser);
	}
	
	function main(){
		 // pr($this->user);
		
		$this->log('aaaa');
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->View->assign('my_profile_box',$this->setWidgets('my_profile_box'));
		
		$this->View->assign('lates_engagement_box',$this->setWidgets('lates_engagement_box'));
		$this->View->assign('inbox_box',$this->setWidgets('inbox_box'));
	
		 
		
		$this->View->assign('medium_banner',$this->setWidgets('medium_banner'));

		
		
			$this->View->assign('entourage',json_encode($this->getchartarchivement()));
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/home.html');		
	}
	
	function getchartarchivement(){
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
							$nextpage=intval($mydata['pages']['nextpage']);
					}else $nextpage = 0;
			
			
			}else $nextpage = 0;
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
			 $dataentourage['total_per_status'][1] = 0;
			 $dataentourage['total_per_status'][2] = 0;
			 // pr($entourage);
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
					
					if( $val['n_status']==1 ) $dataentourage['total_per_status'][1]++;	
					if( $val['n_status']==2 ) $dataentourage['total_per_status'][2]++;		
				 						
				}
				// pr($dataentourage);
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
		// pr($dataentourage);
		return $dataentourage;
	}
	
	// function savePassword() {
		// global $CONFIG;
		// $id = $this->_p('id');
		// $authorid = intval($this->_p("authorid"));
		
		// $password = trim($this->_p('password'));
		// $hashPass = sha1($password.$CONFIG['salt']);
		
		// $sql = "
				// UPDATE social_member SET password='{$hashPass}' WHERE id = {$id} LIMIT 1				
			// ";
			// pr($sql);
		// $qData = $this->query($sql);
		// sendRedirect ("{$CONFIG['BASE_DOMAIN']}home/profileDetail");
		
	// }
	
	
	function changeit() {
	global $CONFIG;
		$data = $this->passwordHelper->changepassword();
		if($data){
				sendRedirect("{$CONFIG['BASE_DOMAIN']}home/profileEdit");
				return $this->out(TEMPLATE_DOMAIN_WEB . 'widgets/change-password.html');
				exit;
		}else{
		("{$CONFIG['BASE_DOMAIN']}home/profileDetail");
		return $this->out(TEMPLATE_DOMAIN_WEB .'widgets/profile-detail.html');
		}
	}
	
	function saveUser() {
		global $CONFIG;
		 
	 
		/*$name = strip_tags($this->_p('name'));
		$last_name = strip_tags($this->_p('last_name'));
		$nickname = strip_tags($this->_p('nickname'));
		$sex = strip_tags($this->_p('sex'));
		$phone_number = strip_tags($this->_p('phone_number'));
				
		$sql = "
				UPDATE social_member SET name='{$name}', last_name='{$last_name}', nickname='{$nickname}',sex='{$sex}' ,phone_number='{$phone_number}' WHERE id = '{$this->user->id}' LIMIT 1				
			";
			// pr($sql);
		$qData = $this->query($sql);*/
		$data = $this->usermodHelper->updateUserProfile();
		if(!$data) return false;
		sendRedirect ("{$CONFIG['BASE_DOMAIN']}home/profileDetail");
		
	}
	
	function profileDetail(){
	$this->View->assign('profile_detail',$this->setWidgets('profile_detail'));
	return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/profile-detail.html');
	}
	
	function profileDetailEdit(){
		global $CONFIG;
		if($this->_p('token')){
			$data = $this->uploadmodHelper->uploadThisImage($files=$_FILES['img'],$path=$CONFIG['LOCAL_PUBLIC_ASSET'].'user/photo/');
			if ($data){
				$saved = $this->usermodHelper->updateUserImageProfile($data['arrImage']['filename']);
				if ($saved)$this->View->assign('status', 1);
			}
			
			// return false;
		}
		
		
		$this->View->assign('profile_edit',$this->setWidgets('profile_edit'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/profile-edit.html");
	}

	function changePassword(){
	$this->View->assign('change_password',$this->setWidgets('change_password'));
	return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/change-password.html');
	}

	function editProfile(){
	$this->View->assign('edit_profile',$this->setWidgets('edit_profile'));
	return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/edit-profile.html');
	}
	
	function entourageList(){
		$this->View->assign('entourage_list',$this->setWidgets('entourage_list'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-list.html');
	}
	
	function entourageDetail(){
		$this->View->assign('entourage_detail',$this->setWidgets('entourage_detail'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-detail.html');
	}
	
	function ajax(){
		if($this->_p('action')=="a360activity") {
			$maxrecord = 2;
			$start = intval($this->_p('start'));
			$data = $this->activityHelper->getA360activity($start,$maxrecord);
			print json_encode($data['content']); exit;
		}
	}	
}
?>