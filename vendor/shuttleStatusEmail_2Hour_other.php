<?php
	//require_once("E:\Apache24\htdocs\responsive\vendor\config.php");
	require_once('config.php');
	GLOBAL $con;



// get the HTML
$currentdate = !empty($_GET['currentdate']) ? date('Y-m-d H:i:s', strtotime($_GET['currentdate'])) : date('Y-m-d');

/* For 2 Hour */
$query = "SELECT tsas.*, pl.Park_Name FROM tbl_shuttle_active_status tsas "
        . " INNER JOIN tbl_parking p ON p.P_ID = tsas.SAS_PatkingID"
        . " INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID"
        . " where SAS_ShuttleActive = '0' and SAS_ShuttleActive_LastTime_2Hour <= DATE_SUB(NOW(), INTERVAL 2 HOUR)";

/*$query = "SELECT tsas.*, pl.Park_Name FROM tbl_shuttle_active_status tsas "
        . " INNER JOIN tbl_parking p ON p.P_ID = tsas.SAS_PatkingID"
        . " INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID"
        . " where SAS_ShuttleActive = '0'";*/

$res = mysqli_query($con, $query) or die("Error");

$rowCount = mysqli_num_rows($res);
if (!empty($rowCount)) {
    $messageDetail = '';
    $updateParkingIds = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $messageDetail .= ' Parking ID: '.$row['SAS_PatkingID'].' Parking Name: '.$row['Park_Name'].' <br/> ';
        $updateParkingIds .= $row['SAS_PatkingID'].',';
    }

    //$mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ujash.letsnurture@gmail.com';
    //$mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'jeba.raj@way.com';
    $mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'mohinder.singh@way.com';
    $subject = 'Check shuttle Offline Status - ' . $currentdate;
    $message = 'Parking lots offline at this moment: ';
    $message .= '<br/><br/>'.$messageDetail;
    $updateParkingIds = rtrim($updateParkingIds, ',');
    sendmail_2Hour($mailto, $subject, $message, $updateParkingIds);
}


function sendmail_2Hour($mailto, $subject, $message, $updateParkingIds) {
    GLOBAL $con;
    $email_from = "support@way.com"; // Who the email is from
    $headers = "From: ".$email_from."\r\n" .
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    if(mail($mailto,$subject, $message, $headers)){
        $query = "update tbl_shuttle_active_status set SAS_ShuttleActive_LastTime_2Hour = '".date('Y-m-d H:i:s')."' where SAS_PatkingID IN (".$updateParkingIds.")";
        $res = mysqli_query($con, $query) or die("Error");
    }
}

?>
