<?php

//require_once("E:\Apache24\htdocs\responsive\vendor\config.php");
require_once('config.php');
GLOBAL $con;



// get the HTML
$currentdate = !empty($_GET['currentdate']) ? date('Y-m-d H:i:s', strtotime($_GET['currentdate'])) : date('Y-m-d');


$query = "SELECT tsas.*, pl.Park_Name FROM tbl_shuttle_active_status tsas "
        . " INNER JOIN tbl_parking p ON p.P_ID = tsas.SAS_PatkingID"
        . " INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID"
        . " where SAS_ShuttleActive = '0' and SAS_ShuttleActive_LastTime <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)";

$res = mysqli_query($con, $query) or die("Error");

$rowCount = mysqli_num_rows($res);
if (!empty($rowCount)) {
    $messageDetail = '';
    $updateParkingIds = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $messageDetail .= ' Parking ID: ' . $row['SAS_PatkingID'] . ' Parking Name: ' . $row['Park_Name'] . ' <br/> ';
        $updateParkingIds .= $row['SAS_PatkingID'] . ',';
        $getSelectedVendorEmail = getSelectedVendorEmail($row['SAS_PatkingID']);
        if (!empty($getSelectedVendorEmail)) {
            sendVendorEmail($getSelectedVendorEmail);
        }
    }

    //$mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ujash.letsnurture@gmail.com';
    //$mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'jeba.raj@way.com';
    $mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ashwin.kannan@way.com, mohinder.singh@way.com, ujash.joshi@letsnurture.com';
    $subject = 'Check shuttle Offline Status - ' . $currentdate;
    $message = 'Check shuttle Offline Parking lot since half hour: ';
    $message .= '<br/><br/>' . $messageDetail;
    $updateParkingIds = rtrim($updateParkingIds, ',');
    sendmail($mailto, $subject, $message, $updateParkingIds);
}

function sendmail($mailto, $subject, $message, $updateParkingIds) {
    GLOBAL $con;
    $email_from = "support@way.com"; // Who the email is from
    $headers = "From: " . $email_from . "\r\n" .
            'Reply-To: ' . $email_from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    //if(mail($mailto,$subject, $message, $headers)){
    $query = "update tbl_shuttle_active_status set SAS_ShuttleActive_LastTime = '" . date('Y-m-d H:i:s') . "' where SAS_PatkingID IN (" . $updateParkingIds . ")";
    $res = mysqli_query($con, $query) or die("Error");
    //}
}

function getSelectedVendorEmail($parkingId) {

    $array = array(
      "575"=>"lisa.sinatra@hertz.com",
      "3321"=>"albertcrz@yahoo.com",
      //"2152"=>"johnd@gatewayglobalsf.com",
      "2036"=>"riaz.ramada5010@gmail.com",
      "1683"=>"mgambardella@Comfortinnboston.com",
      "1220"=>"TMcFadden@ParkPCA.com",
      "878"=>"prasad@tlrgc.com",
      "844"=>"rabdala@spplus.com",
      "684"=>"info@peachyairportparking.com",
      "589"=>"info@fourpointsphoenixsouth.com",
      "586"=>"jim@paylessri.com",
      //"516"=>"ashwin.kannan@way.com",
      "255"=>"customerservice@park2go.com"
      );

    return !empty($array[$parkingId]) ? $array[$parkingId] : array();
}

function sendVendorEmail($mailto) {
    $subject = 'Check shuttle Offline Status - ';
    $message = "<br/>Attention Site Manager:<br/>
Customers waiting at the airport have notified us that they are unable to see your shuttle(s) using the WAY app. One or more of your iPads currently on your shuttle(s) is \"Offline\".
<br/><br/>  
A couple steps to help you reconnect:
<br/>
Make sure your iPad is logged into the WAY app;
<br/>
App should be in 'Driver Mode';
<br/><br/>
Check that the iPad has not run out of battery.
<br/> <br/> 
If you still require assistance or have questions please connect with your WAY Regional representative:
<br/>
Ashwin (ashwin.kannan@way.com) - Technical Assistance
<br/> 
Mo (2098151139/Mohinder.Singh@way.com) - Program Coordinator
<br/> <br/> 
 
<strong>Please do not reply to this email</strong><br/> ";

    $email_from = "support@way.com"; // Who the email is from
    $headers = "From: " . $email_from . "\r\n" .
            'Reply-To: ' . $email_from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    mail($mailto, $subject, $message, $headers);
}

?>
