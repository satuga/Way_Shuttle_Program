<?php 
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = $data['data']['driver_id'];
$latitude = $data['data']['driver_lat'];
$longitude = $data['data']['driver_long'];
$status = $data['data']['driver_status'];

echo "DD";
exit;
if($driver_id != '' && $latitude != '' && $longitude != '')
{
	$output=array("status"=>"1","data"=>"hello");
	echo json_encode($output);exit;
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}


?>