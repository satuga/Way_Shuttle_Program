<?php

    error_reporting(0);
    header('Content-Type: application/json');
    include ('../config.php');
    include ('../function.php');

$content=[];
$lat = sanitize($_REQUEST['lat']);
$long = sanitize($_REQUEST['long']);

if ($lat == '' || $long == '') {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}


























?>
