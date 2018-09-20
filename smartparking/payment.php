<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
include ('config.php');
require_once('stripe-config.php');
include("model/common.class.php");
include("DataAccessLayer/TransactionManagerDA.php");

class PaymentService {

    var $dbMsg;
    private $user_id, $txn_id, $amount, $stripe_token, $card_id, $pay_type, $gate_id;

    function __construct() {
        global $EncryptKey, $con;
        $this->from_date = "";
        $this->to_date = $this->TotalAmount = "";
        $this->dbMsg = "Error in transaction. Please try again.";
        $this->enckey = $EncryptKey;
        // User Authentication
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->user_id = isset($this->fields_val['user_id']) ? $this->fields_val['user_id'] : '';
        $this->txn_id = isset($this->fields_val['txn_id']) ? $this->fields_val['txn_id'] : '';
        $this->amount = isset($this->fields_val['amount']) ? $this->fields_val['amount'] : '';
        // $this->stripe_token = isset($this->fields_val['stripe_token']) ? $this->fields_val['stripe_token'] :'';
        $this->garage_id = isset($this->fields_val['garage_id']) ? $this->fields_val['garage_id'] : '';
        $this->card_id = isset($this->fields_val['card_id']) ? $this->fields_val['card_id'] : '';
        $this->pay_type = isset($this->fields_val['pay_type']) ? $this->fields_val['pay_type'] : '';
        $this->gate_id = isset($this->fields_val['gate_id']) ? $this->fields_val['gate_id'] : '';
        if ($this->user_id == "" || $this->txn_id == "" || $this->amount == "" || $this->card_id == "" || $this->pay_type == "" || $this->gate_id == "") {
            //|| $this->stripe_token==""
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $ValidPayment = $this->ValidPayment($this->user_id, $this->txn_id, $this->garage_id);

        if (strtolower($this->pay_type) != '' && $ValidPayment == 1) {

            $currency = "USD";
            $response = array();
            try {
                // Get Credit card details
                $sql = "select *,AES_DECRYPT(Card_Number,'" . $this->enckey . "') AS CC_NUMBER from tbl_creditcarddetails where Card_User_ID ='" . $this->user_id . "' AND Card_ID ='" . $this->card_id . "'";
                $exe = mysqli_query($con, $sql) or die($this->TransactionError());
                $num = mysqli_num_rows($exe);

                if ($num > 0) {
                    $rec = mysqli_fetch_array($exe);
                    $charge = Stripe_Charge::create(array(
                                'customer' => $rec['Card_StripeCustID'],
                                'amount' => $this->amount * 100, // A positive integer in the smallest currency unit So for ten dollars, you need to pass 1000.
                                'currency' => "USD"
                    ));
                } else {
                    echo $this->fnAuthInvalidParam(1);
                    exit();
                }
            } catch (Exception $e) {
                if ($e instanceof Stripe_CardError || $e instanceof Stripe_InvalidRequestError || $e instanceof Stripe_AuthenticationError || $e instanceof Stripe_ApiConnectionError || $e instanceof Stripe_Error) {
                    ob_end_clean();
                    $content = array("status" => "0", "response" => "error", "message" => $e->getMessage());
                    echo json_encode($content);
                    exit();
                } else {
                    ob_end_clean();
                    $content = array("status" => "0", "response" => "error", "message" => $e->getMessage());
                    echo json_encode($content);
                    exit();
                }
            }
            if ($charge->paid == "1" && $charge->id != "") {
                // https://api.stripe.com/v1/charges/$charge->id
                $this->charge_id = $charge->id;
                $this->Updategatestatus($this->gate_id, $this->user_id);
                echo $this->fnAuthExit();
                exit();
            }
        } else {
            echo $this->fnAuthInvalidParam();
            exit();
        }
    }

    function __destruct() {
        unset($this, $charge);
    }

    function fnAuthInvalidParam($msg = "") {
        $msg = $msg != '' ? $msg : 'Invalid Parameters';
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => $msg);
        return json_encode($content);
    }

    function TransactionError() {
        ob_end_clean();
        return json_encode(array("status" => "0", "response" => "error", "message" => $this->dbMsg));
    }

    function ValidPayment($user_id, $txn_id, $garage_id) {
        GLOBAL $con;
        $sql_select_trans = "select txn_id,transaction_id from tbl_gate_parking_transaction where txn_id='" . $txn_id . "' and out_time is NULL and garage_id='" . $garage_id . "' and user_id='" . $user_id . "'";
        $trans_select_result = mysqli_query($con, $sql_select_trans) or die($this->TransactionError());
        $num_select = mysqli_num_rows($trans_select_result);
        if ($num_select > 0) {
            $row_select = mysqli_fetch_assoc($trans_select_result);
            $this->transaction_id = $row_select['transaction_id'];
            return 1;
        } elseif ($num_select > 1) {
            echo $this->fnAuthInvalidParam("More than one gate entry found.");
            exit();
        } else {
            echo $this->fnAuthInvalidParam("No Records found.");
            exit();
        }
    }

    function Updategatestatus($gateID, $UserID) {
        GLOBAL $con;
        $objTranDA = new TransactionManagerDA();
        //updating gate status
        $objTranDA->UpdateGateEntryStatus($gateID, $UserID);
    }

    function fnAuthExit() {
        GLOBAL $con;
        $entry_mode = 2;
        $gate_status = 'open';
        $flag = 1;
        $sql_update_trans = "update tbl_gate_parking_transaction set gate_id='" . $this->gate_id . "', out_time=now(), last_updated=now(), entry_mode='" . $entry_mode . "', ip_address='" . $_SERVER["REMOTE_ADDR"] . "', user_agent='" . $_SERVER["HTTP_USER_AGENT"] . "', total_time=null, amount='" . $this->amount . "',charge_id='" . $this->charge_id . "' where transaction_id='" . $this->transaction_id . "'";
        $trans_update_result = mysqli_query($con, $sql_update_trans) or die($this->TransactionError());

        $sql = "select from_date , to_date,TotalAmount from tbl_paymenttransaction where TxnID='" . $this->txn_id . "'";
        $exe = mysqli_query($con, $sql);
        $num = mysqli_num_rows($exe);

        if ($num == 1) {
            $payment_rec = mysqli_fetch_array($exe);
            $this->from_date = $payment_rec['from_date'];
            $this->to_date = $payment_rec['to_date'];
            $this->TotalAmount = $payment_rec['TotalAmount'] + $this->amount;

            $fromdateAvailability = $this->from_date;
            $todateAvailability = $this->to_date;
            $parkingidForSpaces = $this->garage_id;
            $this->increaseSpacesByPid($fromdateAvailability, $todateAvailability, $parkingidForSpaces);
            //$this->UpdateParkingQntyWithOrderID($this->txn_id);
            //$this->ReduceParkingQntyWithOrderID($this->txn_id);
        }

        $retArr = array();
        $status = $flag > 0 ? 1 : 0;
        $retArr['status'] = $status;
        $retArr['success'] = $flag;
        $retArr['error'] = "";
        $retArr['transaction_id'] = $this->transaction_id;
        $retArr['gate_status'] = $gate_status;
        $retArr['from_date'] = $this->from_date;
        $retArr['to_date'] = $this->to_date;
        $retArr['amount'] = $this->TotalAmount;
        $retArr['gate_mode'] = $entry_mode;
        $retArr = removeNull($retArr);
        ob_end_clean();
        return json_encode($retArr);
    }

    function increaseSpacesByPid($fromdateAvailability, $todateAvailability, $parkingidForSpaces) {
        GLOBAL $con;
        if (!empty($fromdateAvailability) && !empty($todateAvailability) && !empty($parkingidForSpaces)) {
            $fromdateAvailability = date('Y-m-d', strtotime($fromdateAvailability));
            $todateAvailability = date('Y-m-d', strtotime($todateAvailability));
            if (!empty($parkingidForSpaces)) {
                if (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                    while (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                        echo $availability_upsql = "UPDATE tbl_parkingweekdayavailability set occupiedSpaces = IF(occupiedSpaces <= PA_No_Spaces AND 0 < occupiedSpaces, (occupiedSpaces)-1, occupiedSpaces) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
                        @mysqli_query($con, $availability_upsql);
                        $fromdateAvailability = date("Y-m-d", strtotime("+1 day", strtotime($fromdateAvailability)));
                    }
                } else if (strtotime($fromdateAvailability) == strtotime($todateAvailability)) {
                    $availability_upsql = "UPDATE tbl_parkingweekdayavailability set occupiedSpaces =  IF(occupiedSpaces <= PA_No_Spaces AND 0 < occupiedSpaces, (occupiedSpaces)-1, occupiedSpaces) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
                    @mysqli_query($con, $availability_upsql);
                }
            }
        }
    }

    function descreaseSpacesByPid($fromdateAvailability, $todateAvailability, $parkingidForSpaces) {
        GLOBAL $con;
        if (!empty($fromdateAvailability) && !empty($todateAvailability) && !empty($parkingidForSpaces)) {
            $fromdateAvailability = date('Y-m-d', strtotime($fromdateAvailability));
            $todateAvailability = date('Y-m-d', strtotime($todateAvailability));
            if (!empty($parkingidForSpaces)) {
                if (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                    while (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                        $availability_upsql = "UPDATE tbl_parkingweekdayavailability set occupiedSpaces = IF(occupiedSpaces >= PA_No_Spaces , occupiedSpaces, (occupiedSpaces)+1) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
                        @mysqli_query($con, $availability_upsql);
                        $fromdateAvailability = date("Y-m-d", strtotime("+1 day", strtotime($fromdateAvailability)));
                    }
                } else if (strtotime($fromdateAvailability) == strtotime($todateAvailability)) {
                    $availability_upsql = "UPDATE tbl_parkingweekdayavailability set occupiedSpaces = IF(occupiedSpaces >= PA_No_Spaces , occupiedSpaces, (occupiedSpaces)+1) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
                    @mysqli_query($con, $availability_upsql);
                }
            }
        }
    }

    function UpdateParkingQntyWithOrderID($txtOrderID) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE, $con;
        /* $DBSERVER = DB_SERVER;
          $DBUSER = DB_USERNAME;
          $DBPWD = DB_PASSWORD;
          $DBDATABASE = DB_DATABASE; */
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $dbh->prepare("CALL USP_UpdateParkingQntyWithOrderID(:txtOrderID)");
            $stmt->bindParam(':txtOrderID', $txtOrderID);
            $stmt->execute();
            $stmt = null;
            $dbh = null;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

    function ReduceParkingQntyWithOrderID($txtOrderID) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE, $con;

//                $DBSERVER = DB_SERVER;
//                $DBUSER = DB_USERNAME;
//                $DBPWD = DB_PASSWORD;
//                $DBDATABASE = DB_DATABASE;

        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $dbh->prepare("CALL USP_ReduceParkingQntyWithOrderID(:txtOrderID)");

            $stmt->bindParam(':txtOrderID', $txtOrderID);
            $stmt->execute();

            $stmt = null;
            $dbh = null;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

}

$auth = new PaymentService();
?>
