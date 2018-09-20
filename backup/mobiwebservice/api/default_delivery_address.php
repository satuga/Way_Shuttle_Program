<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);

$user_id = $data['data']['user_id'];
$add_id = $data['data']['address_id'];
$default = $data['data']['default_val'];


$sql="UPDATE tbl_deliveryaddress SET DE_default = '0' WHERE DE_UserID=".$user_id;
mysqli_query($con,$sql);


if($user_id != '' && $add_id != '' && $default != '')
{
	$sql="UPDATE tbl_deliveryaddress SET DE_default = '".$default."' WHERE DE_ID=".$add_id;
	mysqli_query($con,$sql);
	$output=array("status"=>"1","data"=>"Default Address Added");
	echo json_encode($output);exit;
}
else 
{
	$output=array("status"=>"0","data"=>"Please enter correct Data");
	echo json_encode($output);exit;
}
?>
