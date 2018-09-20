<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = sanitize($data['data']['driver_id']);
$ids = sanitize($data['data']['order_id']);
$latitude = sanitize($data['data']['driver_lat']);
$longitude = sanitize($data['data']['driver_long']);

$ord = explode(",",$ids);

if($driver_id != '' && $latitude != '' && $longitude != '')
{
	for($i=0;$i<sizeof($ord);$i++)
	{
		$order_id = $ord[$i];
		$Query1="INSERT INTO driver_ride_path(driver_id,order_id,driver_lat,driver_long) VALUES ('".$driver_id."','".$order_id."',".$latitude.",".$longitude.")";
		mysqli_query($con,$Query1);	
	}
	
	$output=array("status"=>"1","data"=>"latitude longitude added");
	echo json_encode($output);exit;
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
?>