<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$device_type = $data['data']['device_type'];
$access_token = $data['data']['access_token'];

if($device_type != '' && $access_token != '' && $user_id != '')
{
	$user_info = "delete from tbl_user_device where user_id='".$user_id."' and access_token = '".$access_token."'";
 	$info_query = mysqli_query($con,$user_info);
	$output=array("status"=>"1","data"=>'User logout');
	echo json_encode($output);
	exit;
}
else 
{
	$output=array("status"=>"0","data"=>"Please add correct data");
	echo json_encode($output);exit;
}


?>