<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$latitude = $data['data']['driver_lat'];
$longitude = $data['data']['driver_long'];
$status = $data['data']['driver_status'];

if($user_id != '' && $latitude != '' && $longitude != '')
{
	$Query1="INSERT INTO driver_status(user_id,driver_lat,driver_long,driver_status) VALUES ('".$user_id."',".$latitude.",".$longitude.",'".$status."')";
	mysql_query($Query1);
	$output=array("status"=>"1","data"=>"Driver online successfully");
	echo json_encode($output);exit;
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}


?>