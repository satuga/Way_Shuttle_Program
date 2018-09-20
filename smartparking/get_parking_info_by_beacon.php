<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class garageService {

    var $dbMsg;
    // Major = gate_id
    // Minor = gatemode
    private $beacon_id, $major, $minor, $user_id;

    function __construct() {
        $this->dbMsg = "Something went wrong. Please try again.";
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->beacon_id = isset($this->fields_val['beacon_id']) ? $this->fields_val['beacon_id'] : '';
        $this->major = isset($this->fields_val['major']) ? $this->fields_val['major'] : '';
        //$this->minor = isset($this->fields_val['minor']) ? $this->fields_val['minor'] : '';
        $this->user_id = isset($this->fields_val['user_id']) ? $this->fields_val['user_id'] : '';
        if ($this->beacon_id == "" || $this->major == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetGarageDetails($this->beacon_id, $this->major);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetGarageDetails($beacon_id, $major) {
        GLOBAL $con;
        $sql = "SELECT tp.P_ID as id, tp.P_Pricingtype as pricing_type, tp.P_UserID as owner_id FROM tbl_smartgate tsg LEFT JOIN tbl_parking tp ON tp.P_ID = tsg.SGT_GarageID WHERE tsg.SGT_BeaconUuid='" . trim($_REQUEST['beacon_id']) . "' AND tsg.SGT_ID='" . trim($_REQUEST['major']) . "'";
        $res = mysqli_query($con, $sql);
        $count = mysqli_num_rows($res);
        $user_id = !empty($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
        if ($count > 0) {
            $info = mysqli_fetch_assoc($res);
            if ($user_id != '') {
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
                }
            }
            $txn_id = 0;
            $gate_status = 0;
            $sqlTxn = "SELECT txn_id FROM `tbl_gate_parking_transaction` WHERE `garage_id` = '".$info['id']."' AND `user_id` = '".$user_id."' AND entry_mode = 1 Order by last_updated DESC";
            $resTxn = mysqli_query($con, $sqlTxn);
            $countTxn = mysqli_num_rows($resTxn);
            if ($countTxn > 0) {
                $infoTxn = mysqli_fetch_assoc($resTxn);
                $txn_id = $infoTxn['txn_id'];
                $gate_status = 1;
            }
            $beacon_id = !empty($_REQUEST['beacon_id'])?trim($_REQUEST['beacon_id'])  : '';
            $major = !empty($_REQUEST['major']) ? trim($_REQUEST['major']) : '';
            $minor = !empty($_REQUEST['minor']) ? trim($_REQUEST['minor']) : '';
            ob_end_clean();
            $info['beacon_id'] = $beacon_id;
            $info['major'] = $major;
            $info['minor'] = $minor;
            $info['txn_id'] = $txn_id;
            $info['gate_status'] = $gate_status;
            $info['Card_details'] = !empty($card_array) ? $card_array : array();
            $content = array("status" => 1, "data" => $info);
            echo json_encode($content);
            exit;
        }else {
            $content = array("status" => 0, "response" => array(), "message" => 'No Records Found');
            echo json_encode($content);
            exit;
        }
    }

}

$garage = new garageService();
?>
