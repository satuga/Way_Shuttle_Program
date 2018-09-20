<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");
include("DataAccessLayer/ParkingManagerDA.php");

class parkingService {

    var $dbMsg;
    // Major = gate_id
    // Minor = gatemode
    private $parking_id, $user_id;

    function __construct() {
        $this->dbMsg = "Something went wrong. Please try again.";
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->parking_id = isset($this->fields_val['parking_id']) ? $this->fields_val['parking_id'] : '';
        $this->user_id = isset($this->fields_val['user_id']) ? $this->fields_val['user_id'] : '';
        if ($this->parking_id == "" || $this->user_id == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetParkingDetails($this->parking_id, $this->user_id);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetParkingDetails($parking_id, $user_id) {
        GLOBAL $con;
        $txn_id = $pre_gate_status = $post_gate_status = 0;
        $postPayResultArray = array();

        /* Credit Card - Start */
        $card_array = $this->getUserCradInfo($user_id);
        /* Credit Card - End */

        /* get Pre Order details - Start */
        $prePayResultArray = $this->getPreOrPostOrder($parking_id, $user_id, 0);
        /* get Pre Order details - End */
        /* get Post Order details - Start */
        $postPayResultArray = $this->getPreOrPostOrder($parking_id, $user_id, 1);
        /* get Post Order details - End */
        if (!empty($postPayResultArray['TxnID']) && !empty($prePayResultArray['TxnID'])) {
            if (strtotime($prePayResultArray['fromdate']) > strtotime($postPayResultArray['fromdate'])) {
                if (!empty($postPayResultArray['TxnID'])) {
                    $txn_id = $postPayResultArray['TxnID'];
                    $post_gate_status = 1;
                }
                $owner_id = $postPayResultArray['owner_id'];
                unset($postPayResultArray['owner_id']);
            } else if (strtotime($postPayResultArray['fromdate']) > strtotime($prePayResultArray['fromdate'])) {
                if (!empty($prePayResultArray['TxnID'])) {
                    $pre_gate_status = 1;
                    $txn_id = $prePayResultArray['TxnID'];
                }
                $owner_id = $prePayResultArray['owner_id'];
                unset($prePayResultArray['owner_id']);
            }
        } else {
            if (!empty($prePayResultArray)) {
                if (!empty($prePayResultArray['TxnID'])) {
                    $pre_gate_status = 1;
                    $txn_id = $prePayResultArray['TxnID'];
                }
                $owner_id = $prePayResultArray['owner_id'];
                unset($prePayResultArray['owner_id']);
            }
            if (empty($txn_id)) {
                if (!empty($postPayResultArray)) {
                    if (!empty($postPayResultArray['TxnID'])) {
                        $txn_id = $postPayResultArray['TxnID'];
                        $post_gate_status = 1;
                    }
                    $owner_id = $postPayResultArray['owner_id'];
                    unset($postPayResultArray['owner_id']);
                }
            }
        }
        ob_end_clean();
        $info = array();
        $info['parking_id'] = $parking_id;
        $info['pre_payment_status'] = $pre_gate_status;
        $info['pre_payment_order_detail'] = $prePayResultArray;
        $info['post_payment_status'] = $post_gate_status;
        $info['post_payment_order_detail'] = $postPayResultArray;
        $info['txn_id'] = $txn_id;
        $info['owner_id'] = $owner_id;

        /* get available spaces - Start */
        $available_spaces = $this->getAvailableSpaces($parking_id);
        /* get available spaces - End */
        $info['available_spaces'] = $available_spaces;

        /* get user email - Start */
        $available_spaces = $this->getUserEmail($user_id);
        /* get user email - End */
        $info['email'] = $available_spaces;


        $arrayName = '';
        $checkForBeacon = 1;
        if (!empty($pre_gate_status)) {
            $aRow = $prePayResultArray;
            $checkForBeacon = 0;
        } else if (!empty($post_gate_status)) {
            $aRow = $postPayResultArray;
            $checkForBeacon = 0;
        }
        if(empty($aRow['beacons']) && $checkForBeacon == 1){
            $beaconsDetail = $this->getBeaconDetail($parking_id, 1);
            $info['beacons'] = $beaconsDetail;
        } else {
            $info['beacons'] = $aRow['beacons'];
        }
        $info['Card_details'] = $card_array;
        $getParkingTypeByPid = $this->getParkingTypeByPid($parking_id);
        $info['parkingTypeStatus'] = $getParkingTypeByPid['parkingTypeStatus'];
        $info['hourlyParkingID'] = $getParkingTypeByPid['hourlyParkingID'];
        $info['amountDetail'] = array();
        if(!empty($aRow['fromdate'])){
            if(!empty($post_gate_status)){
                $currentDate = date('Y-m-d H:i:s');
                $info['currentDate'] = $currentDate;
                
                $customerExtraMinute = strtotime(date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($aRow['fromdate']))));
                if ($customerExtraMinute >= strtotime($currentDate)) {
                    //$currentDate = $aRow['fromdate'];
                    $blankArrayForAmount = array("price" => "0", "subtotal" => 0,
                                                "total" => 0, "tax" => 0,
                                                "overnight_fee" => 0, "days" => 0,
                                                "fdate" => strtotime($aRow['fromdate']),
                                                "tdate" => strtotime($currentDate),
                                                "min_reservation" => 0);
                    $info['amountDetail'] = $blankArrayForAmount;
                } else {
                    $objParkMDA = new ParkingManagerDA();
                    if($getParkingTypeByPid['parkingTypeStatus']!= 'd'){
                        $garageIdForAmount = $getParkingTypeByPid['hourlyParkingID'];
                    } else {
                        $garageIdForAmount = $aRow['garage_id'];
                    }
                    $objPayinfoDO = $objParkMDA->getParkingPriceAndTotal($aRow['fromdate'], $currentDate, $garageIdForAmount);
                    $info['amountDetail'] = $objPayinfoDO;
                }
            }
        }

        $content = array("status" => 1, "data" => $info);
        echo json_encode($content);
        exit;
    }

    function getAvailableSpaces($parking_id) {
        GLOBAL $con;
        $available_spaces = 0;
        $mainDetail = array();
        $sqlPayQuery = "SELECT * FROM `tbl_parkingweekdayavailability` where P_ID = '" . $parking_id . "' AND P_fromDate = '" . date('Y-m-d') . "' ORDER BY `PA_ID` DESC";
        $sqlPayResult = mysqli_query($con, $sqlPayQuery);
        $countPayResult = mysqli_num_rows($sqlPayResult);
        if ($countPayResult > 0) {
            $mainDetail = mysqli_fetch_assoc($sqlPayResult);
            $available_spaces = $mainDetail['PA_No_Spaces'] - $mainDetail['occupiedSpaces'];
        }
        return $available_spaces;
    }

    function getUserEmail($user_id) {
        GLOBAL $con;
        $email = '';
        $mainDetail = array();
        $sqlPayQuery = "SELECT email_add FROM `tbl_registeration` where id = '" . $user_id . "'";
        $sqlPayResult = mysqli_query($con, $sqlPayQuery);
        $countPayResult = mysqli_num_rows($sqlPayResult);
        if ($countPayResult > 0) {
            $mainDetail = mysqli_fetch_assoc($sqlPayResult);
            $email = $mainDetail['email_add'];
        }
        return $email;
    }

    function getBeaconDetail($garage_id, $gatemode = 1, $parking_ids = '') {
        GLOBAL $con;
        $beacon = array();
        if (!empty($parking_ids)) {
            $dev_sql = "SELECT * from tbl_smartgate where SGT_GarageID IN (" . $parking_ids . ") and SGT_GateType='" . $gatemode . "'";
        } else {
            $dev_sql = "SELECT * from tbl_smartgate where SGT_GarageID = '" . $garage_id . "' and SGT_GateType='" . $gatemode . "'";
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
            $result['BLE_Device_UUID'] = $results['BLE_Device_UUID'];
            $beacon[] = $result;
        }
        return $beacon;
    }

    function getParkingTypeByPidWithDetail($P_ID, $statusQuery = '') {
        GLOBAL $con;
        $sql = "SELECT P_ID, P_Location, P_Pricingtype  FROM `tbl_parking` tp where " . $statusQuery . " tp.P_Location = (select tp1.P_Location from `tbl_parking` tp1 where tp1.P_ID = '" . $P_ID . "') AND (tp.P_Pricingtype = 'hourly' OR tp.P_Pricingtype = 'daily') ORDER BY tp.`P_ID`";
        //echo $sql;
        $result = mysqli_query($con, $sql);
        $parkingTypeStatus = '';
        $mainDetail = array();
        $detailArray = array();
        $countParkingResult = mysqli_num_rows($result);
        if ($countParkingResult > 0) {
            while ($res = mysqli_fetch_assoc($result)) {
                if ($res['P_Pricingtype'] == 'daily' || $res['P_Pricingtype'] == 'hourly') {
                    if ($res['P_Pricingtype'] == 'hourly') {
                        $parkingTypeStatus['h'] = 'h';
                        $detailArray[] = $res['P_ID'];
                    } else if ($res['P_Pricingtype'] == 'daily') {
                        $parkingTypeStatus['d'] = 'd';
                        $detailArray[] = $res['P_ID'];
                    }
                }
            }
            asort($parkingTypeStatus);
            $parkingTypeStatus = implode('', $parkingTypeStatus);
        }
        $parkingTypeStatus = preg_replace("/(.)\\1+/", "$1", $parkingTypeStatus);
        $mainDetail['parkingTypeStatus'] = $parkingTypeStatus;
        $mainDetail['parkingDetails'] = $detailArray;
        return $mainDetail;
    }

    function getPreOrPostOrder($parking_id, $user_id, $is_postpaid = 0) { // 0 = prepay 1 = post pay
        GLOBAL $con;
        $mainDetail = array();
        $mainDetail['beacons'] = array();
        $txn_id = 0;
//        $sqlPayQuery = "SELECT tgpt.transaction_id, tgpt.gate_id, tgpt.garage_id, tgpt.user_id, tgpt.in_time, tgpt.out_time, tgpt.entry_mode, tgpt.amount, tgpt.IsValidEnty, tpt.TxnID FROM `tbl_paymenttransaction` AS tpt LEFT JOIN tbl_gate_parking_transaction AS tgpt ON tgpt.txn_id = tpt.TxnID WHERE tpt.`Parking_ID` = '" . $parking_id . "' AND tpt.`UsrID` = '" . $user_id . "' AND tgpt.entry_mode = 1 AND tpt.is_postpaid = " . $is_postpaid . " Order by tgpt.last_updated DESC";
//        $sqlPayResult = mysqli_query($con, $sqlPayQuery);
//        $countPayResult = mysqli_num_rows($sqlPayResult);

        $sqlParkingQuery = "SELECT * FROM `tbl_parking` WHERE P_ID = '" . $parking_id . "'";
        $sqlParkingResult = mysqli_query($con, $sqlParkingQuery);
        $countParkingResult = mysqli_num_rows($sqlParkingResult);

        if ($countParkingResult > 0) {
            $parkingDetail = mysqli_fetch_assoc($sqlParkingResult);
            $getParkingTypeByPidWithDetail = $this->getParkingTypeByPidWithDetail($parking_id);
            $parking_ids[] = $parking_id;
            if (($getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'dh' || $getParkingTypeByPidWithDetail['parkingTypeStatus'] == 'hd')) {
                foreach ($getParkingTypeByPidWithDetail['parkingDetails'] as $parkingDetails) {
                    $parking_ids[] = $parkingDetails;
                }
            }
            $parking_ids = implode(',', $parking_ids);
            //$sqlTranQuery = "SELECT * FROM `tbl_paymenttransaction` WHERE UsrID = '".$user_id."' AND Parking_ID = '".$parking_id."' AND is_postpaid = '" . $is_postpaid . "' ORDER BY `TxnID` DESC";
            //$sqlTranQuery = "SELECT * FROM `tbl_paymenttransaction` WHERE UsrID = '" . $user_id . "' AND Parking_ID = '" . $parking_id . "' AND is_postpaid = '" . $is_postpaid . "' AND to_date >= NOW() ORDER BY `TxnID` DESC";
            if ($is_postpaid == 1) {
                $sqlTranQuery = "SELECT tpt.*, tgpt.garage_id FROM `tbl_paymenttransaction` tpt INNER JOIN tbl_gate_parking_transaction tgpt ON tgpt.txn_id = tpt.TxnID WHERE tpt.UsrID = '" . $user_id . "' AND tpt.Parking_ID IN (" . $parking_ids . ") AND tpt.is_postpaid = '" . $is_postpaid . "' AND tgpt.entry_mode = 1 AND OrderStatus!='Cancelled' ORDER BY tpt.from_date,tpt.`TxnID` DESC";
                $sqlTranResult = mysqli_query($con, $sqlTranQuery);
                $countTranResult = mysqli_num_rows($sqlTranResult);
            } else {
                //$sqlTranQuery = "SELECT tpt.* FROM `tbl_paymenttransaction` tpt INNER JOIN tbl_gate_parking_transaction tgpt ON tgpt.txn_id = tpt.TxnID WHERE tpt.UsrID = '" . $user_id . "' AND tpt.Parking_ID IN (" . $parking_ids . ") AND tpt.is_postpaid = '" . $is_postpaid . "' AND tgpt.entry_mode = 1 ORDER BY tpt.from_date, tpt.`TxnID` DESC";
                $sqlTranQuery = "SELECT tpt1.* FROM `tbl_paymenttransaction` tpt1 WHERE tpt1.UsrID = '" . $user_id . "' AND OrderStatus!='Cancelled' AND tpt1.Parking_ID IN (" . $parking_ids . ") AND tpt1.is_postpaid = '" . $is_postpaid . "' AND tpt1.to_date >= NOW() AND tpt1.TxnID NOT IN (SELECT tpt.TxnID FROM `tbl_paymenttransaction` tpt INNER JOIN tbl_gate_parking_transaction tgpt ON tgpt.txn_id = tpt.TxnID WHERE tpt.UsrID = '" . $user_id . "' AND tpt.Parking_ID IN (" . $parking_ids . ") AND tpt.is_postpaid = '" . $is_postpaid . "' AND tgpt.entry_mode = 2 AND tpt.to_date >= NOW()  ORDER BY  tpt.from_date, tpt.`TxnID` DESC) ORDER BY  tpt1.from_date, tpt1.`TxnID` DESC";
                $sqlTranResult = mysqli_query($con, $sqlTranQuery);
                $countTranResult = mysqli_num_rows($sqlTranResult);
                if (empty($countTranResult)) {
                    $sqlTranQuery = "SELECT * FROM `tbl_paymenttransaction` WHERE UsrID = '" . $user_id . "' AND Parking_ID IN (" . $parking_ids . ") AND is_postpaid = '" . $is_postpaid . "' AND to_date >= NOW() AND OrderStatus!='Cancelled' ORDER BY from_date, `TxnID` DESC";
                    $sqlTranResult = mysqli_query($con, $sqlTranQuery);
                    $countTranResult = mysqli_num_rows($sqlTranResult);
                }
            }
            $amountPaid = 0;
            $amountPaidWithEntry = 0;
            $exitGate = 0;
            $fromDate = 0;
            $toDate = 0;
            $fromLessDate = 0;
            if ($countTranResult > 0) {

                $tranDetail = mysqli_fetch_assoc($sqlTranResult);
                $txn_id = $tranDetail['TxnID'];
                $garage_id = !empty($tranDetail['garage_id'])?$tranDetail['garage_id']:'';
                $fromDate = $tranDetail['from_date'];
                $toDate = $tranDetail['to_date'];
                $parking_id = $tranDetail['Parking_ID'];
                $dev_sql = "SELECT transaction_id FROM tbl_gate_parking_transaction WHERE garage_id IN (" . $parking_ids . ") AND user_id='" . $user_id . "' AND txn_id='" . $txn_id . "' AND entry_mode=1 order by in_time desc";
                $exe = mysqli_query($con, $dev_sql);
                $num = mysqli_num_rows($exe);
                if (!empty($num)) {
                    $rec = mysqli_fetch_array($exe);
                    $gatemode = $rec['entry_mode'] == 1 ? 1 : 2; //die;
                    $mainDetail['beacons'] = $this->getBeaconDetail($parking_id, $gatemode, $parking_ids);
                    $amountPaidWithEntry = 1;
                }

                $dev_sql = "SELECT transaction_id FROM tbl_gate_parking_transaction WHERE garage_id IN (" . $parking_ids . ") AND user_id='" . $user_id . "' AND txn_id='" . $txn_id . "' AND entry_mode=2 order by in_time desc";
                $exe = mysqli_query($con, $dev_sql);
                $num = mysqli_num_rows($exe);
                if (!empty($num)) {
                    $rec = mysqli_fetch_array($exe);
                    $amountPaidWithEntry = 1;
                    $exitGate = 1;
                }
                if ($is_postpaid == 1) {
                    $amountPaid = 0;
                } else {
                    $amountPaid = 1;
                }
            }
            if (($amountPaidWithEntry != 1 || $exitGate != 1) && $countTranResult > 0) {
                $mainDetail['TxnID'] = $txn_id;
                $fromLessDate = date('Y-m-d H:i:s', strtotime($fromDate . '-1 days'));
                $mainDetail['fromdateless'] = $fromLessDate;
                $mainDetail['fromdate'] = $fromDate;
//                $mainDetail['todate'] = $toDate;
                $mainDetail['todate'] = date('Y-m-d H:i:s');
                $mainDetail['amountPaid'] = $amountPaid;
                $mainDetail['parkingEntry'] = $amountPaidWithEntry;
                $mainDetail['exitGate'] = $exitGate;
                $mainDetail['garage_id'] = $garage_id;

                if ($amountPaid == 1 && $amountPaidWithEntry == 0 && empty($mainDetail['beacons'])) {
                    $mainDetail['beacons'] = $this->getBeaconDetail($parking_id, 1, $parking_ids);
                }
            } else {
                $mainDetail['TxnID'] = 0;
            }
            $mainDetail['owner_id'] = $parkingDetail['P_UserID'];
//            echo "<pre>";
//                print_r($mainDetail);
//            exit;
        } else {
            $content = array("status" => 0, "message" => 'Smart parking not found');
            echo json_encode($content);
            exit;
        }
        return $mainDetail;
    }

    function getUserCradInfo($user_id) {
        GLOBAL $con;
        $card_array = array();
        $EncryptKey = ENCRYPTKEY;
        $sql = "Select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=" . $user_id . " AND Card_Default='1'";
        $res2 = @mysqli_fetch_assoc(mysqli_query($con, $sql));
        if (!empty($res2)) {
            $card_type = $res2['Card_Type'];
            if ($card_type == 'Visa')
                $card_type = 'VISA';
            else if ($card_type == 'MasterCard')
                $card_type = 'MASTER CARD';
            else if ($card_type == 'American Express')
                $card_type = 'AMEX';
            else if ($card_type == 'Discover')
                $card_type = 'DISCOVER';
            else if ($card_type == 'Dinners Club')
                $card_type = 'DINERSCLUB';
            else
                $card_type = $card_type;
            $card_exp_year = substr($res2['Card_Exp_Year'], -2);
            $card_array[] = array("Card_ID" => $res2['Card_ID'], "Card_Type" => $card_type, "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $card_exp_year,
                "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Stripe_UserId" => (isset($res2['Stripe_UserId']) ? $res2['Stripe_UserId'] : '0'), "Card_StripeCustID" => (isset($res2['Card_StripeCustID']) ? $res2['Card_StripeCustID'] : '0'), "Card_Default" => $res2['Card_Default']);
        } else {
            $sql = "Select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=" . $user_id;
            $res2 = @mysqli_fetch_assoc(mysqli_query($con, $sql));
            if (!empty($res2)) {
                $card_type = $res2['Card_Type'];
                if ($card_type == 'Visa')
                    $card_type = 'VISA';
                else if ($card_type == 'MasterCard')
                    $card_type = 'MASTER CARD';
                else if ($card_type == 'American Express')
                    $card_type = 'AMEX';
                else if ($card_type == 'Discover')
                    $card_type = 'DISCOVER';
                else if ($card_type == 'Dinners Club')
                    $card_type = 'DINERSCLUB';
                else
                    $card_type = $card_type;
                $card_exp_year = substr($res2['Card_Exp_Year'], -2);
                $card_array[] = array("Card_ID" => $res2['Card_ID'], "Card_Type" => $card_type, "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $card_exp_year,
                    "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                    "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Stripe_UserId" => (isset($res2['Stripe_UserId']) ? $res2['Stripe_UserId'] : '0'), "Card_StripeCustID" => (isset($res2['Card_StripeCustID']) ? $res2['Card_StripeCustID'] : '0'), "Card_Default" => $res2['Card_Default']);
            }
        }

        return $card_array;
    }

    function getParkingTypeByPid($P_ID) {
        GLOBAL $con;
        $sql = "SELECT * FROM `tbl_parking` where P_Location = (select P_Location from `tbl_parking` where P_ID = '" . $P_ID . "') AND (P_Pricingtype = 'hourly' OR P_Pricingtype = 'daily') ORDER BY `P_ID`";
        //echo $sql;
        $result = mysqli_query($con, $sql);
        $parkingTypeStatus = '';
        $hourlyParkingID = '';
        $countParkingResult = mysqli_num_rows($result);
        if ($countParkingResult > 0) {
            while ($res = mysqli_fetch_assoc($result)) {
                if ($res['P_Pricingtype'] == 'daily' || $res['P_Pricingtype'] == 'hourly') {
                    if ($res['P_Pricingtype'] == 'hourly') {
                        $parkingTypeStatus['h'] = 'h';
                        $hourlyParkingID = $res['P_ID'];
                    } else if ($res['P_Pricingtype'] == 'daily') {
                        $parkingTypeStatus['d'] = 'd';
                    }
                }
            }
            asort($parkingTypeStatus);
            $parkingTypeStatus = implode('', $parkingTypeStatus);
        }
        $finalArray = array();
        $finalArray['parkingTypeStatus'] = preg_replace("/(.)\\1+/", "$1", $parkingTypeStatus);
        $finalArray['hourlyParkingID'] = $hourlyParkingID;
        return $finalArray;
    }

}

$parking = new parkingService();
?>
