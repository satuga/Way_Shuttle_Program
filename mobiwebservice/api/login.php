<?php
header('Content-Type: application/json');
//error_reporting(1);
include ('config.php');
include ('function.php');
$EncryptKey = ENCRYPTKEY;
$email = sanitize($_REQUEST['email']);
$content = array("status" => "0", "response" => ERROR, "message" => ERROR_MSG);
print_r($content);
if ($email == "") {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    $access_token = sanitize($_REQUEST['access_token']);
    $device_id = sanitize($_REQUEST['device_id']);
    $isfbuser = sanitize($_REQUEST['isfbuser']);
    $facebook_id = sanitize($_REQUEST['facebook_id']);
    $password = sanitize($_REQUEST['password']);
    $fullname = sanitize($_REQUEST['fullname']);
    $isFingerprint = sanitize($_REQUEST['isFingerprint']);
    if ($isfbuser == 1) {
        // for facebook  login
        ///$query = "select * from tbl_registeration where facebook_id='".$facebook_id."'";
        $query = "select * from tbl_registeration where facebook=1 and email_add='" . $email . "'";
        $res = mysqli_query($con, $query);
        $count = mysqli_num_rows($res);

        if ($count > 0) {
            // update the records
            //$qry="update tbl_registeration set last_login=now() where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'";
            $qry = "update tbl_registeration set last_login=now() where email_add='" . $email . "' and encrypt_password='' and facebook='1'";
            $update = mysqli_query($con, $qry);
            $data = mysqli_fetch_array($res);

            $fullname = $data['firstname'];
            $lastname = $data['lastname'];
            $school = $data['school'];
            $displayname = $data['display_name'];
            $email = $data['email_add'];
            $id = $data['id'];
        } else {
            // check already email exit
            $query = "select * from tbl_registeration where email_add='" . $email . "'";
            $res = mysqli_query($con, $query);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
                $content = array("status" => "0", "response" => ERROR, "message" => 'This email ' . $email . ' is already exist');
                echo json_encode($content);
                exit;
            }
            if ($count > 0) {
                // update the records
                $update = mysqli_query($con, "update tbl_registeration set last_login=now() where email_add='" . $email . "' and encrypt_password='' and facebook_id='' and  google_id='" . $facebook_id . "'");
                $data = mysqli_fetch_array($res);
                $fullname = $data['firstname'];
                $lastname = $data['lastname'];
                $school = $data['school'];
                $displayname = $data['display_name'];
                $email = $data['email_add'];
                $id = $data['id'];
            }
            // records are not exits so create new one.
            $chk_sum = checksum();
            $sql = "insert into tbl_registeration (firstname,display_name,email_add,encrypt_password,cdate,checksum_register,status,facebook,facebook_id)values('" . $fullname . "','" . $fullname . "','" . $email . "','',now(),'" . $chk_sum . "','1','1','" . $facebook_id . "')";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);

            // Company table
            $sql11 = "insert into companies set id='" . $id . "',email='" . $email . "',joinedOn=now()";
            mysqli_query($con, $sql11);

            // Alias Email, Address
            $reguser = explode("@", $email);
            $aliasemail = $reguser[0] . $id . "@members.way.com";

            $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail . "' ,last_login=now(),device_type = '" . $device_type . "',access_token = '" . $access_token . "' WHERE id =" . $id;
            mysqli_query($con, $sql);
        }
    } elseif ($isfbuser == 2) {
        // For google + login
        $query = "select * from tbl_registeration where email_add='" . $email . "'";
        $res = mysqli_query($con, $query);
        $count = mysqli_num_rows($res);

        if ($count > 0) {
            // update the records
            $update = mysqli_query($con, "update tbl_registeration set last_login=now() where email_add='" . $email . "' and encrypt_password='' and facebook_id='' and  google_id='" . $facebook_id . "'");
            $data = mysqli_fetch_array($res);
            $fullname = $data['firstname'];
            $lastname = $data['lastname'];
            $school = $data['school'];
            $displayname = $data['display_name'];
            $email = $data['email_add'];
            $id = $data['id'];
        } else {
            // records are not exits so create new one.
            $chk_sum = checksum();
            $sql = "insert into tbl_registeration (firstname,display_name,email_add,encrypt_password,cdate,checksum_register,status,google_id)values('" . $fullname . "','" . $fullname . "','" . $email . "','',now(),'" . $chk_sum . "','1','" . $facebook_id . "')";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);

            // Company table
            $sql11 = "insert into companies set id='" . $id . "',email='" . $email . "',joinedOn=now()";
            mysqli_query($con, $sql11);

            // Alias Email, Address
            $reguser = explode("@", $email);
            $aliasemail = $reguser[0] . $id . "@members.way.com";

            $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail . "' ,last_login=now(),device_type = '" . $device_type . "',access_token = '" . $access_token . "' WHERE id =" . $id;
            mysqli_query($con, $sql);
        }
    } else {
        // For normal login
        if(strtolower($isFingerprint) == 'yes'){
            $query = "select * from tbl_registeration where email_add='" . $email . "' and isFingerprint = 1";
        } else {
            $query = "select * from tbl_registeration where email_add='" . $email . "' and encrypt_password=AES_ENCRYPT('" . $password . "','" . ENCRYPTKEY . "')";
        }
        $res = mysqli_query($con, $query);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $data = mysqli_fetch_array($res);
            if ($data['status'] == 1) {
                $fullname = $data['firstname'];
                $lastname = $data['lastname'];
                $school = $data['school'];
                $displayname = $data['display_name'];
                $email = $data['email_add'];
                $id = $data['id'];
            } else {
                $content = array("status" => "0", "response" => ERROR, "message" => 'Please activate your account');
                echo json_encode($content);
                exit;
            }
        } else {
            $content = array("status" => "0", "response" => ERROR, "message" => INCORRECT_MSG);
            echo json_encode($content);
            exit;
        }
    }

    if ($id != '') {
        //--------------- check device type and accesstoken for the user ---------------//
        $sql_device = "select * from tbl_user_device where user_id=" . $id . " AND access_token = '" . $access_token . "'";
        $exe_device = mysqli_query($con, $sql_device);
        $num_device = mysqli_num_rows($exe_device);
        if ($num_device < 0) {
            $del_ext_devices = mysqli_query($con, "delete from tbl_user_device where access_token = '" . $access_token . "'");
            $ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('" . $id . "','" . $device_type . "','" . $access_token . "',now())";
            mysqli_query($con, $ins_device);
        }
        //------------------------ End of the device token ---------------//

        $sql_add = "select * from tbl_deliveryaddress where DE_UserID='" . $id . "' AND DE_default = '1'";
        $exe_add = mysqli_query($con, $sql_add);
        $num_add = mysqli_num_rows($exe_add);
        if ($num_add > 0) {
            while ($add1 = mysqli_fetch_assoc($exe_add)) {
                $address[] = $add1;
            }
            $address_all = array("DE_ID" => $address[0]['DE_ID'], "DE_Name" => $address[0]['DE_Name'], "DE_Address" => $address[0]['DE_Address'], "BLDG_No" => $address[0]['BLDG_No'], "DE_State" => $address[0]['DE_State'], "DE_City" => $address[0]['DE_City'], "DE_Zipcode" => $address[0]['DE_Zipcode'], "DE_Phone" => $address[0]['DE_Phone'], "DE_lat" => $address[0]['DE_lat'], "DE_long" => $address[0]['DE_long'], "DE_default" => $address[0]['DE_default']);
        } else {
            $address_all = "";
        }
        //------------------------ getting card details ---------------//
        $sql2 = "select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No from tbl_creditcarddetails where Card_User_ID=" . $id . " ORDER BY Card_Created ASC";
        $exe2 = mysqli_query($con, $sql2);
        $num2 = mysqli_num_rows($exe2);
        if ($num2 > 0) {
            $flag = 0;
            while ($res2 = mysqli_fetch_array($exe2)) {
                //------------------------ make one card default if any not ---------------//
                if ($res2['Card_Default'] == 1)
                    $flag = 1;
                $card_array[] = array("card_id" => $res2['Card_ID'], "Card_Type" => $res2['Card_Type'], "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $res2['Card_Exp_Year'],
                    "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                    "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Card_Default" => $res2['Card_Default']);
            }
            if ($flag == 0) {
                $card_id = $card_array[0]['card_id'];
                $sql2 = "update tbl_creditcarddetails set Card_Default=1 where Card_ID=$card_id";
                if (mysqli_query($con, $sql2))
                    $card_id = $card_array[0]['Card_Default'] = 1;
            }
        }
        else {
            $card_array = array();
        }

        //------------------------ update the cart ---------------//
        if ($device_id != '') {
            $update_cart = mysqli_query($con, "update tbl_cart set Cart_UserID = '" . $id . "' where Sess_ID = '" . $device_id . "'");
            $update_cart = mysqli_query($con, "update tbl_cart set Sess_ID='' where Cart_UserID = '" . $id . "'");
        }
        //------------------------ delete the cart if multiple dine Records exist ---------------//
        $DineRecords = array();
        //  $sql="select Owner_Restaurant from tbl_cart where Cart_Type 'Dine' and Cart_UserID='".$id."' order by Cart_Created ASC";
        $sql = "select Owner_Restaurant,Cart_Created from tbl_cart where Cart_Type ='Dine' and Cart_UserID= '" . $id . "' group by Owner_Restaurant order by Cart_Created  desc";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res)) {
            while ($rec = mysqli_fetch_array($res)) {
                $DineRecords[] = $rec;
            }
        }
        if (!empty($DineRecords) && count($DineRecords) > 1) {
            $Owner_Restaurant = $DineRecords[0]['Owner_Restaurant'];
            $qry = "delete from tbl_cart where Cart_Type ='Dine' and Cart_UserID= '" . $id . "' and Owner_Restaurant<>'" . $Owner_Restaurant . "'"; // die;
            mysqli_query($con, $qry);
        }
        //------------------------ Get total cart items before login ---------------//
        $sql_count = "select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Cart_UserID='" . $id . "'";
        $query_count = mysqli_query($con, $sql_count);
        $query_count_re = mysqli_num_rows($query_count);
        if ($query_count_re > 0) {
            $counts = mysqli_fetch_array($query_count);
            $total_cart = $counts['total_cart'];
        }

        $sql_driver_status = "UserID='" . $id . "'";
        $query_driver_status = mysqli_query($con, $sql_driver_status);
        $query_driver_status_re = mysqli_num_rows($query_driver_status);
        if ($query_driver_status_re > 0){        
            $driver_detail = mysqli_fetch_array($query_driver_status);
            $status_driver_detail = $driver_detail['total_cart'];
        }



        $is_driver = 0;
        $driver_id = 0;
        $driverInfo = isDriver($id);
        if (!empty($driverInfo)) {
            $is_driver = 1;
            $driver_id = $driverInfo['DVR_DriverID'];
        }


        //Sendmail($email,$id,$chk_sum);
        $content = array(
            "status" => "1",
            "response" => SUCCESS,
            "message" => SUCCESS,
            "userid" => $id,
            "fullname" => $fullname,
            "school" => $school,
            "isFingerprint" => $data['isFingerprint'],
            "city" => $data['city'],
            "state" => $data['state'],
            "birthday" => $data['birthday'],
            "street" => $data['street'],
            "zipcode" => $data['zipcode'],
            "building" => $data['BLDG_No'],
            "dine_driver" => $data['dine_driver'],
            "push_notification" => $data['push_notification'],
            "mobile_phone" => $data['mobile_phone'],
            "gender" => $data['gender'],
            "Lastname" => $lastname,
            "displayname" => $data['display_name'],
            "email" => $email,
            "default_address" => $address_all,
            "logo" => $data['logo'],
            "total_cart" => $total_cart,
            "is_driver" => (string) $is_driver,
            "driver_id" => (string) $driver_id,
            "card_array" => $card_array);

        echo json_encode($content);
        exit;
    }
}
?>
