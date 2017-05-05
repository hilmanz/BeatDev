<?php
class acquisition{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){
		$start = intval($this->apps->_p('start'));
		
		$startdate = strip_tags($this->apps->_p('startdate'));
		$enddate = strip_tags($this->apps->_p('enddate'));
		if ($startdate == "" && $enddate==""){
			$this->apps->Request->setParamPost('startdate',date("Y-m-d",strtotime(date('Y-m-d'). "-7 day")));
			$this->apps->Request->setParamPost('enddate',date('Y-m-d'));
			$entourage = $this->apps->entourageHelper->getEntourage(null,0,1000,true,false,true);
		}else{
			$entourage = $this->apps->entourageHelper->getEntourage(null,0,1000,true,false,false);
		}
		
		// pr(strtotime(date('Y-m-d')));
		
		$dataentourage = false;
		$rangedate = false;
		//pr($entourage);exit;

		if($entourage){
			$dataentourage['total_per_status'] = $entourage['total_per_status'];
			if($entourage['result']){
				foreach($entourage['result'] as $val){			
					$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
					$rangedate[$datetimes] = (string)date("Y-m-d",strtotime($val['register_date']));
					$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage['data'][$val['n_status']][$datetimes]+1;					
					$dataentourage['gender'][$val['sex']]=@$dataentourage['gender'][$val['sex']]+1;					
					$dataentourage['brand'][$val['Brand1_ID']]=@$dataentourage['brand'][$val['Brand1_ID']]+1;
					$birthday = $this->apps->entourageHelper->getAge($val['birthday']);
					$dataentourage['age'][$birthday]=@$dataentourage['age'][$birthday]+1;					
				}
				$newdata = false;
				sort($rangedate);
				$mindate = strtotime(min($rangedate));
				$maxdate = strtotime(max($rangedate));
				
				$startdate = strip_tags($this->apps->_p('startdate'));
				$enddate = strip_tags($this->apps->_p('enddate'));
				if($startdate){
					if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
					$mindate = strtotime($startdate);
				$maxdate = strtotime($enddate);
				$totaldate = ($maxdate - $mindate) / (60*60*24);
				$arrdata = false;
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
				// pr($entourage['result']);
				// pr($newdata);
				$dataentourage['data'] = $newdata;
			}else{
				$startdate = strip_tags($this->apps->_p('startdate'));
				$enddate = strip_tags($this->apps->_p('enddate'));
				if(!$enddate) $enddate = date("Y-m-d");
				if(!$startdate) if($enddate)  $startdate = date("Y-m-d",strtotime($enddate. "-7 day"));
				$mindate = strtotime($startdate);
				$maxdate = strtotime($enddate);
				$totaldate = ($maxdate - $mindate) / (60*60*24);
			
				for($i=0;$i<=$totaldate;$i++){
			
					$dates = date("Y-m-d",$mindate);
					$val = date("Y-m-d" , strtotime("{$dates} +{$i} day"));
					// pr($val);
				 	$newdata[1][$val] = 0;
					 
				}
				$dataentourage['data'] = $newdata;
							
			}
			
				// pr($startdate);
			
		}
		//pr($dataentourage);
				
	
		$this->apps->View->assign('entourage', json_encode($dataentourage));
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/acquisition-chart.html");
	}
	
}
?>