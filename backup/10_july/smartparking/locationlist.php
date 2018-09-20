<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
* File to handle api calls from pi component.
Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
text - wayapppi
encyption key - smartparking
bit - 256 bit
*/
include("config.php");
include("model/common.class.php");

include("DataObjectLayer/GateParkingTransactionManagerDO.php");
include("DataObjectLayer/PaymentInfoManagerDO.php");
include("DataObjectLayer/FeaturedBookingInfoManagerDO.php");

include("DataAccessLayer/ParkingManagerDA.php");
include("DataAccessLayer/TransactionManagerDA.php");

include("UtilLayer/UtilManagerUA.php");
include("DataObjectLayer/GateManagerDO.php");
include("DataAccessLayer/GateManagerDA.php");
class authService{
	var $dbMsg;
	function __construct(){
		global $EncryptKey,$con;
		$this->enckey = $EncryptKey;
		$commonCls = new CommonClass();
		$this->fields_val = $commonCls->fnAssignval();	
		$hashVal = "Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=";
		$this->dbMsg = "Error in transaction. Please try again.";
		
		$objUtil = new UtilManagerUA();
		$objTran = new TransactionManagerDA();
		 
		
		if ( !isset($_GET['actiontype']) || !isset($_GET['username']) || !isset($_GET['password'])  ) 
		{
			echo $this->fnAuthInvalidParam();
			exit();
		}
		
		$UserName=$objUtil->sanitize($_GET['username']);
		$Password=$objUtil->sanitize($_GET['password']);
		$EncryptKey=$objUtil->sanitize($this->enckey);
		
		//Validate login info
		$LoginStatus=$objTran->CheckIsValidLogin($UserName,$Password,$EncryptKey);
		if($LoginStatus!=1)
		{
			$flag = -1;
			$msg = "Incorrect Username or password";
			ob_end_clean();
			$retArr =array();
			$retArr['success']=$flag;
			$retArr['error']=$msg;
			$retArr['actiontype']='';
			 
		
			echo json_encode($retArr);
			exit();
		}
		 
		
		$ActionType =$objUtil->sanitize($this->fields_val['actiontype']) ;
		
		if ( $ActionType=="list"  ) 
		{
			 echo $this->GetSmartGateLocations(); 
		}
		 
	}
	
	
	function GetSmartGateLocations()
	{
		$objGateDA = new GateManagerDA();
		
		$ReturnVal=$objGateDA->GetSmartGateLocations();
		
		ob_end_clean();
		$retArr =array();
		$flag=1;
		$msg="";
				
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['actiontype']='list';
		$retArr['locationlist']=$ReturnVal;
		return json_encode($retArr);
	}
	
	
	 
	
	
	 
	
	 
	 
	 
	 
	 	
	function __destruct() {
       //print "Destroying " . $garage_id . "\n";
   } 
	 
	 
	
	function fnAuthInvalidParam()
	{
		$flag = -7;
		$msg = "invalid parameters";
				
		ob_end_clean();
		$retArr =array();
	
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['actiontype']='';
		$retArr['locationlist']=0;
		
		
		return json_encode($retArr);
	}
	
}
$auth = new authService();