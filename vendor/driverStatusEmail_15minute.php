<?php
	//require_once("E:\Apache24\htdocs\responsive\vendor\config.php");
	require_once('config.php');
	GLOBAL $con;



// get the HTML
$currentdate = !empty($_GET['currentdate']) ? date('Y-m-d H:i:s', strtotime($_GET['currentdate'])) : date('Y-m-d');

/* For 15 minute */
$query = "SELECT tdas.*, r.firstname, r.lastname, r.email_add FROM tbl_shuttle_driver_active_status tdas "
			 . " INNER JOIN tbl_registeration r ON r.id = tdas.SAS_DriverID"
			 . " where SAS_DriverActiveStatus = '0' and SAS_DriverActive_LastTime <= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
/*$query = "SELECT tdas.*, r.firstname, r.lastname, r.email_add FROM tbl_shuttle_driver_active_status tdas "
        . " INNER JOIN tbl_registeration r ON r.id = tdas.SAS_DriverID"
        . " where SAS_DriverActiveStatus = '0'"; */

$res = mysqli_query($con, $query) or die("Error");
$rowCount = mysqli_num_rows($res);

if (!empty($rowCount)) {
    $messageDetail = '';
    $updateDriverIds = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $messageDetail .= ' Driver ID: '.$row['SAS_DriverID'].', Driver Name: '.$row['firstname'].' '.$row['lastname'].', Driver EmailID: '.$row['email_add'].'<br/>';
        $updateDriverIds .= $row['SAS_DriverID'].',';
    }
   	$mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ashwin.kannan@way.com';
    $subject = 'Check driver Offline Status - ' . $currentdate;
    $message = 'Driver offline at this moment: ';
    $message .= '<br/><br/>'.$messageDetail;
    $updateDriverIds = rtrim($updateDriverIds, ',');
    sendmail_driverInactiveStatus($mailto, $subject, $message, $updateDriverIds);
}


function sendmail_driverInactiveStatus($mailto, $subject, $message, $updateDriverIds) {
    GLOBAL $con;
    $email_from = "support@way.com"; // Who the email is from
    $headers = "From: ".$email_from."\r\n" .
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    if(mail($mailto,$subject, $message, $headers)){
        $query = "update tbl_shuttle_driver_active_status set SAS_DriverActive_LastTime = '".date('Y-m-d H:i:s')."' where SAS_DriverID IN (".$updateDriverIds.")";
        $res = mysqli_query($con, $query) or die("Error");
    }
}

?>
