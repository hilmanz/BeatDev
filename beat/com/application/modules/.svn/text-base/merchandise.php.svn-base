<?php
class merchandise extends App{

	function beforeFilter(){
		$this->contentmodHelper = $this->useHelper('contentmodHelper');
		$this->searchmodHelper = $this->useHelper('searchmodHelper');
		$this->uploadmodHelper = $this->useHelper('uploadmodHelper');
		$this->usermodHelper = $this->useHelper('usermodHelper');
		$this->merchandiseHelper = $this->useHelper('merchandiseHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
		
	}
	
	function main(){
		
		$getMerchandiseList = $this->contentmodHelper->getMerchandiseList(null,12);
		//pr($getMerchandiseList);exit;
		$this->assign('merchandise',$getMerchandiseList);
		$this->assign('total',$getMerchandiseList['total']);
		$m_type = strip_tags($this->_p('m_type'));
		$n = intval($this->_p('n_status'));
		if($n==0) $n=1;
		$this->assign('n_status',$n);
		$this->assign('m_type',$m_type);
		$this->log('surf','merchandise');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/merchandise-pages.html');
	}

	function redeem(){
		global $CONFIG;
		$getredeemList = $this->merchandiseHelper->redeemList();
		$ajax = intval($this->_p('ajax'));
		$verifyid = intval($this->_g('id'));
		//var_dump($verifyid);exit;
		if($ajax>0){
			//pr($getredeemList);exit;
			print json_encode($getredeemList);exit;
		}elseif($verifyid>0){
			$setredeemVerify = $this->merchandiseHelper->redeemVerify($verifyid);
			if($setredeemVerify){
				$this->log('surf','verify redeem');
				$this->assign('msg',"Verifying Success");
			}else{
				$this->assign('msg',"Verifying failed");
			}
			sendRedirect($CONFIG['BASE_DOMAIN']."merchandise/redeem");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}else{
			//$this->assign('redeem',$getredeemList);
			
			$this->log('surf','redeem-list');
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/merchandise-redeem.html');
		}
	}

	function redeemExcel(){
		global $CONFIG;
		//var_dump('foo');exit;
		$dd = date("F_j_Y", time());
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");			
		header("Content-Disposition: attachment; filename=Redeem_Activity_{$dd}.xls");  
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		$redeemListDownload = $this->merchandiseHelper->redeemListDownload();

		//var_dump($redeemListDownload);exit;
		echo "<table border='1'>";
		echo "<tr><th>NAME</th><th>EMAIL</th><th>REDEEM DATE</th><th>MERCHANDISE</th><th>REDEEM STATUS</th></tr>";
		foreach ($redeemListDownload as $key => $val){	
			if($val['n_status']>0) $status = "Verified";
			else $status = "Unverify";
			echo "<tr>";
			echo "<td>{$val['name']}</td>
				 <td>{$val['email']}</td> 	
				 <td>{$val['redeemdate']}</td> 	
				 <td>{$val['badge_name']}</td>
				 <td>{$status}</td> ";
			echo "</tr>";
		}
		echo "</table>";
	
		exit;
	}

	function create(){
		global $CONFIG;
		if(strip_tags($this->_p('merchandise'))=='1') {
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."merchandises/";
			$data = false;
		
			if (isset($_FILES['merchandise_img']) && $_FILES['merchandise_img']['name']!=NULL) {
				if (isset($_FILES['merchandise_img']) && $_FILES['merchandise_img']['size'] <= 20000000) {
					$data = $this->uploadmodHelper->uploadThisImage($_FILES['merchandise_img'],$path);
					
				}
			}


			$result = $this->contentmodHelper->addMerchandise($data);
			
			if($result) {
				$this->log('submitMerchandise',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."merchandise");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		$this->log('surf','create - badges');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/create-merchandise.html');
	}
	
	function edit(){
		global $CONFIG;
		$id = intval($this->_g('id'));
		$result = $this->contentmodHelper->detailMerchandise($id);
		$this->assign('detailMerchandise',$result);
		$this->log('surf','edit - merchandise');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/edit-merchandise.html');
	}

	function deleteMerchandise(){
		global $CONFIG;
		$deleteMerchandise = $this->contentmodHelper->deleteMerchandise();
		if($deleteMerchandise){
			$this->log('surf','delete - merchandise');
			sendRedirect($CONFIG['BASE_DOMAIN']."merchandise");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}


	}

	function publishMerchandise(){
		global $CONFIG;
		$publishMerchandise = $this->contentmodHelper->publishMerchandise();
		if($publishMerchandise){
			$this->log('surf','publish - merchandise');
			sendRedirect($CONFIG['BASE_DOMAIN']."merchandise");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}


	}

	function ajax(){
		// pr('masukkkk');exit;
		$needs = $this->_request("needs");
		$start = intval($this->_request("start"));
		if($needs=="merchandise") $data =  $this->contentmodHelper->getMerchandiseList($start,12);
		print json_encode($data);exit;
	}
}
?>