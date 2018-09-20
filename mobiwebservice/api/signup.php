<?php

header('Content-Type: application/json');
include ('config.php');
include ('function.php');
$fullname = isset($_REQUEST['fullname']) ? sanitize($_REQUEST['fullname']) : '';
$mobile = isset($_REQUEST['phone']) ? sanitize($_REQUEST['phone']) : '';
$email = isset($_REQUEST['email']) ? sanitize($_REQUEST['email']) : '';
$password = sanitize($_REQUEST['password']);
$device_type = sanitize($_REQUEST['device_type']);
$access_token = sanitize($_REQUEST['access_token']);
$fullnameArray = explode(' ', $fullname);
if ($email == '' && $password == '') {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    //chk duplicat
    $chk = mysqli_query($con, "select id from tbl_registeration where email_add ='" . $email . "'");
    if (mysqli_num_rows($chk)) {
        $content = array("status" => "0", "response" => ERROR, "message" => "Email id " . $email . " already registered!");
        echo json_encode($content);
        exit;
    } else {
        $EncryptKey = ENCRYPTKEY;
        $chk_sum = checksum();
        if (!empty($fullnameArray[0]) && !empty($fullnameArray[1])) {
            $sql = "insert into tbl_registeration (firstname,lastname,mobile_phone,display_name,email_add,encrypt_password,cdate,checksum_register,status)values('" . $fullnameArray[0] . "','" . $fullnameArray[1] . "','" . $mobile . "','" . $fullname . "','" . $email . "',AES_ENCRYPT('" . $password . "','" . $EncryptKey . "'),now(),'" . $chk_sum . "','0')";
        } else {
            $sql = "insert into tbl_registeration (firstname,mobile_phone,display_name,email_add,encrypt_password,cdate,checksum_register,status)values('" . $fullname . "','" . $mobile . "','" . $fullname . "','" . $email . "',AES_ENCRYPT('" . $password . "','" . $EncryptKey . "'),now(),'" . $chk_sum . "','0')";
        }
        $res = mysqli_query($con, $sql);
        $id = mysqli_insert_id($con);

        // Company table
        $sql11 = "insert into companies set id='" . $id . "',email='" . $email . "',joinedOn=now()";
        mysqli_query($con, $sql11);

        // Alias Email, Address
        $reguser = explode("@", $email);
        $aliasemail = $reguser[0] . $id . "@members.way.com";

        $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail . "' WHERE id =" . $id;
        mysqli_query($con, $sql);

        //---------------- Device token -------------*/
        $query_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('" . $id . "','" . $device_type . "','" . $access_token . "',now())";
        mysqli_query($con, $query_device);


        if ($id) {
            Sendmail($email, $id, $chk_sum);
            $content = array("status" => "1", "response" => SUCCESS, "message" => SUCCESS_MSG, "id" => $id);
            echo json_encode($content);
            exit;
        } else {
            $content = array("status" => "0", "response" => ERROR, "message" => ERROR_MSG);
            echo json_encode($content);
            exit;
        }
    }
}
?>
