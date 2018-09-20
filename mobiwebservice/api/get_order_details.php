<?php

error_reporting(1);
header('Content-Type: application/json');
include 'config.php';
include ('function.php');
include "../dine/dineclassbk.php";
$EncryptKey = ENCRYPTKEY;

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = sanitize($data['data']['user_id']);
$txn_id = sanitize($data['data']['txn_id']);
$type = sanitize($data['data']['type']);
if ($txn_id == '' || $type == '') {
    $output = array("status" => "0", "message" => "parameters missing");
    echo json_encode($output);
    exit;
}
if (strtolower($type) == 'dine') {
    $sq = "select orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.*,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,orders.PaymentDesc,orders.DeliveryAddress,merchant.postalCode,AES_DECRYPT(orders.Card_Number,'" . $EncryptKey . "') AS Card_No FROM orders
	INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
	INNER JOIN merchant ON merchant.id=orders.merchantID
	 WHERE orders.id=" . mysqli_real_escape_string($con, $txn_id);
    $i = 0;
    $subtotal = 0;
    $res = mysqli_query($con, $sq);
    while ($aRow = mysqli_fetch_array($res)) {
        //print_r($aRow); die;
        $tax = getMerchantTaxRate($aRow['merchantID']);
        $tax_total = ($aRow['orderAmount'] * $tax);
        $type = "Dining";
        if (strtolower($aRow['orderType']) == 'delivery')
            $type.=" - Delivery";
        else
            $type.=" - Pick Up";
        $contact['id'] = $aRow['Order_ID'];
        $contact['confirmation_id'] = 'DI' . $aRow['Order_ID'];
        $contact['place_by'] = $aRow['firstname'];
        $contact['merchantID'] = $aRow['merchantID'];
        $contact['mobile_phone'] = $aRow['mobile_phone'];
        $contact['merchantName'] = $aRow['merchantName'];
        $contact['orderDate'] = $aRow['orderDate'];
        $contact['orderTime'] = $aRow['orderTime'];
        $contact['orderPlacedOn'] = $aRow['orderPlacedOn'];
        $contact['orderType'] = $aRow['orderType'];
        $contact['order_type'] = $type;
        $contact['logo'] = $aRow['logo'];
        $contact['orderAmount'] = $aRow['orderAmount'];
        $contact['orderStatus'] = $aRow['orderStatus'];
        $contact['paymentStatus'] = $aRow['paymentStatus'];
        $contact['orderCompleted'] = $aRow['orderCompleted'];
        $contact['paymentType'] = $aRow['paymentType'];
        $contact['orderTaxAmount'] = $aRow['orderTaxAmount'];
        $contact['discount'] = $aRow['discount'];
        $contact['deliveryFee'] = $aRow['deliveryFee'];
        $contact['customerID'] = $aRow['customerID'];
        $contact['firstname'] = $aRow['firstname'];
        $contact['display_name'] = $aRow['display_name'];
        $contact['lastname'] = $aRow['lastname'];
        $contact['email_add'] = $aRow['email_add'];
        $contact['contact_principle'] = $aRow['contact_principle'];
        $contact['home_phone'] = $aRow['home_phone'];
        $contact['mobile_phone'] = $aRow['mobile_phone'];
        $contact['work_phone'] = $aRow['work_phone'];

        $contact['code'] = $aRow['code'];
        $contact['Card_Address'] = $aRow['Card_Address'];
        $contact['Card_State'] = $aRow['Card_State'];
        $contact['Card_City'] = $aRow['Card_City'];
        $contact['Card_Type'] = $aRow['Card_Type'];
        $contact['Card_Name'] = $aRow['Card_Name'];
        $contact['Card_Number'] = $aRow['Card_No'];

        $contact['DeliveryAddress'] = $aRow['DeliveryAddress'];
        $contact['contactAddress'] = $aRow['contactAddress'];
        $contact['RES_CITY'] = $aRow['RES_CITY'];
        $contact['RES_STATE'] = $aRow['RES_STATE'];
        $contact['Redeem'] = $aRow['Redeem'];
        $contact['street'] = $aRow['street'];
        $contact['C_CITY'] = $aRow['C_CITY'];
        $contact['C_STATE'] = $aRow['C_STATE'];
        $contact['postalCode'] = $aRow['postalCode'];
        $contact['PaymentDesc'] = $aRow['PaymentDesc'];
        $contact['points_earned'] = $aRow['points_earned'];


        $sql = "SELECT order_items.*,items.itemName,items.Cus_Pizza,orders.PaymentDesc,menus.menuName FROM order_items
		INNER JOIN items ON items.id=order_items.itemID
		INNER JOIN orders ON orders.id=order_items.orderID
		INNER JOIN menus ON menus.id=items.menuID
		WHERE orderID=" . $aRow['Order_ID'];
        $exe = mysqli_query($con, $sql);
        $y = 0;
        while ($rec = mysqli_fetch_array($exe)) {
            $record[$y]['id'] = $rec['id'];
            $record[$y]['itemID'] = $rec['itemID'];
            $record[$y]['itemName'] = $rec['itemName'];
            $record[$y]['menuName'] = $rec['menuName'];
            $record[$y]['quantity'] = $rec['quantity'];
            $record[$y]['price'] = $rec['price'];
            $record[$y]['size'] = $rec['size'];
            $record[$y]['notes'] = $rec['notes'];
            $record[$y]['crust'] = $rec['crust'];
            $record[$y]['Cus_Pizza'] = $rec['Cus_Pizza'];
            $record[$y]['PaymentDesc'] = $rec['PaymentDesc'];
            //$record[$y]['Size']	=	$rec['size'];

            $subtotal += ($rec['price'] * $rec['quantity']);

            $y++;
        }
        $tax_total = sprintf("%.2f", $subtotal * $tax / 100);
        $contact['orders'] = $record;
        $Csql = "select subitemName,subItem_value,subItemPrice from order_subitems where Order_ID='" . $aRow['Order_ID'] . "' ";
        $Cexe = mysqli_query($con, $Csql);
        $Cnum = mysqli_num_rows($Cexe);
        if ($Cnum > 0) {
            $subgroup_array = array();
            while ($CRes = mysqli_fetch_assoc($Cexe)) {
                //print_r($CRes); die;
                $subgroup_array[] = array("subitem_name" => $CRes['subitemName'], "subitem_type" => $CRes['subItem_value'], "subitem_price" => $CRes['subItemPrice']);
            }
        } else {
            $subgroup_array = array();
        }
        $contact['subitems'] = $subgroup_array;
        $contact['tax'] = $tax_total;
        //$i++;
    }
} else if (strtolower($type) == 'parking') {
    //echo 1;
    /* $SQL="SELECT * FROM tbl_paymenttransaction
      INNER JOIN tbl_parking ON  tbl_parking.P_ID=tbl_paymenttransaction.Parking_ID
      INNER JOIN tbl_parkinglocations ON  tbl_parkinglocations.Park_ID=tbl_parking.P_Location
      where TxnID='".$txn_id."'"; */
    $sq = "select * from tbl_paymenttransaction where TxnID=" . $ID;
    $ex = @mysqli_query($con, $sq);
    $rs = @mysqli_fetch_array($ex);

    if ($rs['PNF_TxnID'] <> '' && $rs['PNF_TxnID'] <> 0) {
        $sql = "select tbl_paymenttransaction.Payat_Lot, PNF_ID,PNF_PhysicalLocationName AS Park_Name,PNF_Address1 AS Park_Address,PNF_phones as Park_Phone,PNF_City AS Park_City,PNF_State AS Park_State,PNF_Zip AS Park_Zip,PNF_ServiceTypeDescription AS P_Lot_Type,tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,tbl_registeration.logo,PaymentSource,Item_ID,TxnID,code,TotalAmount,UsrID,PaymentDesc,Voucher_File,tbl_paymenttransaction.Status,date_format( tbl_paymenttransaction.TxnDate,'%d/%m/%Y') as regdate,tbl_paymenttransaction.TxnDate AS TxnDate,1 AS Main,tbl_paymenttransaction.Activity_ID,tbl_paymenttransaction.Movie_ID,tbl_paymenttransaction.Care_ID,tbl_paymenttransaction.Parking_ID, tbl_paymenttransaction.is_postpaid,tbl_paymenttransaction.Cat_ID,OrderStatus,DeliveryMethod,orderType,Redeem,TxnDate AS ORDERCREATED,Ticket_Type AS Ticket_Type,Ticket_Quantity AS Ticket_Quantity,quantity,Dine_ID AS Dine_ID,from_date,to_date,PNF_Confirmation,PNF_TxnID,45 AS Cat_ID,charges,Amount,RIGHT(AES_DECRYPT(tbl_paymenttransaction.Card_Number,'" . $EncryptKey . "'),4) AS Card_No,Owner_ID,'PNF' AS P_Pricingtype,'PNF' AS Park_Typeoflocation FROM tbl_paymenttransaction
			INNER JOIN tbl_pnftransaction ON  tbl_pnftransaction.PNF_ID=tbl_paymenttransaction.PNF_TxnID
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_paymenttransaction.UsrID
			WHERE tbl_paymenttransaction.TxnID=" . $txn_id;
    } else {
        $sql = "select tbl_paymenttransaction.Payat_Lot, tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,Park_Name,Park_Phone,Park_Address,Park_City,Park_State,Park_Zip,P_Lot_Type,tbl_registeration.logo,PaymentSource,Item_ID,TxnID,code,TotalAmount,UsrID,PaymentDesc,Voucher_File,tbl_paymenttransaction.Status,date_format( tbl_paymenttransaction.TxnDate,'%d/%m/%Y') as regdate,tbl_paymenttransaction.TxnDate AS TxnDate,1 AS Main,tbl_paymenttransaction.Activity_ID,tbl_paymenttransaction.Movie_ID,tbl_paymenttransaction.Care_ID,tbl_paymenttransaction.Parking_ID, tbl_paymenttransaction.is_postpaid,tbl_paymenttransaction.Cat_ID,OrderStatus,DeliveryMethod,orderType,Redeem,TxnDate AS ORDERCREATED,Ticket_Type AS Ticket_Type,Ticket_Quantity AS Ticket_Quantity,quantity,Dine_ID AS Dine_ID,from_date,to_date,PNF_Confirmation,PNF_TxnID,45 AS Cat_ID,charges,Amount,RIGHT(AES_DECRYPT(tbl_paymenttransaction.Card_Number,'" . $EncryptKey . "'),4) AS Card_No,Owner_ID,Park_AirportVenue,P_Pricingtype,Park_Typeoflocation,tbl_paymenttransaction.*,tbl_parkinglocations.smart_lot FROM tbl_paymenttransaction
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_paymenttransaction.UsrID
			 INNER JOIN tbl_parking ON tbl_parking.P_ID=tbl_paymenttransaction.Parking_ID
			 INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			WHERE TxnID=" . $txn_id;
    }
    //echo $sql;
    $res = mysqli_query($con, $sql);
    while ($aRow = mysqli_fetch_array($res)) {
        //	print_r($aRow); die;
        $type = "parking";
        if ($aRow['P_Pricingtype'] == 'event')
            $type.=" - Event";
        else if ($aRow['P_Pricingtype'] == 'PNF')
            $type.=" - PNF";
        else if ($aRow['Park_Typeoflocation'] == 'Residence')
            $type.=" - Way Space";
        else if ($aRow['Park_AirportVenue'] == 1)
            $type.=" - Airport";
        else
            $type.=" - City";
        $beacon = array();
        // $today=date("Y-m-d H:i:s",strtotime("+1 days",strtotime(date("Y-m-d H:i:s"))))."\n";
        $today = date("Y-m-d H:i:s");
        //$today="2016-07-14 22:31:59";
        //	$smartIsValid=strtotime($aRow['to_date']) >= strtotime($today) && strtotime($aRow['from_date']) <= strtotime($today) ? 1 : 0;
        //$smartIsValid=strtotime($aRow['to_date']) >= strtotime($today) ? 1 : 0;

        $dev_sql = "SELECT * FROM tbl_gate_parking_transaction WHERE user_id='" . $user_id . "' AND txn_id='" . $txn_id . "' order by in_time desc";
        $exe = mysqli_query($con, $dev_sql);
        $num = mysqli_num_rows($exe);
        $rec = mysqli_fetch_array($exe);

        if ($aRow['is_postpaid'] == 1) {
            $checkinTime = $rec['in_time'];
            $differenceTime = strtotime(date('Y-m-d H:i:s', strtotime($checkinTime . "+15 minutes")));
            if ($rec['entry_mode'] != 1) {
                $checkoutTime = strtotime(date('Y-m-d H:i:s', strtotime($rec['out_time'])));
            } else {
                $checkoutTime = strtotime(date('Y-m-d H:i:s'));
            }
            if ($checkoutTime < $differenceTime) {
                $aRow['charges'] = 0;
            }
            $aRow['Amount'] = $rec['amount'];

            $contact['checkIn'] = $rec['in_time'];
            if ($rec['entry_mode'] == 1) {
                $contact['checkOut'] = date('Y-m-d H:i:s');
            } else {
                $contact['checkOut'] = $rec['out_time'];
            }
        } else {
            if ($rec['entry_mode'] == 1) {
                $contact['checkIn'] = $rec['in_time'];
                if (strtotime(date('Y-m-d H:i:s')) > strtotime($aRow['to_date'])) {
                    $contact['checkOut'] = date('Y-m-d H:i:s');
                } else {
                    $contact['checkOut'] = $aRow['to_date'];
                }
            } else if ($rec['entry_mode'] == 2) {
                if (strtotime($rec['in_time']) < strtotime($aRow['from_date'])) {
                    $contact['checkIn'] = $rec['in_time'];
                } else {
                    $contact['checkIn'] = $aRow['from_date'];
                }
                if (strtotime($rec['out_time']) > strtotime($aRow['to_date'])) {
                    $contact['checkOut'] = $rec['out_time'];
                } else {
                    $contact['checkOut'] = $aRow['to_date'];
                }
            } else {
                $contact['checkIn'] = $aRow['from_date'];
                $contact['checkOut'] = $aRow['to_date'];
            }
        }

        if (!empty($rec['garage_id'])) {
            $temp_garage_id = $rec['garage_id'];
        } else {
            $temp_garage_id = $garage_id;
        }

        $smartIsValid = 1;
        $garage_id = $aRow['Parking_ID'];
        if ($aRow['smart_lot'] == 'yes') {
            $dev_sql = "SELECT transaction_id FROM tbl_gate_parking_transaction WHERE garage_id IN (" . $garage_id . ", " . $temp_garage_id . ") AND user_id='" . $user_id . "' AND txn_id='" . $txn_id . "' AND entry_mode=2 order by in_time desc";
            $exe = mysqli_query($con, $dev_sql);
            $num = mysqli_num_rows($exe);
            if ($num > 0)
                $smartIsValid = 0;
            if ($smartIsValid == 1) {
                $garage_id = $aRow['Parking_ID'];
//				$dev_sql="SELECT * FROM tbl_gate_parking_transaction WHERE user_id='".$user_id ."' AND txn_id='".$txn_id."' order by in_time desc";
//				$exe=mysqli_query($con,$dev_sql);
//				$num=mysqli_num_rows($exe);
//				$rec=mysqli_fetch_array($exe);
                //print_r($rec);
                //echo $rec['entry_mode'];
                $gatemode = $rec['entry_mode'] == 1 ? 2 : 1; //die;
                $dev_sql = "SELECT * from tbl_smartgate where SGT_GarageID='" . $temp_garage_id . "' and SGT_GateType='" . $gatemode . "'";
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
            }
        }

        $contact['smart_lot'] = $aRow['smart_lot'] == 'yes' ? "1" : "0";
        $contact['smart_valid'] = $smartIsValid;
        $contact['id'] = $aRow['Parking_ID'];
        $contact['is_postpaid'] = $aRow['is_postpaid'];
        $contact['confirmation_id'] = "WAY" . $aRow['TxnID'];
        $contact['place_by'] = $aRow['Name'];
        $contact['Park_Name'] = $aRow['Park_Name'];
        $contact['Park_Phone'] = $aRow['Park_Phone'];
        $contact['Park_Address'] = $aRow['Park_Address'];
        //$contact['checkIn'] = $aRow['from_date'];
        //$contact['checkOut'] = $aRow['to_date'];
        $contact['PlacedOn'] = $aRow['ORDERCREATED'];
        $contact['order_type'] = $type;
        //$contact['orderType']=$aRow['orderType'];
        $contact['logo'] = $aRow['logo'];
        $contact['Amount'] = $aRow['Amount'];
        $contact['charges'] = $aRow['charges'];
        $contact['Discount'] = $aRow['Discount'];
        $contact['Payat_Lot'] = $aRow['Payat_Lot'];
        $contact['Total_paid'] = $aRow['TotalAmount']-$aRow['Payat_Lot'];
        $contact['TotalAmount'] = $aRow['TotalAmount'];
        $contact['PaymentSource'] = $aRow['PaymentSource'];
        $contact['orderStatus'] = $aRow['OrderStatus'];
        /* $contact['paymentStatus']=$aRow['paymentStatus'];
          $contact['orderCompleted']=$aRow['orderCompleted'];
          $contact['paymentType']=$aRow['payment_type'];
          $contact['orderTaxAmount']=$aRow['orderTaxAmount'];
          $contact['discount']=$aRow['discount'];
          $contact['deliveryFee']=$aRow['deliveryFee'];
          $contact['customerID'] = $aRow['customerID'];
          $contact['firstname'] = $aRow['firstname'];
          $contact['display_name'] = $aRow['display_name'];
          $contact['lastname'] = $aRow['lastname'];
          $contact['email_add'] = $aRow['email_add'];
          $contact['contact_principle'] = $aRow['contact_principle'];
          $contact['home_phone'] = $aRow['home_phone'];
          $contact['mobile_phone'] = $aRow['mobile_phone'];
          $contact['work_phone'] = $aRow['work_phone']; */

        $contact['code'] = $aRow['code'];
        $contact['Card_Address'] = $aRow['Card_Address'];
        $contact['Card_State'] = $aRow['Card_State'];
        $contact['Card_City'] = $aRow['Card_City'];
        $contact['Card_Type'] = $aRow['Card_Type'];
        $contact['Card_Name'] = $aRow['Card_Name'];
        $contact['Card_Number'] = $aRow['Card_No'];
        $contact['beacons'] = $beacon;
    }
}
if (empty($contact))
    $output = array("status" => "0", "data" => "No orders found");
else
    $output = array("status" => "1", "data" => $contact);
echo json_encode($output);
exit;
?>
