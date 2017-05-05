<?php

class baentourageHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
		 $this->data = false;
	}
	
	
		 
	function senddatabatoentourage( ){	
				
				//read xls
			$files = $_FILES['files'];
			
			global $ENGINE_PATH;
			include_once $ENGINE_PATH."Utility/excel_reader2.php";
		
			$data = new Spreadsheet_Excel_Reader($files['tmp_name']);
		
			$n = 1;
			$x = 0;
			$y = 0;
			$newarr = false;
			foreach($data->realraw() as $val){
					if($n<6) $keys[] = str_replace(".","",str_replace(" ","",$val));
					
					if($n>5) {	
						
						$newarr[$y]["{$keys[$x]}"] = $val;						
						
						if($x==4){
							$x=0;
							$y++;
						}else  {
							$x++;
							
						}
						
					
					}
					$n++;
					
			}
			 // pr($data->realraw()); 
			 // pr($newarr );exit;
			foreach($newarr as $val){
				$this->data = false;
				$report[] = $this->sendthedata($val);
				
			}
			
			return $report;
			
			
	}
	
	function sendthedata($data){
				
				$sql = " 
				INSERT IGNORE INTO ba_member 
				(email ,	register_date ,	name ,	last_name ,	n_status  ) 
				VALUES  ( '{$data['email']}', '{$data['register_date']}','{$data['name']}','{$data['last_name']}',1 )	
				";
							
				$this->apps->forceopendb(1);
				$q = $this->apps->query($sql);
				$this->data[] = $data['email'];
				if($q) $this->data[] = 'insert BA adding data';
				else $this->data[] = 'not insert BA adding data';
				
				// SELECT BA
				$sql = " SELECT id baid FROM ba_member WHERE email='{$data['email']}' LIMIT 1 ";
				$badata = $this->apps->fetch($sql);
				
				if(!$badata) {
					$this->data[] = 'there is no BA data';
					
				}else 	{ 
					
					$this->data[] = 'there is  BA data';
					
					
					// SELECT ENTURAGE
					$sql = " SELECT email entourageemail,id entourageid FROM social_member WHERE email='{$data['entourageemail']}' LIMIT 1 ";
				 
					$this->apps->forceopendb(0);
					$entouragedata = $this->apps->fetch($sql);
					$this->data[] = $data['entourageemail'];
					if(!$entouragedata) {
						$this->data[] = 'there is no ENTOURAGE data';
						 
					}else 	{ 
						$this->data[] = 'there is ENTOURAGE data';
						
						
						$sql = "INSERT IGNORE INTO ba_entourage 
						(  	entourageid ,entourageemail,baid,n_status ) 
						VALUES  ( '{$entouragedata['entourageid']}', '{$entouragedata['entourageemail']}','{$badata['baid']}',1  )	
						";
				
						$this->apps->forceopendb(1);
						$qData = $this->apps->query($sql);
						
						if(!$qData) {
							$this->data[] = 'insert ENTOURAGE data';
							
						}else {
							$this->data[] = 'no insert ENTOURAGE data';
						
						}
					}
				}
				
				return $this->data;
	}

}

?>