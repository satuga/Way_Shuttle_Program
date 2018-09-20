<?php
/**
* File to handle api calls from pi component.
Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
text - wayapppi
encyption key - smartparking
bit - 256 bit
*/
include("../../common/config.php");
include("model/common.class.php");


include("DataObjectLayer/GateParkingTransactionManagerDO.php");
include("DataObjectLayer/PaymentInfoManagerDO.php");
include("DataObjectLayer/FeaturedBookingInfoManagerDO.php");

include("DataAccessLayer/ParkingManagerDA.php");
include("DataAccessLayer/TransactionManagerDA.php");

include("UtilLayer/UtilManagerUA.php");

class authService{
	var $dbMsg;
	function __construct(){
		global $EncryptKey;
		GLOBAL $con;
		$this->enckey = $EncryptKey;
		$commonCls = new CommonClass();
		$this->fields_val = $commonCls->fnAssignval();	
		$hashVal = "Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=";
		$this->dbMsg = "Error in transaction. Please try again.";
		$garage_id = $this->fields_val['garage_id'];
		$res = $this->fnCheckLogin();
		$user_details = $res['user_details'];
		$user = $user_details->id;
		
		if ( !isset($_GET['garage_id'])  || !isset($_GET['gate_id']) ) 
		{
			echo $this->fnAuthInvalidParam();
			exit();
		}
		
		echo $this->fnAuthEnter();
		
	}
	
	function fnCheckLogin()
	{
		GLOBAL $con;
		$arr = array();
		$flag = 0;
		$msg = "";
		$user_details = array();
		if($this->fields_val['username']!="" && $this->fields_val['password']!=""){			
			//$sql="select id, firstname, lastname from tbl_registeration WHERE email_add ='".$this->fields_val['username']."' AND encrypt_password=AES_ENCRYPT('".$this->fields_val['password']."','".$this->enckey."') AND status=1"; 
			$sql="select id, firstname, lastname from tbl_registeration WHERE email_add ='".$this->fields_val['username']."' AND status=1"; 
			//echo $sql;
			$result=mysqli_query($con,$sql);
			if ($result===false) {
				$flag = -1;
				$msg = $this->dbMsg;
			} else {
				$num_rows = mysqli_num_rows($result);						
				if($num_rows > 0){
					
					$flag = 1;
					$user_details=mysqli_fetch_object($result);
				} else {
					$flag = -2;
					$msg = "Incorrect Username and password";
				}
			}
		} else {
			$msg = "Please enter username and password";
		}
		$arr['flag'] = $flag;
		$arr['msg'] = $msg;
		$arr['user_details'] = $user_details;
		return $arr;
	}
	
	
	function fnAuthEnter()
	{
		GLOBAL $con;
		$flag = 1;
		$msg = "";
		$trans_id = 0;
		//$res = $this->fnCheckLogin();
		//$flag = $res['flag'];
		$Creditcardmsg="";
		
		$garage_id = $this->fields_val['garage_id'];
		$gate_id = $this->fields_val['gate_id'];
		
		$objTranDA1 = new TransactionManagerDA();
		
		$IsGateOpen=$objTranDA1->GetGateStatus($gate_id,$garage_id);
		
		if ($IsGateOpen==-1)
		{
			$flag=-1;
			$msg='Invalid garageid or gate id';
		}
		
		ob_end_clean();
		$retArr=array();
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['gate_status']=$IsGateOpen;
		return json_encode($retArr);
	}
		
	 
	 
	function fnAuthInvalid()
	{
		$flag = -6;
		$msg = "invalid gate entry";
		$trans_id = 0;
		
		$Creditcardmsg="";
		$gate_status='close';
		
		ob_end_clean();
		$retArr =array();
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		
		return json_encode($retArr);
	}
	
	function fnAuthInvalidParam()
	{
		$flag = -7;
		$msg = "invalid parameters";
		$trans_id = 0;
		
		$Creditcardmsg="";
		$gate_status='close';
		
		ob_end_clean();
		$retArr =array();
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		
		return json_encode($retArr);
	}
	
}
$auth = new authService();