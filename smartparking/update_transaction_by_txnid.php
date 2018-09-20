<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class parkingService {

    var $dbMsg;
    // Major = gate_id
    // Minor = gatemode
    private $txn_id;

    function __construct() {
        $this->dbMsg = "Something went wrong. Please try again.";
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->txn_id = isset($this->fields_val['txn_id']) ? $this->fields_val['txn_id'] : '';
        if ($this->txn_id == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetParkingDetails($this->txn_id);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetParkingDetails($txn_id) {
        GLOBAL $con;
        
        $getTransactionDetailBYTxnID = $this->getTransactionDetailBYTxnID($txn_id);
        if(!empty($getTransactionDetailBYTxnID)){
            $fromdate = $getTransactionDetailBYTxnID['from_date'];
            $todate = $getTransactionDetailBYTxnID['to_date'];
            $this->updateTransactionDetailBYTxnID($txn_id, $fromdate, $todate);
        }
        ob_end_clean();
        $info = array();
        $info['txn_id'] = $txn_id;
        $content = array("status" => 1, "data" => $info);
        echo json_encode($content);
        exit;
    }

    function getTransactionDetailBYTxnID($txn_id){
        GLOBAL $con;
        $email = '';
        $mainDetail = array();
        $sqlQuery="select from_date , to_date from tbl_paymenttransaction where TxnID='".$txn_id."'";
        $sqlResult = mysqli_query($con, $sqlQuery);
        $countResult = mysqli_num_rows($sqlResult);
        if ($countResult > 0) {
            $mainDetail = mysqli_fetch_assoc($sqlResult);
        }
        return $mainDetail;
    }
    function updateTransactionDetailBYTxnID($txn_id, $fromdate, $todate){
        GLOBAL $con;
        $diff= strtotime($todate) - strtotime($fromdate);
        $fromdate = date('Y-m-d H:i:s');
        $todate1=date('Y-m-d H:i:s', strtotime($fromdate.'+'.$diff.' seconds'));
        $sqlQuery = "update tbl_paymenttransaction set from_date = '".$fromdate."', to_date='".$todate1."' where TxnID='".$txn_id."'";
        mysqli_query($con, $sqlQuery);
        return true;
    }
    
    function getBeaconDetail($garage_id, $gatemode = 1, $txn_ids = '') {
        GLOBAL $con;
        $beacon = array();
        if(!empty($txn_ids)){
            $dev_sql = "SELECT * from tbl_smartgate where SGT_GarageID IN (" . $txn_ids . ") and SGT_GateType='" . $gatemode . "'";
        } else {
            $dev_sql = "SELECT * from tbl_smartgate where SGT_GarageID = '".$garage_id."' and SGT_GateType='" . $gatemode . "'";
        }
        
        $dev_res = mysqli_query($con, $dev_sql);
        while ($results = mysqli_fetch_assoc($dev_res)) {
            //print_r($results);
            $result = array();
            $result['gate_id'] = $results['SGT_ID'];
            $result['garage_id'] = $results['SGT_GarageID'];
            $result['beacon_id'] = $results['SGT_BeaconUuid'];
            $result['gatemode'] = $results['SGT_GateType'];
            $result['gate_status'] = $results['SGT_IsGateOpen'];
            $beacon[] = $result;
        }
        return $beacon;
    }
    

}

$parking = new parkingService();
?>
