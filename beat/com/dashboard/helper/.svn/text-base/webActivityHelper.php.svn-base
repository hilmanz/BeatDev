<?php

class webActivityHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
		
		$this->startdate = $this->apps->_g('startdate');
		$this->enddate = $this->apps->_g('enddate');	
		if($this->enddate=='') $this->enddate = date('Y-m-d');		
		if($this->startdate=='') $this->startdate = date('Y-m-d' ,  strtotime( '-7 day' ,strtotime($this->enddate)) );
		
	}
	
	function topVisitedPage(){
	
		$sql = "SELECT COUNT( * ) num, action_value, DATE( date_time ) dd
				FROM tbl_activity_log
				WHERE action_id =6 AND DATE( date_time ) >= '{$this->startdate}'
				AND DATE( date_time ) <= '{$this->enddate}'
				GROUP BY action_value ORDER BY num DESC LIMIT 5";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
	
	}
	 
	
	function contentComment($type='content',$cid=false){
	GLOBAL $CONFIG;
	$qContentid = "";
	if($cid!=false) $qContentid = " AND id IN ({$cid}) ";
	
	if($type=='content')$sql = " SELECT title FROM beat_news_content WHERE title IS NOT NULL {$qContentid} ";
	if($type=='comment')$sql = " SELECT comment FROM beat_news_content_comment WHERE comment IS NOT NULL ";
	$qData = $this->apps->fetch($sql,1);
	if(!$qData) return false;
	$str = false;
		foreach($qData as $val){
			 if($type=='content')$str[]=$val['title'];
			 if($type=='comment')$str[]=$val['comment'];
		}
		if($str) $str = implode(' ',$str);
		if($str){
	
			$arrStr = false;
			$dataarr = false;
			$datastr = json_decode(wordsum($str,"#"));
			
			if($datastr){
				foreach($datastr  as $key =>$val){
					$thedata[]= "\"".strtolower($val->text)."\"";
				}
				if($thedata){
				
					$regex = implode(',',$thedata);
					$sql =" SELECT keyword FROM stopword_id  WHERE keyword IN ({$regex}) ";
					// pr($sql);exit;
					$qData = $this->apps->fetch($sql,1);
					
					if($qData){
						foreach($qData as $val){
							$dataarr[$val['keyword']]=1;
						}
					}
					
					// if($type=='comment') pr($dataarr);
					if($dataarr){
						arsort($dataarr);
						$n=0;
						$totalwordshown = 50;
							
						foreach($datastr as $key =>$val){
							if(!array_key_exists(strtolower($val->text),$dataarr)) {
								if($n<=$totalwordshown) $arrStr[]= array("text"=>$val->text,"weight"=>$val->weight,"html"=>array("class"=>"vertical showpopupcontent"),"link"=>array("href"=>strtolower($val->link->href)));
								else continue;
								// pr($val->text);
								$n++;

							}
							
						}
						// exit;
					}else{
						$arrStr = $datastr;
					}
				}
			}
			if($arrStr) return json_encode($arrStr);
			else return false;
		}
		return false;
	
	}
	
	function cityRef(){
		$cityname = strip_tags($this->apps->_p('cityname'));
		$sql ="SELECT city label  FROM  beat_city_reference WHERE city like '%{$cityname}%' AND city <> '(not specified)' ";
		$qData = $this->apps->fetch($sql,1);	
		if($qData) return $qData;
		return false;
	}
	
	
	function getContent($start=0,$limit=10){
		$word = str_replace("#","",strip_tags($this->apps->_p("words")));
		
		 $sql = " 
		 SELECT nc.id,nc.title,nc.brief,nc.content ,cmt.comment
		 FROM beat_news_content_comment cmt
		 LEFT JOIN beat_news_content nc  ON nc.id = cmt.contentid
		 WHERE  
		 nc.title like '%{$word}%' 
		 OR nc.brief like '%{$word}%' 
		 OR nc.content like '%{$word}%'
		 OR cmt.comment like '%{$word}%'
		 GROUP BY nc.id
		 LIMIT {$start},{$limit}
		 ";
		 // pr($sql);
		 $qData = $this->apps->fetch($sql,1);	
		if($qData) return $qData;
		return false;
	}
	
	function topRedeemMerch()
	{
		$sql = "SELECT COUNT( * ) num, detail.name
				FROM my_merchandise mm
				LEFT JOIN tbl_detail_merchandise detail ON mm.merchandiseid = detail.id
				WHERE mm.n_status =1
				GROUP BY mm.merchandiseid";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
		
	}
	
	function topVisitedPageReport($all=false){
		$qLimit  = " LIMIT 5 ";
		if($all==true) $qLimit  = "  ";
		$sql = "SELECT COUNT( * ) num, action_value, DATE( date_time ) dd
				FROM tbl_activity_log
				WHERE action_id =6 AND DATE( date_time ) >= '{$this->startdate}'
				AND DATE( date_time ) <= '{$this->enddate}'
				GROUP BY action_value ORDER BY num DESC {$qLimit} ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		return $qData;
	
	}
	
}

?>