<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
* File to handle api calls from pi component.
Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
text - wayapppi
encyption key - smartparking
bit - 256 bit
*/
include("common/config.php");
include("model/common.class.php");


include("DataObjectLayer/GateParkingTransactionManagerDO.php");
include("DataObjectLayer/PaymentInfoManagerDO.php");
include("DataObjectLayer/FeaturedBookingInfoManagerDO.php");


include("DataAccessLayer/ParkingManagerDA.php");
include("UtilLayer/UtilManagerUA.php");

class authService{
	var $dbMsg;
	function __construct(){
		global $EncryptKey;
		$this->enckey = $EncryptKey;
		$commonCls = new CommonClass();
		$this->fields_val = $commonCls->fnAssignval();	
		$hashVal = "Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=";
		$this->dbMsg = "Error in transaction. Please try again.";
		$garage_id = $this->fields_val['garage_id'];
		$user = 40;
		$sql="SELECT * FROM tbl_gate_parking_transaction WHERE IsValidEnty=1 AND out_time = 0 AND garage_id=".$garage_id." AND user_id=".$user;
		$exe=mysqli_query($con,$sql);
		$num=mysqli_num_rows($exe);
		$rec=mysqli_fetch_array($exe);
		if($num==1){
			echo $this->fnAuthExit();
		}
		else {
			echo $this->fnAuthEnter();
		} 
		//if($this->fields_val['action'] == "AuthEnter")
		/*else if($this->fields_val['action'] == "AuthExit"){
			echo $this->fnAuthExit();
		}*/
	}
	function fnCheckLogin(){
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
	/**
	* Function to check whether user entry is valid and put an entry in transaction table
	gate_id, user_id, password, garage_id
	If username and password not entered - return 0
	If error in DB transaction return -1
	If not a valid user then return -2
	if valid entry then return 1	
	Entry mode - entry will be 1 and exit will be 2
	STEP 1 - Check user is valid with their username and password
	STEP 2 - Check whether user has a valid gate transaction entry with out time is null
	STEP 3 - Insert into transaction table and return the id
	*/
	function fnAuthEnter(){
		$flag = 0;
		$msg = "";
		$trans_id = 0;
		$res = $this->fnCheckLogin();
		$flag = $res['flag'];
		if($flag == 1){


			$garage_id = $this->fields_val['garage_id'];
			$gate_id = $this->fields_val['gate_id'];
			$entry_mode = 1;
			$user_details = $res['user_details'];
			$user_id = $user_details->id;
			//$sql_trans = "select transaction_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '".$garage_id."' and user_id = '".$user_id."' and out_time is NULL";
			$sql_trans = "select transaction_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '".$garage_id."' and user_id = '".$user_id."' and out_time = 0";
			$trans_result=mysqli_query($con,$sql_trans);
			if ($trans_result===false) {
				$flag = -1;
				$msg = $this->dbMsg;
			} else {
				$trans_num_rows = mysqli_num_rows($trans_result);	
				if($trans_num_rows > 0){
					$flag = -3;//Already user has entered the garage and not checked out
					$msg = "User entry already exists for this garage";
				} else {

				$objTran = new ParkingManagerDA();
				
				//Check furure booking availability
				 
				$objFBookingInfo = new FeaturedBookingInfoManagerDO();
				echo "1111";
				$objFBookingInfo=$objTran->GetFeaturedBooking(user_id,$this->fields_val['garage_id'],$EntryDateTime);
				
				if($objFBookingInfo->IsFeaturedBooking==1)
					{
						$IsAvailability=1;
						$FeaturedBookingID = $objFBookingInfo->BookingID;
					}
					else
					{
						//Check parking availability
						$IsAvailability=$objTran->GetAvailability($this->fields_val['garage_id']);
						$FeaturedBookingID=0;
					}
				
				
				 
				//Start Adding Gate transaction CODE:GOPALANMANI-FEB2005
					$objUtil = new UtilManagerUA();
					$objParkGateTran = new GateParkingTransactionManagerDO();
					$objParkGateTran->GateID = $this->fields_val['gate_id'];
					$objParkGateTran->GarageID = $this->fields_val['garage_id'];
					$objParkGateTran->UserID = $user_details->id;
					$objParkGateTran->EntryMode = 1;
					$objParkGateTran->UserDetails = $res['user_details'];
					$objParkGateTran->IPAddress = $objUtil->get_client_ip();
					$objParkGateTran->FeaturedBookingID = $FeaturedBookingID;
					$objParkGateTran->HTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];
					if($IsAvailability ==1)
					{
						$objParkGateTran->IsValidEnty =1;
					}
					else
					{
						$objParkGateTran->IsValidEnty =0;
					}

					$tranval=$objTran->AddGateParkingTransaction($objParkGateTran);
					//End Adding Gate transaction CODE:GOPALANMANI-FEB2005

				if($IsAvailability ==1)
				{
					$flag = 1;
					$msg  = $tranval['msg'];
					$trans_id=$tranval['trans_id'];
				}
				else
				{
					$flag = -4;
					$msg  = "Space is not avalable";
					$trans_id=0;
					
				}
	
				}
			}	
		} else {
			$msg = $res['msg'];
		}
		ob_end_clean();
		$retArr=array();
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['transaction_id']=$trans_id;
		
		return json_encode($retArr);
	}
		
	 
	function fnAuthExit(){
		$flag = 0;
		$msg = "";
		$trans_id = 0;
		$res = $this->fnCheckLogin();	
		$objTran = new ParkingManagerDA();
		$flag = $res['flag'];
		if($flag == 1){
			$garage_id = $this->fields_val['garage_id'];
			$gate_id = $this->fields_val['gate_id'];
			$entry_mode = 2;
			$user_details = $res['user_details'];
			$user_id = $user_details->id;
			$sql_trans = "select transaction_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '".$garage_id."' and user_id = '".$user_id."' and out_time is NULL";
			//echo $sql_trans;
			$trans_result=mysqli_query($con,$sql_trans);
			if ($trans_result===false) {
				$flag = -1;
				$msg = $this->dbMsg;
			} else {
				$trans_num_rows = mysqli_num_rows($trans_result);	
				if($trans_num_rows == 0){
					$flag = -3;//Already user has entered the garage and not checked out
					$msg = "User entry doesn't exists for this garage";
				} else {
					if($trans_num_rows == 1){
						
						
						$trans_details=mysqli_fetch_object($trans_result);						
						$transaction_id = $trans_details->transaction_id;
						
						
							$trans_id = $transaction_id;
							$objParkMDA = new ParkingManagerDA();
							$objPayinfoDO = new PaymentInfoManagerDO();
							$objPayinfoDO = $objParkMDA->GetTransactionAmount($trans_id);
							
							$IsValidPayment =true;
							//ValidPayment($objPayinfoDO);

							if($IsValidPayment ==true)
							{
								//$objParkMDA->AddTransaction($objPayinfoDO);
							
				 				$flag = 1;
								$sql_update_trans = "update tbl_gate_parking_transaction set gate_id='".$gate_id."', out_time=now(), last_updated=now(), entry_mode='".$entry_mode."', ip_address='".$_SERVER["REMOTE_ADDR"]."', user_agent='".$_SERVER["HTTP_USER_AGENT"]."', total_time=null, amount=0 where transaction_id='".$transaction_id."'";
								$trans_update_result=mysqli_query($con,$sql_update_trans);
							}
							else
							{
								$flag = -5;
								$msg = "payment failed.";
							}

					} else {
						$flag = -4;
						$msg = "More than one gate entry found.";
					}
				}
			}
		} else {
			$msg = $res['msg'];
		}
		ob_end_clean();
		$retArr =array();
		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['transaction_id']=$transaction_id;
		$retArr['from_date']=date("F j, Y, g:i a",$objPayinfoDO->FromDate);
		$retArr['to_date']=date("F j, Y, g:i a",$objPayinfoDO->ToDate);
		$retArr['amount']='$'.$objPayinfoDO->TotalAmount;
		return json_encode($retArr);
	}
}
$auth = new authService();