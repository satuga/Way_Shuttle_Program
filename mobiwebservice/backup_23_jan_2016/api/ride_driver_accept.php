<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$driver_id = $data['data']['driver_id'];
$order_id = $data['data']['order_id'];
$latitude = $data['data']['driver_lat'];
$longitude = $data['data']['driver_long'];
$start_journey = $data['data']['start_journey'];
$time  = date('Y-m-d H:i:s');

if($user_id != '' && $driver_id != '' && $latitude != '' && $longitude != '')
{
	$Query1="INSERT INTO driver_accept_ride(driver_id,user_id,order_id,driver_lat,driver_long,start_journey,created_at) VALUES ('".$driver_id."','".$user_id."','".$order_id."',".$latitude.",".$longitude.",'".$start_journey."','".$time."')";
	mysql_query($Query1);
	$output=array("status"=>"1","data"=>"Driver Accepted your order");
	echo json_encode($output);exit;
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
