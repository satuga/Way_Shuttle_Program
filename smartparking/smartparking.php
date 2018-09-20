<?php

header('Content-Type: application/json');
/**
 * File to handle api calls from pi component.
 * Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
 * text - wayapppi
 * encyption key - smartparking
 * bit - 256 bit
 */
error_reporting(1);
include("config.php");
include("model/common.class.php");
include("DataObjectLayer/GateParkingTransactionManagerDO.php");
include("DataObjectLayer/PaymentInfoManagerDO.php");
include("DataObjectLayer/FeaturedBookingInfoManagerDO.php");

include("DataAccessLayer/ParkingManagerDA.php");
include("DataAccessLayer/TransactionManagerDA.php");

include("UtilLayer/UtilManagerUA.php");

class authService {

    var $dbMsg;
    private $username, $password, $garage_id, $gatemode, $gate_id, $user_id, $user_flag, $user_details, $txn_id;

    function __construct() {
        global $EncryptKey, $con;
        $hashVal = "Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=";
        $this->dbMsg = "Error in transaction. Please try again.";
        $this->enckey = $EncryptKey;
        // User Authentication
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        /* $this->username = isset($this->fields_val['username']) ? $this->fields_val['username'] :'';
          $this->password = isset($this->fields_val['password']) ? $this->fields_val['password'] :'';
          if ($this->username=="" && $this->password=="")
          {
          echo $this->fnAuthInvalidParam();
          exit();
          }
          $res = $this->fnCheckLogin($this->username,$this->password); */
        $this->user_id = isset($this->fields_val['user_id']) ? $this->fields_val['user_id'] : '';
        $this->txn_id = isset($this->fields_val['txn_id']) ? $this->fields_val['txn_id'] : '';
        if ($this->user_id == "" || $this->txn_id == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetUserDetails($this->user_id);

        if (empty($res['user_details'])) {
            $trans_id = 0;
            $Creditcardmsg = "";
            $gate_status = 'close';
            ob_end_clean();
            $retArr = array();
            $retArr['success'] = $res['flag'];
            $retArr['error'] = $res['msg'];
            $retArr = removeNull($retArr);
            $retArr = removeNull($retArr);
            echo json_encode($retArr);
            exit();
        }

        $user_detail = $res['user_details']; // get email address from registration table
        $this->user_details = $user_detail;
        $this->user_id = $user_detail->id;
        $this->user_flag = $res['flag'];
        $this->garage_id = isset($this->fields_val['garage_id']) ? $this->fields_val['garage_id'] : '';
        $this->gatemode = isset($this->fields_val['gatemode']) ? $this->fields_val['gatemode'] : '';
        $this->gate_id = isset($this->fields_val['gate_id']) ? $this->fields_val['gate_id'] : '';
        if ($this->garage_id == '' || $this->gatemode == '' || $this->gate_id == '') {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        //print_r($this); die;
        $objTranDA = new TransactionManagerDA();

        $IsValid = $objTranDA->IsValidGateEntry($this->gate_id, $this->garage_id, $this->gatemode);
        if ($IsValid == 0) {
            echo $this->fnAuthInvalid();
            exit();
        }

        //$user = 40;
        $sql = "SELECT * FROM tbl_gate_parking_transaction WHERE IsValidEnty=1 AND entry_mode = 1 AND garage_id='" . $this->garage_id . "' AND user_id='" . $this->user_id . "' AND txn_id='" . $this->txn_id . "'";
        $exe = mysqli_query($con, $sql);
        $num = mysqli_num_rows($exe);
        $rec = mysqli_fetch_array($exe);
        if ($num == 1) {

            if ($this->gatemode != 1) {
                // user can exit from parking after 2 minutes
                $in_time = $rec['in_time'];
                $valid_datetime = strtotime(date('Y-m-d H:i:s', strtotime('+30 seconds', strtotime($in_time))));
                $current_datetime = strtotime(date('Y-m-d H:i:s'));
                if ($valid_datetime >= $current_datetime) {

                    ob_end_clean();
                    $retArr = array();
                    $retArr['status'] = 0;
                    $retArr['response'] = "error";
                    $retArr['message'] = "Please wait for few seconds";
                    $retArr = removeNull($retArr);
                    echo json_encode($retArr);
                    exit;
                }

                $customerCanGoExit = 0;
                $customerCanGoExitDatetime = strtotime(date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($in_time))));
                $customerCanGoExitCurrentDatetime = strtotime(date('Y-m-d H:i:s'));
                if ($customerCanGoExitDatetime >= $customerCanGoExitCurrentDatetime) {
                    $customerCanGoExit = 1;
                }
                // check check in and checkout time correct
                $isExtraChargeForPost = 0;
                $sql = "select from_date , to_date,TotalAmount, is_postpaid from tbl_paymenttransaction where TxnID='" . $this->txn_id . "'";
                $exe = mysqli_query($con, $sql);
                $num = mysqli_num_rows($exe);

                if ($num == 1 && $customerCanGoExit == 0) {
                    // not open gate before check in time
                    $today = date("Y-m-d H:i:s");
                    $payment_rec = mysqli_fetch_array($exe);
                    $this->from_date = $payment_rec['from_date'];
                    $this->to_date = $payment_rec['to_date'];
                    $this->TotalAmount = $payment_rec['TotalAmount'];
                    if (strtotime($payment_rec['to_date']) <= strtotime($today)) {
                        // check creditcard Details
                        $card_array = array();
                        $sql2 = "select *,RIGHT(AES_DECRYPT(Card_Number,'" . $this->enckey . "'),4) AS Card_No from tbl_creditcarddetails where Card_User_ID='" . $this->user_id . "' ORDER BY Card_Created ASC";
                        $exe2 = mysqli_query($con, $sql2);
                        $num2 = mysqli_num_rows($exe2);
                        if ($num2 > 0) {

                            while ($res2 = mysqli_fetch_array($exe2)) {
                                $card_array[] = array("card_id" => $res2['Card_ID'], "Card_Type" => $res2['Card_Type'], "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $res2['Card_Exp_Year'],
                                    "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                                    "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Card_Default" => $res2['Card_Default']);
                            }
                        } else {
                            ob_end_clean();
                            $retArr = array();
                            $retArr['status'] = 0;
                            $retArr['success'] = 0;
                            $retArr['error'] = 2;
                            $retArr['message'] = "Uh Oh! You don't have any saved payments";
                            $retArr = removeNull($retArr);
                            echo json_encode($retArr);
                            exit;
                        }
                        if (empty($payment_rec['is_postpaid'])) {
                            // calculate amount
                            $objParkMDA = new ParkingManagerDA();
                            $fromdate = $payment_rec['to_date'];
                            $todate = $today;
                            $P_ID = $this->garage_id;
                            $getParkingTypeByPidWithDetail = $objParkMDA->getParkingTypeByPidWithDetail($P_ID);
                            $fromdateWithOneHour = date('Y-m-d H:i:s', strtotime($fromdate . '+1hour'));
                            if (($getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'dh' || $getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'hd') && strtotime($todate) < strtotime($fromdateWithOneHour)) {
                                $hourlyP_ID = $getParkingTypeByPidWithDetail['parkingDetails']['hourly']['P_ID'];
                                $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $hourlyP_ID);
                            } else {
                                $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
                            }
                            //$objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
                            //$this->increaseSpacesByPid($payment_rec['from_date'], $payment_rec['to_date'], $P_ID);
                            //$this->UpdateParkingQntyWithOrderID($this->txn_id);
                            ob_end_clean();
                            $retArr = array();
                            $retArr['status'] = 0;
                            $retArr['success'] = 0;
                            $retArr['error'] = 3;
                            $retArr['message'] = "You're checking out later than scheduled.*";
                            $retArr['checkin'] = $payment_rec['from_date'];
                            $retArr['checkout'] = $payment_rec['to_date'];
                            $retArr['current'] = $today;
                            $retArr['amount'] = $objPayinfoDO['total']; //->TotalAmount;
                            $retArr['creditcard'] = $card_array;
                            $retArr = removeNull($retArr);
                            echo json_encode($retArr);
                            exit;
                        } else {
                            $isExtraChargeForPost = 1;
                        }
                    }
                    if (!empty($payment_rec['is_postpaid'])) {
                        // calculate amount
                        /*                         * *****REPEARTED CODE REMOVE IT LATER - UJASH***** */
                        $card_array = array();
                        $sql2 = "select *,RIGHT(AES_DECRYPT(Card_Number,'" . $this->enckey . "'),4) AS Card_No from tbl_creditcarddetails where Card_User_ID='" . $this->user_id . "' ORDER BY Card_Created ASC";
                        $exe2 = mysqli_query($con, $sql2);
                        $num2 = mysqli_num_rows($exe2);
                        if ($num2 > 0) {

                            while ($res2 = mysqli_fetch_array($exe2)) {
                                $card_array[] = array("card_id" => $res2['Card_ID'], "Card_Type" => $res2['Card_Type'], "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $res2['Card_Exp_Year'],
                                    "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                                    "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Card_Default" => $res2['Card_Default']);
                            }
                        } else {
                            ob_end_clean();
                            $retArr = array();
                            $retArr['status'] = 0;
                            $retArr['success'] = 0;
                            $retArr['error'] = 2;
                            $retArr['message'] = "Uh Oh! You don't have any saved payments";
                            $retArr = removeNull($retArr);
                            echo json_encode($retArr);
                            exit;
                        }
                        /*                         * ********** */
                        //$this->increaseSpacesByPid($payment_rec['from_date'], $payment_rec['to_date'], $this->garage_id);
                        //$this->UpdateParkingQntyWithOrderID($this->txn_id);
                        
                        ob_end_clean();
                        $retArr = array();
                        $retArr['status'] = 0;
                        $retArr['success'] = 0;
                        $retArr['error'] = 5;
                        $extraTotalForPaid = 0;
                        $TotalAmountFinalPost = 0;
                        /*if ($isExtraChargeForPost) {
                            $retArr['message'] = "Pay remaining Payment. Also, you're checking out later than scheduled.*";
                            $objParkMDA = new ParkingManagerDA();
                            $fromdate = $payment_rec['from_date'];
                            $todate = $today;
                            $P_ID = $this->garage_id;
                            $getParkingTypeByPidWithDetail = $objParkMDA->getParkingTypeByPidWithDetail($P_ID);
                            $fromdateWithOneHour = date('Y-m-d H:i:s', strtotime($fromdate . '+1hour'));
                            if (($getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'dh' || $getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'hd')) {
                                $hourlyP_ID = $getParkingTypeByPidWithDetail['parkingDetails']['hourly']['P_ID'];
                                $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $hourlyP_ID);
                            } else {
                                $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
                            }
                            //$objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
                            $TotalAmountFinalPost = $objPayinfoDO['total'];
                        } else {
                            $objParkMDA = new ParkingManagerDA();
                            $fromdate = $payment_rec['from_date'];
                            $todate = $today;
                            $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $this->garage_id);
                            $TotalAmountFinalPost = $objPayinfoDO['total'];
                            $retArr['message'] = "Pay remaining Payment.";
                        }*/
						$retArr['message'] = "Pay remaining Payment.";
						$objParkMDA = new ParkingManagerDA();
						$fromdate = $payment_rec['from_date'];
						$todate = $today;
						$P_ID = $this->garage_id;
						$getParkingTypeByPidWithDetail = $objParkMDA->getParkingTypeByPidWithDetail($P_ID);
						$fromdateWithOneHour = date('Y-m-d H:i:s', strtotime($fromdate . '+1hour'));
						if (($getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'dh' || $getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'hd')) {
							$hourlyP_ID = $getParkingTypeByPidWithDetail['parkingDetails']['hourly']['P_ID'];
							$objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $hourlyP_ID);
						} else {
							$objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
						}
						//$objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($fromdate, $todate, $P_ID);
						//$TotalAmountFinalPost = $objPayinfoDO['total'];
                        $TotalAmountFinalPost = $objPayinfoDO['total'];
                        $TotalTaxFinalPost = $objPayinfoDO['tax'];
                        $TotalOvernightFeeFinalPost = $objPayinfoDO['overnight_fee'];
                        $charges_details = $objPayinfoDO;
                        $this->updateCheckoutTime($this->txn_id, $TotalAmountFinalPost, $TotalTaxFinalPost, $TotalOvernightFeeFinalPost);
						
                        $retArr['checkin'] = $payment_rec['from_date'];
                        $retArr['checkout'] = $this->to_date;
                        $retArr['current'] = $today;
                        $retArr['amount'] = $TotalAmountFinalPost; //->TotalAmount;
                        $retArr['creditcard'] = $card_array;
                        $retArr['amountSummary'] = $charges_details;
                        $retArr = removeNull($retArr);
                        echo json_encode($retArr);
                        exit;
                    }
                }
                //die;
                $this->Updategatestatus($this->gate_id, $this->user_id);
                echo $this->fnAuthExit();
            } else {
                echo $this->fnAuthInvalid();
            }
        } else {
            if ($this->gatemode != 2) {
                // check check in and checkout time correct
                $sql = "select from_date , to_date,TotalAmount,Parking_ID, is_postpaid  from tbl_paymenttransaction where TxnID='" . $this->txn_id . "'";
                $exe = mysqli_query($con, $sql);
                $num = mysqli_num_rows($exe);
                $payment_rec = mysqli_fetch_array($exe);
                if ($num == 1) {
                    // not open gate before check in time
                    $today = date("Y-m-d H:i:s");
                    //$fromDate = strtotime(date('Y-m-d H:i:s', strtotime($payment_rec['from_date'])) . ' -2 Hours');
                    $fromDate = date('Y-m-d H:i:s', strtotime($payment_rec['from_date']));
                    $fromLessDate = date('Y-m-d H:i:s', strtotime($fromDate));
                    if ((strtotime($fromLessDate) >= strtotime($today)) >= strtotime($fromDate)) {
                        ob_end_clean();
                        $retArr = array();
                        $retArr['status'] = 1;
                        $retArr['success'] = 1;
                        $retArr['error'] = 1;
                        $retArr['message'] = "You're checking in earlier than scheduled.*";
                        $retArr['checkin'] = $payment_rec['from_date'];
                        $retArr['checkout'] = $payment_rec['to_date'];
                        $retArr['current'] = $today;
                        $retArr = removeNull($retArr);
                        $this->Updategatestatus($this->gate_id, $this->user_id);
						echo $this->fnAuthEnterNew($retArr);
						//echo json_encode($retArr);
						exit;
                    }
                    if (strtotime($payment_rec['to_date']) <= strtotime($today)) {
                        ob_end_clean();
                        $retArr = array();
                        $retArr['status'] = 0;
                        $retArr['success'] = 0;
                        $retArr['error'] = 1;
                        $retArr['message'] = "You've Missed your scheduled.*";
                        $retArr['checkin'] = $payment_rec['from_date'];
                        $retArr['checkout'] = $payment_rec['to_date'];
                        $retArr['current'] = $today;
                        $retArr = removeNull($retArr);
                        echo json_encode($retArr);
                        exit;
                    }
                }
				/*if (!empty($payment_rec['is_postpaid'])) {
                    $parkingidForSpaces = $payment_rec['Parking_ID'];
                    $this->descreaseSpacesByPid($payment_rec['from_date'], $payment_rec['to_date'], $parkingidForSpaces);
					//$this->UpdateParkingQntyWithOrderID($this->txn_id);
                }*/
                $this->Updategatestatus($this->gate_id, $this->user_id);
                echo $this->fnAuthEnter();
            } else {
                echo $this->fnAuthInvalid();
            }
        }
    }

    function Updategatestatus($gateID, $UserID) {
        GLOBAL $con;
        $objTranDA = new TransactionManagerDA();
        //updating gate status
        $objTranDA->UpdateGateEntryStatus($gateID, $UserID);
    }

    /**
     * Function to check whether user entry is valid and put an entry in transaction table
     * gate_id, user_id, password, garage_id
     * If username and password not entered - return 0
     * If error in DB transaction return -1
     * If not a valid user then return -2
     * if valid entry then return 1
     * Entry mode - entry will be 1 and exit will be 2
     * STEP 1 - Check user is valid with their username and password
     * STEP 2 - Check whether user has a valid gate transaction entry with out time is null
     * STEP 3 - Insert into transaction table and return the id
     */
    function fnAuthEnter() {
        GLOBAL $con;
        $flag = 0;
        $msg = "";
        $trans_id = 0;
        $Creditcardmsg = "";
        if ($this->user_flag == 1) {

            $entry_mode = 1;
            $sql_trans = "select transaction_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '" . $this->garage_id . "' and user_id = '" . $this->user_id . "' and entry_mode = 1 and txn_id='" . $this->txn_id . "'";
            $trans_result = mysqli_query($con, $sql_trans);
            if ($trans_result === false) {
                $flag = -1;
                $msg = $this->dbMsg;
            } else {
                $trans_num_rows = mysqli_num_rows($trans_result); // die;
                if ($trans_num_rows > 0) {
                    $flag = -3; //Already user has entered the garage and not checked out
                    $msg = "User entry already exists for this garage";
                } else {

                    $objTran = new ParkingManagerDA();
                    //Check parking availability
                    $IsAvailability = $objTran->GetAvailability($this->garage_id);

                    //Start Adding Gate transaction CODE:GOPALANMANI-FEB2005
                    $objUtil = new UtilManagerUA();
                    $objParkGateTran = new GateParkingTransactionManagerDO();
                    $objParkGateTran->GateID = $this->gate_id;
                    $objParkGateTran->GarageID = $this->garage_id;
                    $objParkGateTran->UserID = $this->user_id;
                    $objParkGateTran->txn_id = $this->txn_id;
                    $objParkGateTran->EntryMode = 1;
                    $objParkGateTran->UserDetails = $this->user_details;
                    $objParkGateTran->IPAddress = $objUtil->get_client_ip();
                    $objParkGateTran->HTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];

                    //Start featured booking validation
                    //$objFBookDO = new FeaturedBookingInfoManagerDO();
                    //$objFBookDO = $objParkMDA->GetFeaturedBooking($user_details->id,fields_val['garage_id']);
                    $objParkGateTran->BookingID = 0; //$objFBookDO->BookingID;
                    //End featured booking validation

                    if ($IsAvailability == 1) {
                        $objParkGateTran->IsValidEnty = 1;
                    } else {
                        $objParkGateTran->IsValidEnty = 0;
                    }


                    $tranval = $objTran->AddGateParkingTransaction($objParkGateTran);
                    //End Adding Gate transaction CODE:GOPALANMANI-FEB2005

                    if ($IsAvailability == 1) {
                        $flag = 1;
                        $gate_status = 'open';
                        $msg = $tranval['msg'];
                        $trans_id = $tranval['trans_id'];
                        $IsAvailCreditCard = $objTran->IsAvailableCreditCard($objParkGateTran->UserID);
                        if ($IsAvailCreditCard == 0)
                            $Creditcardmsg = 'Add valid Credit card before exit';
                    }
                    else {
                        $flag = -4;
                        $gate_status = 'close';
                        $msg = "Space is not available";
                        $trans_id = 0;
                    }
                }
            }
        } else {
            $msg = $res['msg'];
        }
        ob_end_clean();
        $retArr = array();
        $status = $flag > 0 ? 1 : 0;
        $retArr['status'] = $status;
        $retArr['success'] = $flag;
        $retArr['error'] = $msg;
        $retArr['creditcard'] = $Creditcardmsg;
        $retArr['transaction_id'] = $trans_id;
        $retArr['gate_status'] = $gate_status;
        $retArr['gate_mode'] = $this->gatemode;
        $retArr = removeNull($retArr);
        return json_encode($retArr);
    }

	function fnAuthEnterNew($retArr) {
		GLOBAL $con;
        $flag = 0;
        $msg = "";
        $trans_id = 0;
        $Creditcardmsg = "";
        if ($this->user_flag == 1) {

            $entry_mode = 1;
            $sql_trans = "select transaction_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '" . $this->garage_id . "' and user_id = '" . $this->user_id . "' and entry_mode = 1 and txn_id='" . $this->txn_id . "'";
            $trans_result = mysqli_query($con, $sql_trans);
            if ($trans_result === false) {
                $flag = -1;
                $msg = $this->dbMsg;
            } else {
                $trans_num_rows = mysqli_num_rows($trans_result); // die;
                if ($trans_num_rows > 0) {
                    $flag = -3; //Already user has entered the garage and not checked out
                    $msg = "User entry already exists for this garage";
                } else {

                    $objTran = new ParkingManagerDA();
                    //Check parking availability
                    $IsAvailability = $objTran->GetAvailability($this->garage_id);

                    //Start Adding Gate transaction CODE:GOPALANMANI-FEB2005
                    $objUtil = new UtilManagerUA();
                    $objParkGateTran = new GateParkingTransactionManagerDO();
                    $objParkGateTran->GateID = $this->gate_id;
                    $objParkGateTran->GarageID = $this->garage_id;
                    $objParkGateTran->UserID = $this->user_id;
                    $objParkGateTran->txn_id = $this->txn_id;
                    $objParkGateTran->EntryMode = 1;
                    $objParkGateTran->UserDetails = $this->user_details;
                    $objParkGateTran->IPAddress = $objUtil->get_client_ip();
                    $objParkGateTran->HTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];

                    //Start featured booking validation
                    //$objFBookDO = new FeaturedBookingInfoManagerDO();
                    //$objFBookDO = $objParkMDA->GetFeaturedBooking($user_details->id,fields_val['garage_id']);
                    $objParkGateTran->BookingID = 0; //$objFBookDO->BookingID;
                    //End featured booking validation

                    if ($IsAvailability == 1) {
                        $objParkGateTran->IsValidEnty = 1;
                    } else {
                        $objParkGateTran->IsValidEnty = 0;
                    }


                    $tranval = $objTran->AddGateParkingTransaction($objParkGateTran);
                    //End Adding Gate transaction CODE:GOPALANMANI-FEB2005

                    if ($IsAvailability == 1) {
                        $flag = 1;
                        $gate_status = 'open';
                        $msg = $tranval['msg'];
                        $trans_id = $tranval['trans_id'];
                        $IsAvailCreditCard = $objTran->IsAvailableCreditCard($objParkGateTran->UserID);
                        if ($IsAvailCreditCard == 0)
                            $Creditcardmsg = 'Add valid Credit card before exit';
                    }
                    else {
                        $flag = -4;
                        $gate_status = 'close';
                        $msg = "Space is not available";
                        $trans_id = 0;
                    }
                }
            }
        } else {
            $msg = $res['msg'];
        }
        ob_end_clean();
        if(empty($retArr)){
			$retArr = array();
			$status = $flag > 0 ? 1 : 0;
			$retArr['status'] = $status;
			$retArr['success'] = $flag;
			$retArr['error'] = $msg;
			$retArr['creditcard'] = $Creditcardmsg;
			$retArr['transaction_id'] = $trans_id;
			$retArr['gate_status'] = $gate_status;
			$retArr['gate_mode'] = $this->gatemode;
			$retArr = removeNull($retArr);
			return json_encode($retArr);
		} else {
			$retArr['creditcard'] = $Creditcardmsg;
			$retArr['transaction_id'] = $trans_id;
			$retArr['gate_status'] = $gate_status;
			$retArr['gate_mode'] = $this->gatemode;
			$retArr = removeNull($retArr);
			return json_encode($retArr);
		}
	}
	
    function fnAuthExit() {
        GLOBAL $con;
        $flag = 0;
        $msg = "";
        $trans_id = 0;

        // $objTran = new ParkingManagerDA();
        $Creditcardmsg = "";
        $gate_status = 'close';
        if ($this->user_flag == 1) {
            $entry_mode = 2;
            $sql_trans = "select transaction_id,in_time, garage_id from tbl_gate_parking_transaction where IsValidEnty=1 and garage_id = '" . $this->garage_id . "' and user_id = '" . $this->user_id . "' and entry_mode = 1 and txn_id=" . $this->txn_id;
            //echo $sql_trans;
            $trans_result = mysqli_query($con, $sql_trans);
            if ($trans_result === false) {
                $flag = -1;
                $msg = $this->dbMsg;
            } else {
                $trans_num_rows = mysqli_num_rows($trans_result);
                if ($trans_num_rows == 0) {
                    $flag = -3; //Already user has entered the garage and not checked out
                    $msg = "User entry doesn't exists for this garage";
                } else {
                    if ($trans_num_rows == 1) {
                        $IsValidPayment = true;
                        $trans_details = mysqli_fetch_object($trans_result);
                        $trans_id = $transaction_id = $trans_details->transaction_id;
                         $objParkMDA = new ParkingManagerDA();
                          // $objPayinfoDO = new PaymentInfoManagerDO();
                          $objPayinfoDO = $objParkMDA->GetTransactionAmount($trans_id);
						  $from_time = $objPayinfoDO->FromDate;
                          $to_time = $objPayinfoDO->ToDate;
                        /*  
                          $mins=round(abs($to_time - $from_time) / 60,2);

                          if($mins<=1)  {
                          $IsValidPayment =true;
                          $objPayinfoDO->TotalAmount=0;
                          } else {
                          // ACTIVATE BELOW LIVE
                          $IsAvailCreditCard=$objParkMDA->IsAvailableCreditCard($this->user_id );
                          $IsAvailCreditCard=1; //for added testing only
                          if($IsAvailCreditCard==0) {
                          $Creditcardmsg='Add Credit Card to proceed to exit';
                          } else {
                          //$IsValidPayment=$objParkMDA->ValidPayment($objPayinfoDO);
                          // Remove in LIVE
                          $sql2="select *,RIGHT(AES_DECRYPT(Card_Number,'".$this->enckey."'),4) AS Card_No from tbl_creditcarddetails where Card_User_ID=".$this->user_id." ORDER BY Card_Created ASC";
                          $exe2=mysqli_query($con,$sql2);
                          $num2=mysqli_num_rows($exe2);
                          if($num2>0)
                          {
                          while($res2=mysqli_fetch_array($exe2)){
                          $card_array[]=array("card_id"=>$res2['Card_ID'],"Card_Type"=>$res2['Card_Type'],"CARD_NO"=>base64_encode($res2['Card_No']),"Card_Exp_Year"=>$res2['Card_Exp_Year'],
                          "Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
                          "Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip'],"Card_Default"=>$res2['Card_Default']);
                          }
                          }
                          else{
                          $card_array=array();
                          }
                          ob_end_clean();
                          $retArr =array();
                          $retArr['status']=0;
                          $retArr['success']=0;
                          $retArr['error']=1;
                          $retArr['message']="You have exceeded the checkout time, so you have to pay ".'$'.$objPayinfoDO->TotalAmount.".";
                          $retArr['message']="You're checking out later than scheduled.*";
                          $retArr['checkin']='';
                          $retArr['checkout']='';
                          $retArr['amount']='$'.$objPayinfoDO->TotalAmount;
                          $retArr['creditcard']=$card_array;
                          return json_encode($retArr);
                          $IsValidPayment =true;
                          }
                          } */

                        if ($IsValidPayment == true) {
                            // ACTIVATE BELOW LIVE
                            //$objParkMDA->AddTransaction($objPayinfoDO);
                            $gate_status = 'open';
                            $flag = 1;
                            $sql_update_trans = "update tbl_gate_parking_transaction set gate_id='" . $this->gate_id . "', out_time=now(), last_updated=now(), entry_mode='" . $entry_mode . "', ip_address='" . $_SERVER["REMOTE_ADDR"] . "', user_agent='" . $_SERVER["HTTP_USER_AGENT"] . "', total_time=null where transaction_id='" . $transaction_id . "'"; //, amount='" . $objPayinfoDO->TotalAmount . "'
                            $trans_update_result = mysqli_query($con, $sql_update_trans);
							$sql_parking_time = "select from_date, to_date from tbl_paymenttransaction where TxnID='" . $this->txn_id . "'";
                            $exe_parking_time = mysqli_query($con, $sql_parking_time);
                            $payment_rec_parking_time = mysqli_fetch_array($exe_parking_time);
                            $this->increaseSpacesByPid($payment_rec_parking_time['from_date'], $payment_rec_parking_time['to_date'], $trans_details->garage_id);
                        } else {
                            $flag = -5;
                            $msg = "payment failed.";
                            $gate_status = 'close';
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
        $status = $flag > 0 ? 1 : 0;
        ob_end_clean();
        $retArr = array();
        $retArr['status'] = $status;
        $retArr['success'] = $flag;
        $retArr['error'] = $msg;
        $retArr['transaction_id'] = $transaction_id;
        $retArr['creditcard'] = $Creditcardmsg;
        $retArr['gate_status'] = $gate_status;
        $retArr['from_date'] = $this->from_date;
        $retArr['to_date'] = $this->to_date;
        $retArr['amount'] = $this->TotalAmount;
        $retArr['gate_mode'] = $this->gatemode;
        $retArr = removeNull($retArr);
        return json_encode($retArr);
    }

    function fnCheckLogin($username, $password) {
        GLOBAL $con;
        $arr = array();
        $flag = 0;
        $msg = "";
        $user_details = array();
        if ($username != "" && $password != "") {
            //$sql="select id, firstname, lastname from tbl_registeration WHERE email_add ='".$username."' AND encrypt_password=AES_ENCRYPT('".$password."','".$this->enckey."') AND status=1";
            $sql = "select id, firstname, lastname from tbl_registeration WHERE email_add ='" . $username . "' AND status=1";
            //echo $sql;
            $result = mysqli_query($con, $sql);
            if ($result === false) {
                $flag = -1;
                $msg = $this->dbMsg;
            } else {
                $num_rows = mysqli_num_rows($result);
                if ($num_rows > 0) {

                    $flag = 1;
                    $user_details = mysqli_fetch_object($result);
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

    function fnGetUserDetails($user_id) {
        GLOBAL $con;
        $arr = array();
        $flag = 0;
        $msg = "";
        $user_details = array();
        if ($user_id != "") {
            //$sql="select id, firstname, lastname from tbl_registeration WHERE email_add ='".$username."' AND encrypt_password=AES_ENCRYPT('".$password."','".$this->enckey."') AND status=1";
            $sql = "select id,firstname,lastname from tbl_registeration WHERE id ='" . $user_id . "' AND status=1";
            //echo $sql;
            $result = mysqli_query($con, $sql);
            if ($result === false) {
                $flag = -1;
                $msg = $this->dbMsg;
            } else {
                $num_rows = mysqli_num_rows($result);
                if ($num_rows > 0) {

                    $flag = 1;
                    $user_details = mysqli_fetch_object($result);
                } else {
                    $flag = -2;
                    $msg = "User does not exists";
                }
            }
        } else {
            $msg = "Please enter user_id";
        }
        $arr['flag'] = $flag;
        $arr['msg'] = $msg;
        $arr['user_details'] = $user_details;
        return $arr;
    }

    function fnAuthInvalid() {
        GLOBAL $con;
        $flag = -6;
        $msg = "invalid gate entry";
        $trans_id = 0;

        $Creditcardmsg = "";
        $gate_status = 'close';

        ob_end_clean();
        $retArr = array();
        $retArr['success'] = $flag;
        $retArr['error'] = $msg;
        $retArr = removeNull($retArr);
        return json_encode($retArr);
    }

    function fnAuthInvalidParam() {
        $flag = -7;
        $msg = "invalid parameters";
        $trans_id = 0;

        $Creditcardmsg = "";
        $gate_status = 'close';

        ob_end_clean();
        $retArr = array();
        $retArr['success'] = $flag;
        $retArr['error'] = $msg;
        $retArr = removeNull($retArr);
        return json_encode($retArr);
    }

    function __destruct() {
        unset($this);
    }

    function increaseSpacesByPid($fromdateAvailability, $todateAvailability, $parkingidForSpaces) {
        GLOBAL $con;
        if (!empty($fromdateAvailability) && !empty($todateAvailability) && !empty($parkingidForSpaces)) {
            $fromdateAvailability = date('Y-m-d', strtotime($fromdateAvailability));
            $todateAvailability = date('Y-m-d', strtotime($todateAvailability));
            if (!empty($parkingidForSpaces)) {
                if (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                    while (strtotime($fromdateAvailability) < strtotime($todateAvailability)) {
                        echo $availability_upsql = "UPDATE tbl_parkingweekdayavailability set PA_Updated = '".date('Y-m-d H:i:s')."',  occupiedSpaces = IF(occupiedSpaces <= PA_No_Spaces AND 0 < occupiedSpaces, (occupiedSpaces)-1, occupiedSpaces) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
                        @mysqli_query($con, $availability_upsql);
                        $fromdateAvailability = date("Y-m-d", strtotime("+1 day", strtotime($fromdateAvailability)));
                    }
                } else if (strtotime($fromdateAvailability) == strtotime($todateAvailability)) {
                    $availability_upsql = "UPDATE tbl_parkingweekdayavailability set PA_Updated = '".date('Y-m-d H:i:s')."', occupiedSpaces =  IF(occupiedSpaces <= PA_No_Spaces AND 0 < occupiedSpaces, (occupiedSpaces)-1, occupiedSpaces) WHERE P_ID = $parkingidForSpaces AND P_fromDate = '" . $fromdateAvailability . "'";
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
    function updateCheckoutTime($txtOrderID, $TotalAmountFinalPost, $TotalTaxFinalPost, $TotalOvernightFeeFinalPost) {
        GLOBAL $con;
        $this->to_date = date('Y-m-d H:i:s');
        $sql_update_trans = "update tbl_paymenttransaction set to_date='" . $this->to_date . "', TotalAmount = '".$TotalAmountFinalPost."', charges = '".$TotalTaxFinalPost."', Overnight_Fee = '".$TotalOvernightFeeFinalPost."'  where TxnID='" . $txtOrderID . "'";
        mysqli_query($con, $sql_update_trans);
    }

}

$auth = new authService();
