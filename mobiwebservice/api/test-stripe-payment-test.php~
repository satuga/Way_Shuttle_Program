<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
include ('config.php');
require_once('test-stripe-config.php');
include("function.php");
include("../message_class.php");
include 'encrypt/security.php';
include("../dine/dineclassbk.php");
$Dine_Obj = new Dine();
$rep = 0;
$PTI_TID = 0;
$Package_TotalAmount = 0;
$EncryptKey = ENCRYPTKEY;
$adminmail = ADMINMAIL;


//----------- decrypt card number -----------------//
/*
  $encrypted_card_number=rawurldecode($_REQUEST['Card_Number']);
  $card_number=Security::decrypt($encrypted_card_number,CARDENCRYPT);
  function xml2array($contents, $get_attributes=1, $priority = 'tag')
  {
  if(!$contents) return array();
  if(!function_exists('xml_parser_create')) {
  return array();
  }
  //Get the XML parser of PHP - PHP must have this module for the parser to work
  $parser = xml_parser_create('');
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
  xml_parse_into_struct($parser, trim($contents), $xml_values);
  xml_parser_free($parser);
  if(!$xml_values) return;
  //Initializations
  $xml_array = array();
  $parents = array();
  $opened_tags = array();
  $arr = array();
  $current = &$xml_array; //Reference

  //Go through the tags.
  $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
  foreach($xml_values as $data) {
  unset($attributes,$value);//Remove existing values, or there will be trouble

  //This command will extract these variables into the foreach scope
  // tag(string), type(string), level(int), attributes(array).
  extract($data);//We could use the array by itself, but this is cooler.

  $result = array();
  $attributes_data = array();

  if(isset($value)) {
  if($priority == 'tag') $result = $value;
  else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
  }

  //Set the attributes too.
  if(isset($attributes) and $get_attributes) {
  foreach($attributes as $attr => $val) {
  if($priority == 'tag') $attributes_data[$attr] = $val;
  else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
  }
  }

  //See tag status and do the needed.
  if($type == "open") {//The starting of the tag '<tag>'
  $parent[$level-1] = &$current;
  if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
  $current[$tag] = $result;
  if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
  $repeated_tag_index[$tag.'_'.$level] = 1;

  $current = &$current[$tag];

  } else { //There was another element with the same tag name

  if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
  $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
  $repeated_tag_index[$tag.'_'.$level]++;
  } else {//This section will make the value an array if multiple tags with the same name appear together
  $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
  $repeated_tag_index[$tag.'_'.$level] = 2;

  if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
  $current[$tag]['0_attr'] = $current[$tag.'_attr'];
  unset($current[$tag.'_attr']);
  }

  }
  $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
  $current = &$current[$tag][$last_item_index];
  }

  } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
  //See if the key is already taken.
  if(!isset($current[$tag])) { //New Key
  $current[$tag] = $result;
  $repeated_tag_index[$tag.'_'.$level] = 1;
  if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

  } else { //If taken, put all things inside a list(array)
  if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

  // ...push the new element into that array.
  $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

  if($priority == 'tag' and $get_attributes and $attributes_data) {
  $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
  }
  $repeated_tag_index[$tag.'_'.$level]++;

  } else { //If it is not an array...
  $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
  $repeated_tag_index[$tag.'_'.$level] = 1;
  if($priority == 'tag' and $get_attributes) {
  if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

  $current[$tag]['0_attr'] = $current[$tag.'_attr'];
  unset($current[$tag.'_attr']);
  }

  if($attributes_data) {
  $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
  }
  }
  $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
  }
  }

  } elseif($type == 'close') { //End of tag '</tag>'
  $current = &$parent[$level-1];
  }
  }
  return($xml_array);
  }
 */
$User_ID = sanitize($_REQUEST["User_ID"]);
if (isset($User_ID) && $User_ID != '') {

    $tip = $_REQUEST['tip'];
    if (sanitize($_REQUEST['orderDineAmount']) == '')
        $_REQUEST['orderDineAmount'] = sanitize($_REQUEST['dine_total']);

    $deliver_id = sanitize($_REQUEST['Delivery_address']);
    $del_query = "select * from tbl_deliveryaddress where DE_ID ='" . $deliver_id . "'";
    $address_qu = mysqli_query($con, $del_query);
    $address_count = mysqli_num_rows($address_qu);
    if ($address_count > 0) {
        while ($d_address = mysqli_fetch_array($address_qu)) {
            $address_info[] = $d_address;
        }
        $deliver_address_info = $address_info[0]['DE_Address'] . ',' . $address_info[0]['BLDG_No'] . ',' . $address_info[0]['DE_State'] . ',' . $address_info[0]['DE_City'] . ',' . $address_info[0]['DE_Zipcode'];
    } else {
        $deliver_address_info = "";
    }
    if (strtolower(sanitize($_REQUEST['pay_type'])) == 'creditcard') {

        $token = sanitize($_REQUEST['stripe_token']);
        $amount = sanitize($_REQUEST['orderAmount']) * 100;
        $email = sanitize($_REQUEST['Delivery_Email']);
        $currency = "USD";
        //echo "payment TYPE SELECTED";
        if ($amount == "" || $email == "") {
            $content = array("status" => "0", "response" => "error", "message" => "Parameter Missing");
            echo json_encode($content);
            exit;
        }
        $response = array();
        //echo "before try";
        try {
            //$customerData=Stripe_Customer::all();
            // Ravi
            // tweak for new base encode algorithm for api + app call
            $encrypted_card_number = base64_decode(sanitize($_REQUEST['Card_Number']));
            //$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_Number='".$encrypted_card_number."' AND Card_User_ID=".$User_ID;
            //$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_StripeCustID='".$customer_id."' AND Card_User_ID=".$User_ID;
            //echo "here";
            //---------------- Get Credit card details ---------------------//
            //$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_ID=".$Card_ID;
            //echo "select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_User_ID =".$user_id." AND Card_Number = AES_ENCRYPT('".$encrypted_card_number."','".$EncryptKey."')";

            $exeCC = mysqli_query($con, "select *,AES_DECRYPT(Card_Number,'" . $EncryptKey . "') AS CC_NUMBER from tbl_creditcarddetails where Card_User_ID =" . $User_ID . " AND Card_Number = AES_ENCRYPT('" . $encrypted_card_number . "','" . $EncryptKey . "')");
            $ResCC = mysqli_fetch_array($exeCC);
            //  End Credit card details
            //var_dump($ResCC); die;
            $charge = Stripe_Charge::create(array(
                        'customer' => $ResCC['Card_StripeCustID'],
                        'amount' => $amount,
                        'currency' => "USD",
						'capture' => true
            ));
            //$no = Security::decrypt($ResCC['CC_NUMBER'],CARDENCRYPT);
            //$ResCC['CC_NUMBER'] = $no;
        } catch (Stripe_CardError $e) {
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        } catch (Stripe_InvalidRequestError $e) {
            // Invalid parameters were supplied to Stripe's API
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        } catch (Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        } catch (Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        } catch (Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $response['status'] = '0';
            $response['response'] = 'error';
            $response['message'] = $e->getMessage();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        $ErrorMessage = $charge->failure_message;
        $OrderID = $charge->id;
        $TransactionID = $charge->balance_transaction;
    }
    if ($charge->failure_message != "") {
        $ErrorMessage = $charge->failure_message;
    }

    if ($charge->paid == "1" && $charge->id != "") {

        $rep = 1;
        // User details
        $sql2 = "select firstname,lastname,street,city,state,zipcode,voucher,email_add from tbl_registeration where id=" . $User_ID;
        $exe2 = mysqli_query($con, $sql2);
        $res2 = mysqli_fetch_array($exe2);
        $Buyer_Name = $res2['firstname'] . " " . $res2['lastname'];

        // Package Cart
        $Package_Total = 0;
        // $Query="SELECT SUM(Cart_TotalAmount) AS TAMT,Cart_Package from tbl_cartpackages where Cart_Session='".$_REQUEST['device_id']."'";
        $Query = "SELECT SUM(Cart_TotalAmount) AS TAMT,Cart_Package from tbl_cartpackages where 	Cart_UserID='" . $User_ID . "'";


        $res = mysqli_query($con, $Query);
        $PRes1 = mysqli_fetch_array($res);
        // $Query="SELECT * FROM tbl_cartpackages WHERE Cart_Session='".$_REQUEST['device_id']."'";
        $Query = "SELECT * FROM tbl_cartpackages WHERE Cart_UserID='" . $User_ID . "'";
        $res = mysqli_query($con, $Query);
        $Pnum = mysqli_num_rows($res);

        $Package_Total = $PRes1['TAMT'];
        $PTI_TID = 0;
        if ($Pnum > 0) {
            $pk = $_REQUEST['Package'];
            if ($_REQUEST['Instructions' . $pk] == 'Enter special instructions for your order here:')
                $Instructions = "";
            else
                $Instructions = mysqli_real_escape_string($con, sanitize($_REQUEST['Instructions' . $pk]));

            $Delivery_Address = explode(":", sanitize($_REQUEST['DeliveryAddress' . $pk]));
            $Delivery_Email = explode(":", sanitize($_REQUEST['Delivery_Email' . $pk]));

            if ($Delivery_Address[1] <> '')
                $Delivery_Address = $Delivery_Address[1];
            else
                $Delivery_Address = sanitize($_REQUEST['DeliveryAddress' . $pk]);

            if ($Delivery_Email[1] <> '')
                $Delivery_Email = $Delivery_Email[1];
            else
                $Delivery_Email = sanitize($_REQUEST['Delivery_Email' . $pk]);

            $rand = genRandomString();
            $code = $rand;
            $Delivery_Address = sanitize($_REQUEST['DeliveryAddress']);
            $Query1 = "INSERT INTO tbl_packagetransaction(T_ID,T_Package,T_UsrID,T_TotalAmount,T_Created,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,code) VALUES (''," . $PRes1['Cart_Package'] . "," . $User_ID . "," . $PRes1['TAMT'] . ",Now(),'" . mysqli_real_escape_string($con, $ResCC['Card_Type']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_FirstName']) . "',AES_ENCRYPT('" . $ResCC['CC_NUMBER'] . "','" . $EncryptKey . "'),'" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Month']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Year']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Zip']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Street']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_State']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_City']) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['DeliveryMethod' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['State' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['City' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['Zip_Code' . $pk])) . "','" . mysqli_real_escape_string($con, $Delivery_Email) . "','" . mysqli_real_escape_string($con, $Delivery_Address) . "','" . $Instructions . "','" . $code . "')";

            mysqli_query($con, $Query1);

            $PTI_TID = mysqli_insert_id($con);
            $Package_TotalAmount = $PRes1['TAMT'];

            while ($PRes = mysqli_fetch_array($res)) {
                if (sanitize($_REQUEST['Instructions' . $pk]) == 'Enter special instructions for your order here:')
                    $Instructions = "";
                else
                    $Instructions = mysqli_real_escape_string($con, sanitize($_REQUEST['Instructions' . $pk]));

                $Delivery_Address = explode(":", sanitize($_REQUEST['DeliveryAddress' . $pk]));
                $Delivery_Email = explode(":", sanitize($_REQUEST['Delivery_Email' . $pk]));

                if ($Delivery_Address[1] <> '')
                    $Delivery_Address = $Delivery_Address[1];
                else
                    $Delivery_Address = sanitize($_REQUEST['DeliveryAddress' . $pk]);

                if ($Delivery_Email[1] <> '')
                    $Delivery_Email = $Delivery_Email[1];
                else
                    $Delivery_Email = sanitize($_REQUEST['Delivery_Email' . $pk]);

                $Query = "INSERT INTO tbl_packagetransactionitems(PTI_ID,PTI_TID,PTI_ItemID,PTI_Amount,PTI_Quantity,PTI_TotalAmount,DeliveryMethod,DeliveryEmail,DeliveryAddress,PaymentDesc) VALUES (''," . $PTI_TID . "," . $PRes['Cart_Item'] . "," . $PRes['Cart_Amount'] . "," . $PRes['Cart_Quantity'] . "," . $PRes['Cart_TotalAmount'] . ",'" . mysqli_real_escape_string($con, sanitize($_REQUEST['DeliveryMethod' . $pk])) . "','" . mysqli_real_escape_string($con, $Delivery_Email) . "','" . mysqli_real_escape_string($con, $Delivery_Address) . "','" . $Instructions . "')";
                mysqli_query($con, $Query);
                $pk++;
            }
        }



        // end Package
        $rand = genRandomString();
        $code = $rand;

        $Order_Ids = "";
        $Pay_TotalAmount = 0;
        $Dine_TotalAmount = 0;
        $voucher_created = 0;
        $pk = 1;
        $Pay_TxnID = 0;
        /* ================================================================================================================ */
        $Main_query = "select * from tbl_cart where Cart_UserID='" . $User_ID . "'";
        $Main_res = mysqli_query($con, $Main_query);
        $AllTran = mysqli_num_rows($Main_res);
        $Dine_Query = "select * from tbl_cart where Cart_UserID='" . $User_ID . "' AND Cart_Type='Dine'";
        $Dine_res = mysqli_query($con, $Dine_Query);
        $result = mysqli_fetch_array($Dine_res);
        $DineTran = mysqli_num_rows($Dine_res);

        $Other_Query = "select * from tbl_cart where Cart_UserID='" . $User_ID . "' AND Cart_Type!='Dine'";
        $Other_res = mysqli_query($con, $Other_Query);
        $OtherTran = mysqli_num_rows($Other_res);
        if ($AllTran > 0) {
            $orderdate = date("Y-m-d", strtotime(sanitize($_REQUEST['orderdate'])));
            if (sanitize($_REQUEST['pay_type']) == 'Cash')
                $paymentStatus = "Due";
            else
                $paymentStatus = "Paid";
            if (sanitize($_REQUEST['Instructions']) == 'Enter special instructions for your order here:')
                $Instructions = "";

            //$Delivery_Address=$_REQUEST['Delivery_Address'];
            $Delivery_Address = $deliver_address_info;
            $Owner_ID = $result['Owner_ID'];
            // For Miles/Points
            $PointPercentage = round(GetPointPercentage('72'));
            // Points Calculation
            $Points = 0;
            //$PointPercentage;
            $Points = round(((sanitize($_REQUEST['orderDineAmount']) * $PointPercentage) / 100), 2);
            // Insert Order

            if (sanitize($_REQUEST['delivery_fee']) == '')
                $deliveryFee = 0;
            else
                $deliveryFee = mysqli_real_escape_string($con, sanitize($_REQUEST['delivery_fee']));
            if ($_REQUEST['POINTS'] > 0)
                $sess_points = mysqli_real_escape_string($con, sanitize($_REQUEST['POINTS']));
            else
                $sess_points = 0;


            $Query = "INSERT INTO orders (merchantID,Owner_ID,orderDate,orderTime,customerID,orderPlacedOn,orderType,orderAmount,Tips,orderStatus,paymentStatus,paymentType,orderTaxAmount,discount,discount_points,points_earned,deliveryFee,comments,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,code,DeliveryAddress,PaymentDesc,delivery_lat,delivery_long,parking_total,dine_total)
      VALUES
      ('" . sanitize($_REQUEST['MID']) . "'," . $result['Owner_ID'] . ",'" . mysqli_real_escape_string($con, $orderdate) . "','" . sanitize($_REQUEST['ordertime']) . "','" . $User_ID . "',NOW(),'" . mysqli_real_escape_string($con, sanitize($_REQUEST['orderfor'])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['orderAmount'])) . "','" . $tip . "','Pending','" . $paymentStatus . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['pay_type'])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['dine_tax'])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['discount'])) . "','" . $sess_points . "','" . $Points . "','" . $deliveryFee . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['comments'])) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Type']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_FirstName']) . "',AES_ENCRYPT('" . $ResCC['CC_NUMBER'] . "','" . $EncryptKey . "'),'" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Month']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Year']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Zip']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Street']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_State']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_City']) . "','" . $code . "','" . mysqli_real_escape_string($con, $Delivery_Address) . "','" . mysqli_real_escape_string($con, $Instructions) . "','" . sanitize($_REQUEST['delivery_lat']) . "','" . sanitize($_REQUEST['delivery_long']) . "','" . sanitize($_REQUEST['parking_total']) . "','" . sanitize($_REQUEST['dine_total']) . "')";
            $Dine_TotalAmount = sanitize($_REQUEST['dine_total']);
            mysqli_query($con, $Query);
            $Order_Id = mysqli_insert_id($con);
            $Dine_TxnID = $Order_Id;
            $itemIDs = "";


            if (sanitize($_REQUEST['promo_id']) != '') {
                $Query_promo = "INSERT INTO tbl_promocodelog(PCL_OrderID,PCL_CategoryName,PCL_CustomerID,PCL_CreatedDate,PCL_PMC_PromoID)
        VALUES
        ('" . $Order_Id . "','DINE','" . $User_ID . "',NOW(),'" . sanitize($_REQUEST['promo_id']) . "')";
                mysqli_query($con, $Query_promo);


                $promo_upsql = "UPDATE tbl_promocode SET PMC_occupied= PMC_occupied + 1 WHERE PMC_PromoID = " . sanitize($_REQUEST['promo_id']);
                mysqli_query($con, $promo_upsql);
            }
            if (sanitize($_REQUEST['POINTS']) > 0 && sanitize($_REQUEST['discount']) > 0) {
                // Move Points to Redeemed
                $psql = "SELECT Points,Points_Redeemed FROM tbl_registeration WHERE id=" . $User_ID;
                $pexe = mysqli_query($con, $psql);
                $pres = mysqli_fetch_array($pexe);
                $NewPoints = $pres['Points'] - sanitize($_REQUEST['POINTS']);
                $NewPoints_Redeemed = $pres['Points_Redeemed'] + sanitize($_REQUEST['POINTS']);
                $upsql = "UPDATE tbl_registeration SET Points=" . $NewPoints . ",Points_Redeemed=" . $NewPoints_Redeemed . " WHERE id=" . $User_ID;
                mysqli_query($con, $upsql);
            }
            $_REQUEST['POINTS'] = "";
            if ($DineTran > 0) {
                $Dine_Query1 = "select * from tbl_cart where Cart_UserID='" . $User_ID . "' AND Cart_Type='Dine'";
                $Dine_res1 = mysqli_query($con, $Dine_Query);
                while ($result11 = mysqli_fetch_array($Dine_res1)) {
                    // Insert Order Items
                    $Query = "INSERT INTO order_items (orderID,itemID,quantity,price,size,notes,crust)
            VALUES
            ('" . $Order_Id . "','" . $result11['Cart_ServiceID'] . "','" . $result11['Cart_Quantity'] . "','" . $result11['Amount'] . "','" . mysqli_real_escape_string($con, $result11['Size']) . "','" . mysqli_real_escape_string($con, $result11['Notes']) . "','" . $result11['crust'] . "')";

                    mysqli_query($con, $Query);
                    $orderItemID = mysqli_insert_id($con);
                    $itemIDs.="," . $result11['Cart_ServiceID'];
                    // Insert Sub Items
                    $Query2 = "SELECT * from tbl_cartsubitems where Cart_ID=" . $result11['Cart_ID'];
                    $res2 = mysqli_query($con, $Query2);
                    $SNum = mysqli_num_rows($res2);
                    if ($SNum > 0) {
                        while ($result12 = mysqli_fetch_array($res2)) {
                            $SubGdetails = SubItemdetails($result12['subgroup_id']);
                            $price_index = $result12['price_index'];
                            if ($result12['subgroup_value'] == 'Left Side' || $result12['subgroup_value'] == 'Right Side')
                                $subItemPrice = $SubGdetails[1] / 2;
                            else if ($result12['price_index'] > 0) {
                                $subprices = explode(",", $SubGdetails[1]);
                                $subItemPrice = $subprices[$price_index - 1];
                            } else
                                $subItemPrice = $SubGdetails[1];
                            if ($result12['subgroup_x2'] == 1)
                                $subItemPrice = $subItemPrice * 2;
                            if ($result12['Free_Toppings'] == 1)
                                $subItemPrice = 0;
                            $Query2 = "INSERT INTO order_subitems (Order_ID,orderItemID,subItemID,subItemName,subitem_value,subItemPrice,subgroup_x2,Free_Toppings)
                VALUES
                ('" . $Order_Id . "','" . $orderItemID . "','" . $result12['subgroup_id'] . "','" . mysqli_real_escape_string($con, $SubGdetails[0]) . "','" . mysqli_real_escape_string($con, $result12['subgroup_value']) . "','" . mysqli_real_escape_string($con, $subItemPrice) . "','" . $result12['subgroup_x2'] . "','" . $result12['Free_Toppings'] . "')";
                            mysqli_query($con, $Query2);
                        }
                    }
                }
                $sql = "INSERT INTO tbl_points(P_UserID,P_Transaction,P_Type,P_Point_Thru,P_points,P_Percentage,P_TransactionAmount,P_Created) VALUES(" . $User_ID . "," . $Order_Id . ",'Dine','Payment Transaction','" . $Points . "','" . $PointPercentage . "'," . sanitize($_REQUEST['orderDineAmount']) . ",Now())";
                mysqli_query($con, $sql);
                // Update Points for Transaction
                $psql = "SELECT Points FROM tbl_registeration WHERE id=" . $User_ID;
                $pexe = mysqli_query($con, $psql);
                $pres = mysqli_fetch_array($pexe);
                $DBPoints = $pres['Points'] + $Points;
                $upsql = "UPDATE tbl_registeration SET Points=" . $DBPoints . " WHERE id=" . $User_ID;
                mysqli_query($con, $upsql);
                // End Miles/Points
            }
            if ($OtherTran > 0) {
                while ($result = mysqli_fetch_array($Other_res)) {
                    $ServiceID = $result['Cart_ServiceID'];
                    if ($result['Cart_Type'] == 'Activities') {
                        $Cat_ID = 64;
                        $Parking_ID = 0;
                        $Ticket_ID = 0;
                        $Care_ID = 0;
                        $Activity_ID = $result['Cart_ServiceID'];
                        $sq = "SELECT SUM(CAP_Quantity) AS CAP_Quantity FROM  tbl_cartactivityprice WHERE CAP_CartID=" . $result['Cart_ID'];
                        $ssexe = mysqli_query($con, $sq);
                        $ssres = mysqli_fetch_array($ssexe);
                        $CAP_Quantity = $ssres['CAP_Quantity'];
                        if ($CAP_Quantity < 1)
                            $CAP_Quantity = $result['Cart_Quantity'];
                    }
                    else if ($result['Cart_Type'] == 'Movies') {
                        $Cat_ID = 70;
                        $Parking_ID = 0;
                        $Ticket_ID = 0;
                        $Care_ID = 0;
                        $Activity_ID = 0;
                        $Movie_ID = $result['Cart_ServiceID'];
                        $sq = "SELECT SUM(CAP_Quantity) AS CAP_Quantity FROM tbl_cartmovieprice WHERE CAP_CartID=" . $result['Cart_ID'];
                        $ssexe = mysqli_query($con, $sq);
                        $ssres = mysqli_fetch_array($ssexe);
                        $CAP_Quantity = $ssres['CAP_Quantity'];
                        if ($CAP_Quantity < 1)
                            $CAP_Quantity = $result['Cart_Quantity'];
                    }
                    else if ($result['Cart_Type'] == 'Care') {
                        $Cat_ID = 61;
                        $Parking_ID = 0;
                        $Ticket_ID = 0;
                        $Care_ID = $result['Cart_ServiceID'];
                        $Activity_ID = 0;
                    } else if ($result['Cart_Type'] == 'Tickets') {
                        $Cat_ID = 71;
                        $Parking_ID = 0;
                        $Ticket_ID = $result['Cart_ServiceID'];
                        $Activity_ID = 0;
                        $Care_ID = 0;
                        $Movie_ID = 0;
                        $ServiceTitle = GetServiceTitle($Ticket_ID, $Cat_ID);
                    } else if ($result['Cart_Type'] == 'Parking') {
                        $Cat_ID = 45;
                        $Parking_ID = $result['Cart_ServiceID'];
                        $Ticket_ID = 0;
                        $Care_ID = 0;
                        $Activity_ID = 0;
                    }

                    $ServiceTitle = GetServiceTitle($Activity_ID, $Cat_ID);

                    if (sanitize($_REQUEST['Instructions' . $pk]) == 'Enter special instructions for your order here:')
                        $Instructions = "";
                    else
                        $Instructions = mysqli_real_escape_string($con, sanitize($_REQUEST['Instructions' . $pk]));

                    $Delivery_Address = explode(":", sanitize($_REQUEST['DeliveryAddress' . $pk]));
                    $Delivery_Email = explode(":", sanitize($_REQUEST['Delivery_Email' . $pk]));
                    if ($Delivery_Address[1] <> '')
                        $Delivery_Address = $Delivery_Address[1];
                    else
                        $Delivery_Address = sanitize($_REQUEST['DeliveryAddress' . $pk]);
                    if ($Delivery_Email[1] <> '')
                        $Delivery_Email = $Delivery_Email[1];
                    else
                        $Delivery_Email = sanitize($_REQUEST['Delivery_Email' . $pk]);
                    $CreatedOn = date("Y-m-d, G:i:s");

                    if ($Cat_ID == '64') {
                        $SubCat_ID = GetPlaySubcat($ServiceID, $Cat_ID);

                        if ($SubCat_ID == '86') {
                            $PointPercentage = GetSubcatPointPercentage($SubCat_ID);
                        } else {
                            $PointPercentage = GetPointPercentage($Cat_ID);
                        }
                    } else {
                        $PointPercentage = GetPointPercentage($Cat_ID);
                    }
                    // Points Calculation
                    $Points = 0;
                    //$PointPercentage;
                    $Points = round((($result['TotalAmount'] * $PointPercentage) / 100), 2);
                    // Get Additional Charges labels
                    if ($result['Cart_Type'] == 'Parking')
                        $csql1 = "SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=" . $result['Cart_ServiceID'];
                    else if ($result['Cart_Type'] == 'Activities')
                        $csql1 = "SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Activity_ID=" . $result['Cart_ServiceID'];

                    $csql2 = "SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=" . $Cat_ID;
                    $csql = $csql1 . " UNION " . $csql2;
                    $cexe = mysqli_query($con, $csql);
                    $extfees = 0;
                    $extlabels = "";
                    //$cres=mysqli_fetch_array($cexe);
                    //var_dump($cres); die;
                    while ($cres = mysqli_fetch_array($cexe)) {
                        if ($cres['format'] == '%') {
                            $per_amt = $result['Amount'] * $cres['Setting_Value'] / 100;
                            $extfees = round($extfees + $per_amt, 2);
                            $labels = $cres['Setting_Name'] . "(" . $cres['Setting_Value'] . "%):$" . $per_amt . ",";
                        } else {
                            $per_amt = $cres['Setting_Value'];
                            $extfees = round($extfees + $cres['Setting_Value'], 2);
                            $labels = $cres['Setting_Name'] . ":$" . $per_amt . ",";
                        }
                        if ($extfees >= 0)
                            $extlabels.=$labels;
                    }
                    $extlabels = trim($extlabels, ",");
                    if ($result['charges'] <= 0)
                        $extlabels = "";

                    // New Changes
                    $TotalAmount = $result['TotalAmount'];
                    $charges = $result['charges']; //Check this Bindra
                    if ($result['Cart_Type'] == 'Activities') {
                        if ($result['Shipping_Cost'] > 0) {
                            $labels = "Shipping Cost " . ":$" . $result['Shipping_Cost'] . ",";
                            $extlabels.=$labels;
                            $charges = $charges + $result['Shipping_Cost'];
                            $TotalAmount = $TotalAmount + $result['Shipping_Cost'];
                        }
                    }

                    $extlabels = trim($extlabels, ",");
                    // End New Changes
                    // $Query="INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Care_ID,Activity_ID,Ticket_ID,Cat_ID,from_date,to_date,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,charges,charges_details,TotalAmount,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_CWCode,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points)
                    // VALUES
                    // ('".$User_ID."','".$result['Owner_ID']."','".$Parking_ID."','".$Care_ID."','".$Activity_ID."','".$Ticket_ID."','".$Cat_ID."','".$result['from_date']."','".$result['to_date']."','".mysqli_real_escape_string($con,$result['Show_Time'])."','".mysqli_real_escape_string($con,$result['Movie_Name'])."','".mysqli_real_escape_string($con,$result['Club_Number'])."','".$CreatedOn."','Credit Card','".$result[Cart_Quantity]."','".$result['Amount']."','".$result[charges]."','".mysqli_real_escape_string($con,$extlabels)."','".$result[TotalAmount]."','".$code."','1','".$result[Ticket_Type]."','".$result[Ticket_Quantity]."','".$result[care_payment_type]."','".$result[Parking_type]."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."','".mysqli_real_escape_string($con,$ResCC['Card_Number'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Verify_Code'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,strtoupper($ResCC['Card_State']))."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['State'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['City'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'.$pk])."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."','".$Points."')";

                    $Query = "INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Care_ID,Activity_ID,Ticket_ID,Movie_ID,Cat_ID,from_date,to_date,Ticket_Title,Ticket_EventID,Ticket_EventVenue,Ticket_Section,Ticket_Row,Ticket_Owner,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,Discount,charges,Overnight_Fee,charges_details,TotalAmount,Payat_Lot,Payment_Type,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points,NetPark_rate,NetPark_daily_rate)
            VALUES
            ('" . $User_ID . "','" . $result['Owner_ID'] . "','" . $Parking_ID . "','" . $Care_ID . "','" . $Activity_ID . "','" . $Ticket_ID . "','" . $Movie_ID . "','" . $Cat_ID . "','" . $result['from_date'] . "','" . $result['to_date'] . "','" . mysqli_real_escape_string($con, $result['Ticket_Title']) . "','" . mysqli_real_escape_string($con, $result['Ticket_EventID']) . "','" . mysqli_real_escape_string($con, $result['Ticket_EventVenue']) . "','" . mysqli_real_escape_string($con, $result['Ticket_Section']) . "','" . mysqli_real_escape_string($con, $result['Ticket_Row']) . "','" . mysqli_real_escape_string($con, $result['Ticket_Owner']) . "','" . mysqli_real_escape_string($con, $result['Show_Time']) . "','" . mysqli_real_escape_string($con, $result['Movie_Name']) . "','" . mysqli_real_escape_string($con, $result['Club_Number']) . "','" . $CreatedOn . "','Credit Card','" . $result['Cart_Quantity'] . "','" . $result['Amount'] . "','" . $result['Discount'] . "','" . $charges . "','" . $result['Overnight_Fee'] . "','" . mysqli_real_escape_string($con, $extlabels) . "','" . $TotalAmount . "','" . $result['Payat_Lot'] . "','" . $result['Payment_Type'] . "','" . $code . "','1','" . $result['Ticket_Type'] . "','" . $result['Ticket_Quantity'] . "','" . $result['care_payment_type'] . "','" . $result['Parking_type'] . "','" . mysqli_real_escape_string($con, $ResCC['Card_Type']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_FirstName']) . "',AES_ENCRYPT('" . $ResCC['CC_NUMBER'] . "','" . $EncryptKey . "'),'" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Month']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Year']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Zip']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Street']) . "','" . mysqli_real_escape_string($con, strtoupper($ResCC['Card_State'])) . "','" . mysqli_real_escape_string($con, $ResCC['Card_City']) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['DeliveryMethod' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['State' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['City' . $pk])) . "','" . mysqli_real_escape_string($con, sanitize($_REQUEST['Zip_Code' . $pk])) . "','" . mysqli_real_escape_string($con, $Delivery_Email) . "','" . mysqli_real_escape_string($con, $Delivery_Address) . "','" . $Instructions . "','" . $Points . "','" . mysqli_real_escape_string($con, $result['NetPark_rate']) . "','" . mysqli_real_escape_string($con, $result['NetPark_daily_rate']) . "')";
                    mysqli_query($con, $Query);
                    $Order_Id = mysqli_insert_id($con);
                    // For Message
                    /*  put comments by bindra shah 25 feb 2016
                      issues : Many of function missing so file break

                      if($result['Cart_Type']=='Parking')
                      {
                      $Message_Obj->insertParkingmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
                      }
                      else{
                      $Message_Obj->insertmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
                      }
                     */
                    // Send Order to Ticket Evolution
                    if ($result['Cart_Type'] == 'Tickets') {
                        include("ticketevolution-sendorder.php");
                    }
                    // For Tickets Quantity
                    if ($Ticket_ID > 0) {
                        $sq = "SELECT Booked_Ticket_Quantity,Available_Ticket_Quantity,Ticket_Quantity FROM tbl_tickets WHERE Ticket_ID=" . $Ticket_ID;
                        $ex = mysqli_query($con, $sq);
                        $re = mysqli_fetch_array($ex);
                        $Booked_Ticket_Quantity = $re['Booked_Ticket_Quantity'] + $result['Cart_Quantity'];
                        $Available_Ticket_Quantity = $re['Available_Ticket_Quantity'] - $result['Cart_Quantity'];

                        $sq = "UPDATE tbl_tickets SET Booked_Ticket_Quantity=" . $Booked_Ticket_Quantity . ",Available_Ticket_Quantity=" . $Available_Ticket_Quantity . " WHERE Ticket_ID=" . $Ticket_ID;
                        mysqli_query($con, $sq);
                    }


                    // For Miles/Points
                    if ($PointPercentage == '')
                        $PointPercentage = 0;
                    if ($Points == '')
                        $Points = 0;
                    $sql = "INSERT INTO tbl_points(P_UserID,P_Transaction,P_Point_Thru,P_points,P_Percentage,P_TransactionAmount,P_Created) VALUES(" . $User_ID . "," . $Order_Id . ",'Payment Transaction','" . round($Points, 2) . "','" . round($PointPercentage) . "'," . $TotalAmount . ",Now())";
                    mysqli_query($con, $sql);
                    // Update Points for Transaction
                    $psql = "SELECT Points FROM tbl_registeration WHERE id=" . $User_ID;
                    $pexe = mysqli_query($con, $psql);
                    $pres = mysqli_fetch_array($pexe);
                    $DBPoints = $pres['Points'] + $Points;
                    $upsql = "UPDATE tbl_registeration SET Points=" . $DBPoints . " WHERE id=" . $User_ID;
                    mysqli_query($con, $upsql);
                    // End Miles/Points


                    if (sanitize($_REQUEST['DeliveryMethod' . $pk]) == 'Electronic Confirmation Code' && $voucher_created == 0) {

                        // Create PDF
                        if ($result['$resCart_Type'] == 'Activities') {

                            $filename = "WayTicket" . $Order_Id . $result['Cart_ServiceID'] . strtotime(date("F j, Y, g:i a"));

                            $htmlfilename = $filename . ".html";
                            $pngfilename = $filename . ".png";
                            $voucher_created = $Order_Id;
                            $sql = "UPDATE tbl_paymenttransaction SET Voucher_File='" . $htmlfilename . "' WHERE TxnID=" . $Order_Id;
                            mysqli_query($con, $sql);
                        }
                    }

                    $Order_Ids.="," . $Order_Id;
                    $Pay_TotalAmount = $Pay_TotalAmount + $TotalAmount;

                    $pk++;
                }
            }
            $Order_Ids = trim($Order_Ids, ",");
            $Pay_TotalAmount = $Pay_TotalAmount + $Package_TotalAmount + $Dine_TotalAmount;
            if ($Dine_TxnID == '')
                $Dine_TxnID = 0;
            if ($Order_Ids == '')
                $Order_Ids = 0;
            if ($PTI_TID == '')
                $PTI_TID = 0;
            if ($User_ID == '')
                $User_ID = 0;

            //--------------- Empty Cart -------------------//



            $sql = "INSERT INTO tbl_payment(Dine_TxnID,Pay_TxnID,Package_TxnID,UsrID,Pay_TotalAmount,Pay_Created,Pay_Status) VALUES('" . $Dine_TxnID . "','" . $Order_Ids . "','" . $PTI_TID . "','" . $User_ID . "','" . $Pay_TotalAmount . "',NOW(),1)";
            mysqli_query($con, $sql);
            $Payment_Id = mysqli_insert_id($con);

            // $queryemp="DELETE FROM tbl_cart where Sess_ID='".$_REQUEST['device_id']."'";
            //TEMP REMOVAL RAVI FOR TESTING
            $queryemp = "DELETE FROM tbl_cart where Cart_UserID='" . $User_ID . "'";
            mysqli_query($con, $queryemp);


            //Insert First Data values into Database
            if (getenv(HTTP_X_FORWARDED_FOR)) {
                $visit_ip = getenv(HTTP_X_FORWARDED_FOR);
            } else {
                $visit_ip = getenv(REMOTE_ADDR);
            }

            if ($CalculatedTax == '')
                $CalculatedTax = 0;
            if ($CalculatedShipping == '')
                $CalculatedShipping = 0;
            if ($TransactionID == '')
                $TransactionID = 0;
            if ($TransactionScore == '')
                $TransactionScore = 0;
            if ($ProessorReferenceNumber == '')
                $ProessorReferenceNumber = 0;
            if ($ProessorReferenceNumber == '')
                $ProessorReferenceNumber = 0;
            $sql = "INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('" . $Payment_Id . "','" . $User_ID . "','" . sanitize($_REQUEST['orderAmount']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Type']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_FirstName']) . "',AES_ENCRYPT('" . $ResCC['CC_NUMBER'] . "','" . $EncryptKey . "'),'" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Month']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Year']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Street']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_State']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_City']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Zip']) . "','" . $CommercialServiceProvider . "','" . $TransactionTime . "','" . $TransactionID . "','" . $ProessorReferenceNumber . "','" . $ProcessorResponseMessage . "','" . $ErrorMessage . "','" . $OrderID . "','" . $ApprovalCode . "','" . $AVSResponse . "','" . $TDate . "','" . $TransactionResult . "','" . $ProcessorApprovalCode . "','" . $CalculatedTax . "','" . $CalculatedShipping . "','" . $TransactionScore . "','" . $AuthenticationResponseCode . "',Now(),'" . $visit_ip . "')";

            mysqli_query($con, $sql);

            $fp_quantity = $rec['fp_quantity'] - sanitize($_REQUEST['quantity']);
            if (sanitize($_REQUEST['pay_type']) == 'Cash')
                $resp = 1;
            else
                $resp = $TransactionResult;
            if ($TransactionResult == 'APPROVED')
                $rep = 1;

            /* ======================================================= Bindra Changes  ================================================================= */
            $data = array();
            $data['dine'] = array();
            $data['parking'] = array();
            $points_earned = 0;
            $grand_total = 0;
            // Get Transactions Details
            $Transactions = GetTotalTransaction($Payment_Id);
            $data['order_date'] = date("F j, Y", strtotime($Transactions[3]));
            $data['order_time'] = date("h:i:A", strtotime($Transactions[3]));
            // End Get Transactions Details
            // Get Dine Details
            if ($Transactions[4] != '' && $Transactions[4] > 0) {
                $DineRecords = $Dine_Obj->GetAReservation($Transactions[4]);
                if ($DineRecords[1] > 0) {
                    $OrderNo = $Transactions[4];
                    $data['dine']['order_id'] = 'DI' . $OrderNo;
                    $data['dine']['type'] = 'Dining';
                    $OrderItems = $Dine_Obj->GetOrderItems($OrderNo);
                    $data['dine']['total_item'] = $OrderItems[1];

                    foreach ($DineRecords[0] as $k => $v) {
                        $points_earned = $points_earned + $v['points_earned'];
                        $TxnIDs = explode(",", $OrderNo);
                        $merchantName = $v['merchantName'];
                        if (COUNT($TxnIDs) > 1) {
                            $GetOrderDetailByOrderNo = GetOrderDetailByOrderNo($OrderNo);
                            $orderAmount = !empty($GetOrderDetailByOrderNo['orderAmount'])?$GetOrderDetailByOrderNo['orderAmount']:'';
                            $orderTaxAmount = !empty($GetOrderDetailByOrderNo['orderTaxAmount'])?$GetOrderDetailByOrderNo['orderTaxAmount']:'';
                            $discount = !empty($GetOrderDetailByOrderNo['discount'])?$GetOrderDetailByOrderNo['discount']:'';
                            $deliveryFee = !empty($GetOrderDetailByOrderNo['deliveryFee'])?$GetOrderDetailByOrderNo['deliveryFee']:'';
                            $Tips = $v["Tips"];
                        } else {
                            $orderAmount = $v['orderAmount'];
                            $orderTaxAmount = $v['orderTaxAmount'];
                            $discount = $v['discount'];
                            $deliveryFee = $v["deliveryFee"];
                            $Tips = $v["Tips"];
                        }
                    }
                    $data['dine']['dine_name'] = $merchantName;
                    $data['dine']['total_tax'] = number_format($orderTaxAmount, 2);
                    $data['dine']['discount'] = number_format($discount, 2);
                    $data['dine']['delivery_charges'] = number_format($deliveryFee, 2);
                    $data['dine']['delivery_tip'] = number_format($Tips, 2);
                    $data['dine']['total'] = number_format($Dine_TotalAmount, 2);
                    $data['dine']['total_quantity'] = count($DineRecords);
                }
            } else {
                $data['dine']['item'] = 0;
            }
            // End Get Dine Details
            //print_r($Transactions);
            // Get other Details

            if ($Transactions[0] != '') {
                // $data['parking']['detail']=array();
                $Records = GetTransactionReport($Transactions[0]);
                // print_r($Records); die;
                foreach ($Records[0] as $k => $v) {
                    $points_earned = $points_earned + $v['points'];
                }
                $totalDiscount = $park_total_tax = 0;
                foreach ($Records[0] as $k => $v) {
                    $temp_array = "";
                    $Discount = $v['Discount'];
                    $totalDiscount+=$Discount;
                    if ($v['Parking_ID'] > 0) {
                        $cat = 45;
                        $Item_ID = $v['Parking_ID'];
                        $Service_Title = GetServiceTitle($Item_ID, $cat);
                        $title = $Service_Title[0];
                        $OverAllTotalAmount+=$v['TotalAmount'] - $v['charges'];
                        $park_total_tax+=$v['charges'];
                        $Order_No = "PA" . $v['TxnID'];
                        $temp_array['park_name'] = $title;
                        $temp_array['order_id'] = $Order_No;
                        $temp_array['type'] = 'Parking';
                        $temp_array['amount'] = $v['TotalAmount'] - $v['charges'];
                        $temp_array['charges'] = $v['charges']; //ravi
                        $temp_array['tax'] = round($v['charges'], 2);
                        // $temp_array['tax'] = round((($v['TotalAmount'])*$tax/100),2);
                        $temp_array['discount'] = $Discount;
                        $temp_array['Payat_Lot'] = number_format($v['Payat_Lot'], 2);
                        $temp_array['Total_payat_lot_paid'] = number_format($v['TotalAmount'], 2);
                        $temp_array['check_in'] = date("m/d", strtotime($v['from_date'])) . ' @ ' . date("g:i A", strtotime($v['from_date']));
                        $temp_array['check_out'] = date("m/d", strtotime($v['to_date'])) . ' @ ' . date("g:i A", strtotime($v['to_date']));
                        $data['parking']['detail'][] = $temp_array;
                        $fromdateAvailability = sanitize($v['from_date']);
                        $todateAvailability = sanitize($v['to_date']);
                        $parkingidForSpaces = sanitize($v['Parking_ID']);
                        $sql_main_parking="SELECT P_ID  FROM `tbl_parking` tp where tp.P_Location = (select tp1.P_Location from `tbl_parking` tp1 where tp1.P_ID = '".$parkingidForSpaces."') AND (tp.P_Pricingtype = 'daily') ORDER BY tp.`P_ID`";
                        $result_main_parking = mysqli_query($con, $sql_main_parking);
                        $res_main_parking=mysqli_fetch_assoc($result_main_parking);
                        descreaseSpacesByPid($fromdateAvailability, $todateAvailability, $res_main_parking['P_ID']);
                        //UpdateParkingQntyWithOrderID($v['TxnID']);
                    }
                }

                //	$total_tax= round((($OverAllTotalAmount)*$tax/100),2);
                //$data['parking']['total_tax'] =number_format($total_tax,2);
                $data['parking']['total_tax'] = number_format($park_total_tax, 2);
                $data['parking']['discount'] = number_format($totalDiscount, 2);
                $data['parking']['sub_total'] = number_format($OverAllTotalAmount, 2);
                $data['parking']['total'] = number_format($OverAllTotalAmount + $park_total_tax, 2);
            }
            // End other Details
            $promocode_discount = 0;
            $promocode = 0;
            if (sanitize($_REQUEST['promo_id']) != '') {
                $promo_id = sanitize($_REQUEST['promo_id']);
                $promo_upsql = "SELECT * from tbl_promocode WHERE PMC_PromoID = $promo_id AND PMC_IsActive = '1' LIMIT 1";
                $promo_query = mysqli_query($con, $promo_upsql);
                $rec = mysqli_fetch_assoc($promo_query);
                $promocode_discount = $rec['PMC_DiscountAmount'];
                $promocode = $rec['PMC_PromoCode'];
            }
            $tax_total = $data['dine']['total_tax'] + $data['parking']['total_tax'];
            $sub_total = $data['dine']['total'] + $data['parking']['sub_total'];
            $grand_total = $sub_total + $tax_total;
            $data['promocode_discount'] = number_format($promocode_discount, 2);
            $data['promocode'] = $promocode;
            $data['sub_total'] = number_format($sub_total, 2);
            $data['tax_total'] = number_format($tax_total, 2);
            $data['grand_total'] = number_format($grand_total - $promocode_discount, 2);
            $data['total_discount'] = number_format($data['dine']['discount'] + $data['parking']['discount'] + $promocode_discount, 2);
            $data['earned_way_bucks'] = number_format($points_earned, 2);
            $data['total_way_bucks'] = GetAvailablePoints($User_ID);
            //Ravi adding credit card for display purpose only
            $data['cc_num'] = base64_encode($encrypted_card_number);

            /* ========================================= End Bindra Changes ==================================================== */
            $content = array("status" => "1", "message" => "success", "data" => $data, "rep" => $rep, "resp" => $TransactionResult, "TxnID" => $Payment_Id, "PTxnID" => $PTI_TID, "err" => $ErrorMessage);
            // echo json_encode($content,JSON_FORCE_OBJECT);
            echo json_encode($content);
            exit;
        } else {
            //Insert First Data values into Database
            if (getenv(HTTP_X_FORWARDED_FOR)) {
                $visit_ip = getenv(HTTP_X_FORWARDED_FOR);
            } else {
                $visit_ip = getenv(REMOTE_ADDR);
            }
            if ($CalculatedTax == '')
                $CalculatedTax = 0;
            if ($CalculatedShipping == '')
                $CalculatedShipping = 0;
            if ($OrderID == '')
                $OrderID = 0;
            if ($Payment_Id == '')
                $Payment_Id = 0;
            $sql = "INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('" . $Payment_Id . "','" . $User_ID . "','" . sanitize($_REQUEST['orderAmount']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Type']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_FirstName']) . "',AES_ENCRYPT('" . $ResCC['CC_NUMBER'] . "','" . $EncryptKey . "'),'" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Month']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Exp_Year']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Street']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_State']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_City']) . "','" . mysqli_real_escape_string($con, $ResCC['Card_Zip']) . "','" . $CommercialServiceProvider . "','" . $TransactionTime . "','" . $TransactionID . "','" . $ProessorReferenceNumber . "','" . $ProcessorResponseMessage . "','" . $ErrorMessage . "','" . $OrderID . "','" . $ApprovalCode . "','" . $AVSResponse . "','" . $TDate . "','" . $TransactionResult . "','" . $ProcessorApprovalCode . "','" . $CalculatedTax . "','" . $CalculatedShipping . "','" . $TransactionScore . "','" . $AuthenticationResponseCode . "',Now(),'" . $visit_ip . "')";
            mysqli_query($con, $sql);
            $content = array("status" => 0, "message" => "Declined", "rep" => $rep, "resp" => $TransactionResult, "err" => $ErrorMessage);
            echo json_encode($content);
            exit;
        }
    }
}
else {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
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
    $DBSERVER = DB_SERVER;
    $DBUSER = DB_USERNAME;
    $DBPWD = DB_PASSWORD;
    $DBDATABASE = DB_DATABASE;
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

?>
