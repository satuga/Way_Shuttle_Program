<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
* File to handle api calls from pi component.
* Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
* text - wayapppi
* encyption key - smartparking
* bit - 256 bit
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
			$retArr['gateid']=0;
			$retArr = removeNull($retArr);
			echo json_encode($retArr);
			exit();
		}


		$ActionType =$objUtil->sanitize($this->fields_val['actiontype']) ;

		if ( $ActionType=="add" ||$ActionType=="edit" )
		{
			if ( $ActionType=="add" )
			{
				if ( !isset($_GET['gatename']) || !isset($_GET['gatetype']) || !isset($_GET['garageid'])  || !isset($_GET['SocketMac'])|| !isset($_GET['beaconmac'])|| !isset($_GET['txmax']) || !isset($_GET['os']))
				{
					echo $this->fnAuthInvalidParam();
					exit();
				}
			}
			else
			{
				if ( !isset($_GET['gateid']) || !isset($_GET['gatename']) || !isset($_GET['gatetype']) || !isset($_GET['garageid'])  || !isset($_GET['SocketMac'])|| !isset($_GET['beaconmac'])|| !isset($_GET['txmax']) || !isset($_GET['os']))
				{
					echo $this->fnAuthInvalidParam();
					exit();
				}
			}


			$objGateDO = new GateManagerDO();
			$objGateDO->GateName = $objUtil->sanitize($_GET['gatename']);
			$objGateDO->GateType = $objUtil->sanitize($_GET['gatetype']);
			$objGateDO->GarageID = $objUtil->sanitize($_GET['garageid']);
			$objGateDO->SocketMac = $objUtil->sanitize($_GET['socketmac']);

			$objGateDO->BeaconMac = $objUtil->sanitize($_GET['beaconmac']);
			$objGateDO->TxMax = $objUtil->sanitize($_GET['txmax']);
			$objGateDO->OS = $objUtil->sanitize($_GET['os']);
			$objGateDO->ActionType = $objUtil->sanitize($ActionType);

			if ( $ActionType=="add" )
			{
				echo $this->ManageGateInfo($objGateDO);
			}
			else
			{
				$objGateDO->GateID = $objUtil->sanitize($_GET['gateid']);
				echo $this->ManageGateInfo($objGateDO);
			}

		}

		else if ( $ActionType=="delete"  )
		{
			if ( !isset($_GET['gateid']))
				{
					echo $this->fnAuthInvalidParam();
					exit();
				}

			echo $this->DeleteGateInfo($objUtil->sanitize($_GET['gateid']));
		}

		else if ( $ActionType=="list"  )
		{
			 echo $this->GetGateList();
		}

	}


	function GetGateList()
	{
		$objGateDA = new GateManagerDA();

		$ReturnVal=$objGateDA->GetGateList();

		ob_end_clean();
		$retArr =array();
		$flag=1;
		$msg="";

		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['actiontype']='list';
		$retArr['gatelist']=$ReturnVal;
		$retArr = removeNull($retArr);
		return json_encode($retArr);
	}


	function DeleteGateInfo($GateID)
	{
		$objGateDA = new GateManagerDA();

		$ReturnVal=$objGateDA->DeleteSmartGate($GateID);

		ob_end_clean();
		$retArr =array();
		if($ReturnVal>=1)
		{
			$flag=1;
			$msg="";
		}
		else
		{
			$flag=-2;
			$msg="invalid gate id!";
		}

		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['actiontype']='delete';
		$retArr['gateid']=$GateID;
		$retArr = removeNull($retArr);
		return json_encode($retArr);
	}


	function ManageGateInfo(GateManagerDO $objGateDO)
	{
		$objGateDA = new GateManagerDA();

		if($objGateDO->ActionType=="add")
		{
			$ReturnVal=$objGateDA->AddGateInfo($objGateDO);
		}
		else
		{
			$ReturnVal=$objGateDA->UpdateGateInfo($objGateDO);
		}
		$GateID =$ReturnVal;


		ob_end_clean();
		$retArr =array();
		if($ReturnVal>=1)
		{
			$flag=1;
			$msg="";
		}
		else
		{
			$flag=$ReturnVal;
			$msg="input errors";
			if($ReturnVal==-2)
			{
				$msg="invalid gate type!";
			}
			else if($ReturnVal==-3)
			{
				$msg="invalid garageid!";
			}
			else if($ReturnVal==-4)
			{
				$msg="garageid does not exist!";
			}
			$GateID=0;
		}



		$retArr['success']=$flag;
		$retArr['error']=$msg;
		$retArr['actiontype']=$objGateDO->ActionType;
		$retArr['gateid']=$GateID;
		$retArr = removeNull($retArr);
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
		$retArr['gateid']=0;
		$retArr = removeNull($retArr);
		return json_encode($retArr);
	}

}
$auth = new authService();
