<?php

error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
$DBSERVER = DB_SERVER;
$DBUSER = DB_USERNAME;
$DBPWD = DB_PASSWORD;
$DBDATABASE = DB_DATABASE;
include ('../function.php');
include ('./DataAccessLayer/ParkingManagerDA.php');
$objDA = new ParkingManagerDA();
//----- Required parameters which are required to create the query for searching ------//
$lat = '1';//sanitize($_REQUEST['lat']);
$long = '1';//sanitize($_REQUEST['long']);
$keywords = sanitize($_REQUEST['keyword']);

//----- check if the main parameters lat , long are present or not ------//
if ($lat == '' || $long == '') {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    $Events = $objDA->GetNearestEvents($lat, $long, $keywords);
    $count = count($Events);
    if ($count > 0){
        $content = array("status" => 1, "count" => $count, "data" => $Events);
        echo json_encode($content);
        exit;
    } else {
        $content = array("status" => 0, "response" => ERROR, "message" => 'No Records Found');
        echo json_encode($content);
        exit;
    }
}
?>
