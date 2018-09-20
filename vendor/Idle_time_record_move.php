<?php
	require_once('config.php');
	GLOBAL $con;

  // get the HTML
  $currentdate = !empty($_GET['currentdate']) ? date('Y-m-d H:i:s', strtotime($_GET['currentdate'])) : date('Y-m-d');
  $query = "SELECT tsas.*, pl.Park_Name FROM tbl_shuttle_active_status tsas"
        . " INNER JOIN tbl_parking p ON p.P_ID = tsas.SAS_PatkingID"
        . " INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID"
        . " where SAS_ShuttleActive = '0' and SAS_ShuttleActive_LastTime_1Hour <= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $res = mysqli_query($con, $query) or die("Error");
  $rowCount = mysqli_num_rows($res);
  if (!empty($rowCount)) {
    $messageDetail = '';
    $updateParkingIds = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $messageDetail .= ' Parking ID: '.$row['SAS_PatkingID'].' Parking Name: '.$row['Park_Name'].' <br/> ';
        $updateParkingIds .= $row['SAS_PatkingID'].',';
    }
  }
}

?>
