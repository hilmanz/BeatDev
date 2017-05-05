<?php
class baperformance extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->entouragemodHelper = $this->useHelper('entouragemodHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		//$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->activitymodHelper = $this->useHelper('activitymodHelper');
		$this->searchmodHelper  = $this->useHelper('searchmodHelper');
		$this->messagemodHelper = $this->useHelper('messagemodHelper');
		
		$this->synchmodHelper = $this->useHelper('synchmodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		
		$this->assign('social',$this->usermodHelper->getrecepient());
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		
		$branduser = false;
		$areauser = false;
		$pluser = false;
		$bauser = false;
		
		$this->assign('social',$this->usermodHelper->getrecepient());
		$this->assign('branddetail',$this->usermodHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->usermodHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->usermodHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->usermodHelper->getrecepient('badetail'));
		// pr($this->usermodHelper->getrecepient('pldetail'));
	
		if(in_array($this->user->leaderdetail->type,array(2,4))) $branduser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4))) $areauser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4,5))) $pluser = true;
		if(in_array($this->user->leaderdetail->type,array(2))) $bauser = true;
		
		$this->assign('branduser',$branduser);
		$this->assign('areauser',$areauser);
		$this->assign('pluser',$pluser);
		$this->assign('bauser',$bauser);	
	}
	
	function main(){
		$this->log('surf','baperformance');
		
		$startdate 	=strip_tags($this->_p('startdate'));
		$enddate 	=strip_tags($this->_p('enddate'));
		if($startdate) $startdate =date("d-m-Y",strtotime($startdate));
		if($enddate) $enddate =date("d-m-Y",strtotime($enddate));
		
		$this->assign('startdate',$startdate);
		$this->assign('enddate',$enddate);
		
		$this->assign('search',$this->_p('search'));
		$this->assign('uid',$this->_p('uid'));
		$this->assign('brandid',$this->_p('brandid'));
		$this->assign('plpicker',$this->_p('plpicker'));
		$this->assign('cityid',$this->_p('cityid'));
		$data = $this->usermodHelper->getUserProfile(); 
		
		$this->View->assign('userprofile',$data);
		$this->View->assign('roletype',$this->user->leaderdetail->type);
		
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
			$dataentourage['total_per_status'][1]=0;
			$dataentourage['total_per_status'][2]=0;	
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

		$loadCity = $this->contentmodHelper->loadCity();
		// pr($this->usermodHelper->getrecepient('areadetail'));
		$this->assign('getCity',$loadCity);
		$this->assign('entourage',json_encode($dataentourage));
		 
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/baperformance-pages.html');
	}

	function ajax_load_ba(){
		$listBA = $this->contentmodHelper->listBA2();
		print json_encode($listBA);exit;
	}
}
?>