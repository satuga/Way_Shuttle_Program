<?php
include('config.php');
include('function.php');
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), TRUE);

$user_id = sanitize($data['data']['user_id']);
$name = sanitize($data['data']['user_name']);
$address = sanitize($data['data']['address']);
$building = sanitize($data['data']['building']);
$state = sanitize($data['data']['state']);
$city = sanitize($data['data']['city']);
$zipcode = sanitize($data['data']['zipcode']);
$phone = sanitize($data['data']['phone']);
$add_id = sanitize($data['data']['address_id']);
$type = sanitize($data['data']['type']);

$lat = sanitize($data['data']['lat']);
$long = sanitize($data['data']['long']);
$default = '0';

/*if($default == '1')
{
	$sql="UPDATE tbl_deliveryaddress SET DE_default = '0' WHERE DE_UserID=".$user_id;
	mysqli_query($con,$sql);
}*/

if($user_id != '')
{
	if($type == 'add')
	{
		$sql="INSERT INTO tbl_deliveryaddress(`DE_UserID`,`DE_Name`,`DE_Address`,`BLDG_No`,`DE_State`,`DE_City`,`DE_Zipcode`,`DE_Phone`,`DE_lat`,`DE_long`,`DE_default`,`DE_Created`) VALUES('".$user_id."','".$name."','".$address."','".$building."','".$state."','".$city."','".$zipcode."','".$phone."','".$lat."','".$long."','".$default."',Now())";

		$exeCC=mysqli_query($con,$sql);
		$last_id=mysqli_insert_id($con);
		if($last_id != '')
		{
			$output=array("status"=>"1","data"=>"Address Added Successfully","new_id"=>"".$last_id."");
			echo json_encode($output);
			exit;
		}
		else
		{
			$output=array("status"=>"0","data"=>"Address Not Added");
			echo json_encode($output);exit;
		}
	}
	else
	{
		$sql="UPDATE tbl_deliveryaddress SET DE_Name='".$name."',DE_Address='".$address."',BLDG_No='".mysqli_real_escape_string($con,$building)."',DE_State='".mysqli_real_escape_string($con,$state)."',DE_City='".mysqli_real_escape_string($con,$city)."',DE_Zipcode='".mysqli_real_escape_string($con,$zipcode)."',DE_Phone='".mysqli_real_escape_string($con,$phone)."',DE_lat = '".$lat."',DE_long = '".$long."',DE_default = '".$default."' WHERE DE_ID=".$add_id;
		mysqli_query($con,$sql);
		$output=array("status"=>"1","data"=>"Address Updated Successfully");
		echo json_encode($output);exit;

	}
}
else
{
	$output=array("status"=>"0","data"=>"Please enter correct Data");
	echo json_encode($output);exit;
}
?>
