<?php
error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");
class garageService{
	var $dbMsg;
    // Major = gate_id
    // Minor = gatemode
	private $beacon_id,$major,$minor,$user_id;
	function __construct(){
        $this->dbMsg = "Error in transaction. Please try again.";
		$commonCls = new CommonClass();
		$this->fields_val = $commonCls->fnAssignval();
        $this->beacon_id = isset($this->fields_val['beacon_id']) ? $this->fields_val['beacon_id'] :'';
        $this->major = isset($this->fields_val['major']) ? $this->fields_val['major'] :'';
        $this->minor = isset($this->fields_val['minor']) ? $this->fields_val['minor'] :'';
		$this->user_id = isset($this->fields_val['user_id']) ? $this->fields_val['user_id'] :'';
		if ($this->beacon_id=="" || $this->major=="" || $this->minor=="")
		{
			echo $this->fnAuthInvalidParam();
			exit();
		}
		$res = $this->fnGetGarageDetails($this->beacon_id,$this->major,$this->minor);
    }
	function __destruct() {
       unset($this);
   	}
    function fnAuthInvalidParam(){
		ob_end_clean();
		$content = array("status" => "0","response" => "error", "message" => 'Invalid Parameters');
		return json_encode($content);
    }
    function fnGetGarageDetails($beacon_id,$major,$minor)
    {
        GLOBAL $con;
        $garage_details = array();
		$today=date("Y-m-d H:i:s");
		// echo $sql="select * from tbl_smartgate
		// 	  inner join tbl_paymenttransaction on UsrID='".$this->user_id."' and Parking_ID > 0 and Parking_ID=SGT_GarageID
		// 	  left join tbl_gate_parking_transaction on txn_id=TxnID and garage_id=Parking_ID
		//  where SGT_ID='".$this->major."' and SGT_GateType='".$this->minor."' and SGT_BeaconUuid ='".$this->beacon_id."'  group by TxnID"; die;
		echo $sql ="select *,tbl_paymenttransaction.from_date as checkIn,tbl_paymenttransaction.to_date as checkOut from tbl_smartgate
		 		inner join tbl_paymenttransaction on Parking_ID>0 and Parking_ID=SGT_GarageID and UsrID='".$this->user_id."'
    			left join tbl_gate_parking_transaction on garage_id=SGT_GarageID and txn_id=TxnID
 				where SGT_ID='".$this->major."' and SGT_GateType='".$this->minor."' and SGT_BeaconUuid ='".$this->beacon_id."'
 				group by TxnID order by TxnID desc"; //die;
		$results=mysqli_query($con,$sql);
		//and gate_id='".$this->major."' and entry_mode='".$this->minor."'
		// and to_date >= '".$today."' and from_date <= '".$today."'
		if ($results===false) {
			ob_end_clean();
			$content = array("status" => "0","response" => "error", "message" => $this->dbMsg);
			echo json_encode($content);
			exit;
		}
        else {
			$num_rows = mysqli_num_rows($results);
			if($num_rows > 0){
				$i=0;
				while ($result=mysqli_fetch_object($results)) {
					$txn_id =  $result->txn_id;
					$order_txn_id = $result->TxnID;
					$gatemode=  $result->entry_mode;
					$smartIsValid=1;
					// check record exist or not
					if($txn_id!=$order_txn_id && $txn_id=='')
					{
						$smartIsValid=1;
						$gatemode="1";

					}
					else if($txn_id==$order_txn_id && $gatemode==1)
					{
						$smartIsValid=1;
						$gatemode="2";
					}
					else $smartIsValid=0;
					// $garage_details[$i]["id"] = $result->id;
					// $smartIsValid = $result->txn_id ==  $result->TxnID &&;
					if($smartIsValid==1){
						$sql="select * from tbl_smartgate where SGT_GarageID='".$result->SGT_GarageID."' and SGT_GateType='".$gatemode."'";
						$exe=mysqli_query($con,$sql);
						$res=mysqli_fetch_object($exe);
						$garage_details[$i]["smart_valid"] = $smartIsValid;
						$garage_details[$i]["garage_id"] = $result->SGT_GarageID;
						$garage_details[$i]["txn_id"] = $result->TxnID;
						$garage_details[$i]["txnid"] = $result->txn_id;
						$garage_details[$i]["gatemode"] = $gatemode;
						$garage_details[$i]["entry_mode"] = $result->entry_mode;
						//$garage_details[$i]["today"] = $today;
						$garage_details[$i]["today_format"] = date("Y-m-d H:i A");
						//$garage_details[$i]["from_date"] = $result->checkIn;
						$garage_details[$i]["from_date_formate"] = date("Y-m-d H:i A",strtotime($result->checkIn));
						//$garage_details[$i]["to_date"] = $result->checkOut;
		                $garage_details[$i]["to_date_formate"] = date("Y-m-d H:i A",strtotime($result->checkOut));
		                $garage_details[$i]["beacon_major"] = $res->SGT_ID;
		                $garage_details[$i]["beacon_minor"] = $res->SGT_GateType;
		                $garage_details[$i]["beacon_id"] = $res->SGT_BeaconUuid;
		                $garage_details[$i]["name"] = $res->SGT_GateName;
		                $garage_details[$i]["mac"] = $res->SGT_Mac;
		                $garage_details[$i]["os"] = $res->SGT_IsGateOpen;
		                $garage_details[$i]["bt_mac"] = $res->SGT_BtMac;
		                $garage_details[$i]["beacon_mac"] = $res->SGT_BtBeaconMac;
		                $garage_details[$i]["gate_status"] = $res->SGT_IsGateOpen;
						$garage_details[$i]["updated_at"] = $res->SGT_LastUpdatedTime;
		                // $garage_details[$i]["status"] = $result->sgt_status;
		                // $garage_details[$i]["created_at"] = $result->created_at;
		                // $garage_details[$i]["updated_at"] = $result->updated_at;
					}
					$i++;
				}
				foreach ($garage_details as $key => $val) {
					$array2[] = array_map(function($value) {
						   return $value == NULL ? "" : $value;
							}, $val);
				}

				ob_end_clean();
				if(!empty($array2)){
					$content = array("status" => "1","response" => "success","count"=>$num_rows, "data" => $array2);
					echo json_encode($content);
					exit;
				}
				else {
					ob_end_clean();
					$content = array("status" => "0","response" => "error", "message" => "No Record Found");
					echo json_encode($content);
					exit;
				}

			}
            else {
				ob_end_clean();
				$content = array("status" => "0","response" => "error", "message" => "No Record Found");
				echo json_encode($content);
				exit;
			}
		}

    }

}
$garage = new garageService();

 ?>
