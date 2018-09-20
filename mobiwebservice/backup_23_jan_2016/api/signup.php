<?php
include ('config.php');
include ('function.php');
header('Content-Type: application/json');

if(isset($_REQUEST['fullname'])){
  $fullname = $_REQUEST['fullname'];
} else {
  $fullname = "";
}

if(isset($_REQUEST['phone'])){
  $mobile = $_REQUEST['phone'];
} else {
  $mobile = "";
}

$email = $_REQUEST['email'];
$password = $_REQUEST['password'];

$device_type = $_REQUEST['device_type'];
$access_token = $_REQUEST['access_token'];

$parameter = $email . ':' . $password;
$response = chk_parameter($parameter, 2);
//echo "resp".$response;

if ($response == 0)
{
    $content = array("status" => "0","response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
    //chk duplicat
    $chk = mysql_query("select id from tbl_registeration where email_add ='".$email ."' and facebook=0");
    if (mysql_num_rows($chk))
    {
        $content = array("status" => "0","response" => ERROR, "message" => "Email id " . $email ." already registered!");
        echo json_encode($content);
        exit;
    }
    else
    {
        $EncryptKey = ENCRYPTKEY;
        $chk_sum = checksum();
        $sql="insert into tbl_registeration (firstname,mobile_phone,display_name,email_add,encrypt_password,cdate,checksum_register,status)values('".$fullname."','".$mobile."','".$fullname."','".$email."',AES_ENCRYPT('" . $password . "','" . $EncryptKey ."'),now(),'".$chk_sum."','0')";
        $res=mysql_query($sql);
        $id = mysql_insert_id();

        // Company table
        $sql11 = "insert into companies set id='" . $id . "',email='" . $email ."',joinedOn=now()";
        mysql_query($sql11);

        // Alias Email, Address
        $reguser = explode("@", $email);
        $aliasemail = $reguser[0] . $id . "@members.way.com";

        $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail ."' WHERE id =" . $id;
        mysql_query($sql);

        //---------------- Device token -------------*/
        $query_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        mysql_query($query_device);


        if ($id)
        {
            Sendmail($email,$id,$chk_sum);
            $content = array("status" => "1","response" => SUCCESS, "message" => SUCCESS_MSG,"id"=>$id);
            echo json_encode($content);
            exit;
        }
        else
        {
            $content = array("status" => "0","response" => ERROR, "message" => ERROR_MSG);
            echo json_encode($content);
            exit;
        }
    }
}

?>
